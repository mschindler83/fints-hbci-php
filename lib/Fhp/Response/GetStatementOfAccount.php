<?php

namespace Fhp\Response;

use Fhp\Model\StatementOfAccount\Statement;
use Fhp\Model\StatementOfAccount\StatementOfAccount;
use Fhp\Model\StatementOfAccount\Transaction;
use Fhp\Parser\MT940;

/**
 * Class GetStatementOfAccount
 * @package Fhp\Response
 */
class GetStatementOfAccount extends Response
{
    const SEG_ACCOUNT_INFORMATION = 'HIKAZ';

    /**
     * @return StatementOfAccount|null
     * @throws \Fhp\Parser\Exception\MT940Exception
     */
    public function getStatementOfAccount()
    {
        return static::createModelFromArray(
            $this->getStatementOfAccountArray()
        );
    }

    /**
     * @return array
     * @throws \Fhp\Parser\Exception\MT940Exception
     */
    public function getStatementOfAccountArray()
    {
        $data = array();
        $seg = $this->findSegment(static::SEG_ACCOUNT_INFORMATION);
        if (is_string($seg)) {
            if (preg_match('/@(\d+)@(.+)/ms', $seg, $m)) {
                $parser = new MT940($m[2]);
                $data = $parser->parse(MT940::TARGET_ARRAY);
            }
        }

        return $data;
    }

    /**
     * Creates a StatementOfAccount model from array.
     *
     * @param array $array
     * @return StatementOfAccount|null
     */
    public static function createModelFromArray(array $array)
    {
        
        if (empty($array)) {
            return null;
        }


        $soa = new StatementOfAccount();

        foreach ($array as $date => $statement) {
            $statementModel = new Statement();
            $statementModel->setDate($date ? new \DateTime($date) : null);
            $statementModel->setStartBalance((float) $statement['start_balance']['amount']);
            $statementModel->setCreditDebit($statement['start_balance']['credit_debit']);

            if (isset($statement['transactions'])) {
                foreach ($statement['transactions'] as $trx) {
                    $transaction = new Transaction();
                    $transaction
                        ->setBookingDate($trx['booking_date'] ? new \DateTime($trx['booking_date']) : null)
                        ->setValutaDate($trx['valuta_date'] ? new \DateTime($trx['valuta_date']) : null)
                        ->setCreditDebit($trx['credit_debit'])
                        ->setAmount($trx['amount'])
                        ->setBookingText($trx['description']['booking_text'])
                        ->setDescription1($trx['description']['description_1'])
                        ->setDescription2($trx['description']['description_2'])
                        ->setReferencePropertiesByDescription(sprintf('%s%s', $trx['description']['description_1'], $trx['description']['description_2']))
                        ->setBankCode($trx['description']['bank_code'])
                        ->setAccountNumber($trx['description']['account_number'])
                        ->setName($trx['description']['name']);

                    $statementModel->addTransaction($transaction);
                }
            }
            $soa->addStatement($statementModel);
        }

        return $soa;
    }
}
