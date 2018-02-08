<?php

namespace Fhp\Parser;

use Fhp\Parser\Exception\MT940Exception;

/**
 * Class MT940
 * @package Fhp\Parser
 */
class MT940
{
    const TARGET_ARRAY = 0;

    const CD_CREDIT = 'credit';
    const CD_DEBIT = 'debit';
    const CD_CREDIT_CANCELLATION = 'credit_cancellation';
    const CD_DEBIT_CANCELLATION = 'debit_cancellation';

    /** @var string */
    protected $rawData;
    /** @var string */
    protected $soaDate;

    /**
     * MT940 constructor.
     *
     * @param string $rawData
     */
    public function __construct($rawData)
    {
        $this->rawData = (string) $rawData;
    }

    /**
     * @param string $target
     * @return array
     * @throws MT940Exception
     */
    public function parse($target)
    {
        switch ($target) {
        case static::TARGET_ARRAY:
            return $this->parseToArray();
            break;
        default:
            throw new MT940Exception('Invalid parse type provided');
        }
    }

    /**
     * @return array
     * @throws MT940Exception
     */
    protected function parseToArray()
    {
        // The divider can be either \r\n or @@

        $divider = "(@@|\r\n)";
        $result = array();
        $statementBlocks = preg_split('/' . $divider . '-' . $divider . '/', $this->rawData);

        foreach ($statementBlocks as $statementBlock) {
            $parts = preg_split('/' . $divider . ':/', $statementBlock);
            $statement = array();
            $transactions = array();
            $cnt = 0;
            for ($i = 0, $cnt = count($parts); $i < $cnt; $i++) {
                // handle start balance
                // 60F:C160401EUR1234,56
                if (preg_match('/^60(F|M):/', $parts[$i])) {
                    // remove 60(F|M): for better parsing
                    $parts[$i] = substr($parts[$i], 4);
                    $this->soaDate = $this->getDate(substr($parts[$i], 1, 6));

                    $amount = str_replace(',', '.', substr($parts[$i], 10));
                    $statement['start_balance'] = array(
                        'amount' => $amount,
                        'credit_debit' => (substr($parts[$i], 0, 1) == 'C') ? static::CD_CREDIT : static::CD_DEBIT
                    );
                    $statement['date'] = $this->soaDate;
                } elseif (
                    // found transaction
                    // trx:61:1603310331DR637,39N033NONREF
                    0 === strpos($parts[$i], '61:')
                    && isset($parts[$i + 1])
                    && 0 === strpos($parts[$i + 1], '86:')
                ) {
                    $transaction = substr($parts[$i], 3);
                    $description = substr($parts[$i + 1], 3);

                    $currentTrx = array();

                    preg_match('/^\d{6}(\d{4})?(C|D|RC|RD)[A-Z]?([^N]+)N/', $transaction, $matches);

                    switch ($matches[2]) {
                    case 'C':
                        $currentTrx['credit_debit'] = static::CD_CREDIT;
                        break;
                    case 'D':
                        $currentTrx['credit_debit'] = static::CD_DEBIT;
                        break;
                    case 'RC':
                        $currentTrx['credit_debit'] = static::CD_CREDIT_CANCELLATION;
                        break;
                    case 'RD':
                        $currentTrx['credit_debit'] = static::CD_DEBIT_CANCELLATION;
                        break;
                    default:
                        throw new MT940Exception('c/d/rc/rd mark not found in: ' . $transaction);
                    }

                    $amount = $matches[3];
                    $amount = str_replace(',', '.', $amount);
                    $currentTrx['amount'] = floatval($amount);

                    $currentTrx['transaction_code'] = substr($description, 0, 3);

                    $description = $this->parseDescription($description);
                    $currentTrx['description'] = $description;

                    // :61:1605110509D198,02NMSCNONREF
                    // 16 = year
                    // 0511 = valuta date
                    // 0509 = booking date
                    $year = substr($transaction, 0, 2);
                    $valutaDate = $this->getDate($year . substr($transaction, 2, 4));

                    $bookingDate = substr($transaction, 6, 4);
                    if (preg_match('/^\d{4}$/', $bookingDate)) {
                        // if valuta date is earlier than booking date, then it must be in the new year.
                        if (substr($transaction, 2, 2) == '12' && substr($transaction, 6, 2) == '01') {
                            $year++;
                        } elseif (substr($transaction, 2, 2) == '01' && substr($transaction, 6, 2) == '12') {
                            $year--;
                        }
                        $bookingDate = $this->getDate($year . $bookingDate);
                    } else {
                        // if booking date not set in :61, then we have to take it from :60F
                        $bookingDate = $this->soaDate;
                    }

                    $currentTrx['booking_date'] = $bookingDate;
                    $currentTrx['valuta_date'] = $valutaDate;

                    $transactions[] = $currentTrx;
                }
            }
            $statement['transactions'] = $transactions;
            if (count($transactions) > 0) {
                $result[] = $statement;
            }
        }

        return $result;
    }

