<?php

namespace Fhp\Model\StatementOfAccount;

/**
 * Class Transaction
 * @package Fhp\Model\StatementOfAccount
 */
class Transaction
{
    const CD_CREDIT = 'credit';
    const CD_DEBIT = 'debit';
    const CD_CREDIT_CANCELLATION = 'credit_cancellation';
    const CD_DEBIT_CANCELLATION = 'debit_cancellation';

    /**
     * @var \DateTime|null
     */
    protected $bookingDate;

    /**
     * @var \DateTime|null
     */
    protected $valutaDate;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $creditDebit;

    /**
     * @var string
     */
    protected $bookingText;

    /**
     * @var string
     */
    protected $description1;

    /**
     * @var string
     */
    protected $description2;

    /**
     * Array keys are identifiers like "SVWZ" for the main description.
     * @var string[]
     */
    protected $structuredDescription;

    /**
     * @var string
     */
    protected $bankCode;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var string
     */
    protected $name;

    /**
     * See https://www.bayernlb.de/internet/media/de/ir/downloads_1/zahlungsverkehr/formate_1/MT940_942.pdf page 451 / 8.2.6 Geschäftsvorfallcodes
     * @var string
     */
    protected $transactionCode;

    /**
     * Get booking date.
     *
     * @deprecated Use getBookingDate() instead
     * @codeCoverageIgnore
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->getBookingDate();
    }

    /**
     * Get booking date
     *
     * @return \DateTime|null
     */
    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    /**
     * Get date
     *
     * @return \DateTime|null
     */
    public function getValutaDate()
    {
        return $this->valutaDate;
    }

    /**
     * Set booking date
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setBookingDate(\DateTime $date = null)
    {
        $this->bookingDate = $date;

        return $this;
    }

    /**
     * Set valuta date
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setValutaDate(\DateTime $date = null)
    {
        $this->valutaDate = $date;

        return $this;
    }

    /**
     * Get the signed amount based on credit/debit setting.
     * Debits and canceled credits have a negative sign.
     * @return float
     */
    public function getSignedAmount()
    {
        switch ($this->creditDebit) {
            case Transaction::CD_DEBIT:
            case Transaction::CD_CREDIT_CANCELLATION:
                return -1 * $this->amount;
            default:
                return $this->amount;
        }
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = (float) $amount;

        return $this;
    }

    /**
     * Get creditDebit
     *
     * @return string
     */
    public function getCreditDebit()
    {
        return $this->creditDebit;
    }

    /**
     * Set creditDebit
     *
     * @param string $creditDebit
     *
     * @return $this
     */
    public function setCreditDebit($creditDebit)
    {
        $this->creditDebit = $creditDebit;

        return $this;
    }

    /**
     * Get bookingText
     *
     * @return string
     */
    public function getBookingText()
    {
        return $this->bookingText;
    }

    /**
     * Set bookingText
     *
     * @param string $bookingText
     *
     * @return $this
     */
    public function setBookingText($bookingText)
    {
        $this->bookingText = (string) $bookingText;

        return $this;
    }

    /**
     * Get description1
     *
     * @return string
     */
    public function getDescription1()
    {
        return $this->description1;
    }

    /**
     * Set description1
     *
     * @param string $description1
     *
     * @return $this
     */
    public function setDescription1($description1)
    {
        $this->description1 = (string) $description1;

        return $this;
    }

    /**
     * Get description2
     *
     * @return string
     */
    public function getDescription2()
    {
        return $this->description2;
    }

    /**
     * Set description2
     *
     * @param string $description2
     *
     * @return $this
     */
    public function setDescription2($description2)
    {
        $this->description2 = (string) $description2;

        return $this;
    }

    /**
     * Get structuredDescription
     *
     * @return string[]
     */
    public function getStructuredDescription()
    {
        return $this->structuredDescription;
    }

    /**
     * Set structuredDescription
     *
     * @param string[] $structuredDescription
     *
     * @return $this
     */
    public function setStructuredDescription($structuredDescription)
    {
        $this->structuredDescription = $structuredDescription;

        return $this;
    }

    /**
     * Get the main description (SVWZ)
     *
     * @return string
     */
    public function getMainDescription()
    {
        if (array_key_exists('SVWZ', $this->structuredDescription)) {
            return $this->structuredDescription['SVWZ'];
        } else {
            return '';
        }
    }

    /**
     * Get bankCode
     *
     * @return string
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * Set bankCode
     *
     * @param string $bankCode
     *
     * @return $this
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = (string) $bankCode;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set accountNumber
     *
     * @param string $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = (string) $accountNumber;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Get see https://www.bayernlb.de/internet/media/de/ir/downloads_1/zahlungsverkehr/formate_1/MT940_942.pdf page 451 / 8.2.6 Geschäftsvorfallcodes
     *
     * @return  string
     */
    public function getTransactionCode()
    {
        return $this->transactionCode;
    }

    /**
     * Set see https://www.bayernlb.de/internet/media/de/ir/downloads_1/zahlungsverkehr/formate_1/MT940_942.pdf page 451 / 8.2.6 Geschäftsvorfallcodes
     *
     * @param  string  $transactionCode  See https://www.bayernlb.de/internet/media/de/ir/downloads_1/zahlungsverkehr/formate_1/MT940_942.pdf page 451 / 8.2.6 Geschäftsvorfallcodes
     *
     * @return  self
     */
    public function setTransactionCode($transactionCode)
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }
}