    /**
     * @param string $descr
     * @return array
     */
    protected function parseDescription($descr)
    {
        $prepared = array();
        $result = array();

        // prefill with empty values
        for ($i = 0; $i <= 63; $i++) {
            $prepared[$i] = null;
        }

        $descr = str_replace("\r\n", '', $descr);
        $descr = str_replace('? ', '?', $descr);
        preg_match_all('/\?[\r\n]*(\d{2})([^\?]+)/', $descr, $matches, PREG_SET_ORDER);

        $descriptionLines = array();
        $description1 = ''; // Legacy, could be removed.
        $description2 = ''; // Legacy, could be removed.
        foreach ($matches as $m) {
            $index = (int) $m[1];
            if ((20 <= $index && $index <= 29) || (60 <= $index && $index <= 63)) {
                if (20 <= $index && $index <= 29) {
                    $description1 .= $m[2];
                } else {
                    $description2 .= $m[2];
                }
                $m[2] = trim($m[2]);
                if (!empty($m[2])) {
                    $descriptionLines[] = $m[2];
                }
            } else {
                $prepared[$index] = $m[2];
            }
        }

        $description = array();
        if (empty($descriptionLines) || strlen($descriptionLines[0]) < 5 || $descriptionLines[0][4] !== '+') {
            $description['SVWZ'] = implode('', $descriptionLines);
        } else {
            $lastType = null;
            foreach ($descriptionLines as $line) {
                if (strlen($line) > 5 && $line[4] === '+') {
                    if ($lastType != null) {
                        $description[$lastType] = trim($description[$lastType]);
                    }
                    $lastType = substr($line, 0, 4);
                    $description[$lastType] = substr($line, 5);
                } else {
                    $description[$lastType] .= $line;
                }
                if (strlen($line) < 27) {
                    // Usually, lines are 27 characters long. In case characters are missing, then it's either the end
                    // of the current type or spaces have been trimmed from the end. We want to collapse multiple spaces
                    // into one and we don't want to leave trailing spaces behind. So add a single space here to make up
                    // for possibly missing spaces, and if it's the end of the type, it will be trimmed off later.
                    $description[$lastType] .= ' ';
                }
            }
            $description[$lastType] = trim($description[$lastType]);
        }

        $result['description']       = $description;
        $result['booking_text']      = trim($prepared[0]);
        $result['primanoten_nr']     = trim($prepared[10]);
        $result['description_1']     = trim($description1);
        $result['bank_code']         = trim($prepared[30]);
        $result['account_number']    = trim($prepared[31]);
        $result['name']              = trim($prepared[32] . $prepared[33]);
        $result['text_key_addition'] = trim($prepared[34]);
        $result['description_2']     = trim($description2);
        return $result;
    }

    /**
     * @param string $val
     * @return string
     */
    protected function getDate($val)
    {
        $val = '20' . $val;
        preg_match('/(\d{4})(\d{2})(\d{2})/', $val, $m);
        return $m[1] . '-' . $m[2] . '-' . $m[3];
    }
}
