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

    /**
     * reference types in the description
     */
    const EREF = 'eref';
    const KREF = 'kref';
    const MREF = 'mref';
    const CRED = 'cred';
    const SVWZ = 'svwz';

    const REFERENCE_TYPES = array(
        self::EREF,
        self::KREF,
        self::MREF,
        self::CRED,
        self::SVWZ
    );

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
     * end-to-end-id
     *
     * @var string
     */
    protected $eref;

    /**
     * customer reference number
     *
     * @var string
     */
    protected $kref;

    /**
     * mandate reference number
     *
     * @var string
     */
    protected $mref;

    /**
     * creditor identification number
     *
     * @var string
     */
    protected $cred;

    /**
     * payment reference
     *
     * @var string
     */
    protected $svwz;

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
     * Get eref
     *
     * @return string
     */
    public function getEref()
    {
        return $this->eref;
    }

    /**
     * Set eref
     *
     * @param $eref
     * @return $this
     */
    public function setEref($eref)
    {
        $this->eref = $eref;
        return $this;
    }

    /**
     * Get kref
     *
     * @return string
     */
    public function getKref()
    {
        return $this->kref;
    }

    /**
     * Set kref
     *
     * @param $kref
     * @return $this
     */
    public function setKref($kref)
    {
        $this->kref = $kref;
        return $this;
    }

    /**
     * Get mref
     *
     * @return string
     */
    public function getMref()
    {
        return $this->mref;
    }

    /**
     * Set mref
     *
     * @param $mref
     * @return $this
     */
    public function setMref($mref)
    {
        $this->mref = $mref;
        return $this;
    }

    /**
     * Get cred
     *
     * @return string
     */
    public function getCred()
    {
        return $this->cred;
    }

    /**
     * Set cred
     *
     * @param $cred
     * @return $this
     */
    public function setCred($cred)
    {
        $this->cred = $cred;
        return $this;
    }

    /**
     * Get svwz
     *
     * @return string
     */
    public function getSvwz()
    {
        return $this->svwz;
    }

    /**
     * Set svwz
     *
     * @param $svwz
     * @return $this
     */
    public function setSvwz($svwz)
    {
        $this->svwz = $svwz;
        return $this;
    }

    /**
     * Splits the description and set the reference properties of the transaction
     *
     * @param $description
     * @param string $pattern
     */
    public function setReferencePropertiesByDescription($description = null, $pattern =  '/([A-Z]{4})+/') {

        if(!$description) {
            $description = sprintf('%s%s', $this->getDescription1(), $this->getDescription2());
        }

        $descriptionArray = preg_split($pattern, $description, NULL, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach(Transaction::REFERENCE_TYPES as $REFERENCE_TYPE) {
            $referenceTypeArrayIndex = array_search(strtoupper($REFERENCE_TYPE), $descriptionArray);
            if(false !== $referenceTypeArrayIndex) {
                $valueIndex = $referenceTypeArrayIndex + 1;

                if(array_key_exists($valueIndex, $descriptionArray)) {
                    // deletes the plus at the beginning of the reference value
                    $referenceValue = substr($descriptionArray[$valueIndex], 1);
                    $validValue = true;
                    // checks if the value at the next index is valid
                    foreach(Transaction::REFERENCE_TYPES as $CHECK_REFERENCE_TYPE) {
                        if(false !== strpos($referenceValue, $CHECK_REFERENCE_TYPE)) {
                            $validValue&=false;
                        }
                    }

                    // if the value is valid checks if the setter exists and sets the value to the object
                    if($validValue) {
                        $reflClass = new \ReflectionClass($this);
                        $methodName = sprintf('set%s', strtoupper($REFERENCE_TYPE));
                        if ($reflClass->hasMethod($methodName)) {
                            $reflMethod = $reflClass->getMethod($methodName);

                            if ($reflMethod->isPublic()) {
                                $reflMethod->invoke($this, $referenceValue);
                            }
                        }
                    }
                }
            }
        }

        return $this;
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
    
    public function __toString()
    {
        $dateFormat = 'd.m.Y';
        
        $properties = array(
            $this->getName(),
            $this->getAccountNumber(),
            $this->getBankCode(),
            $this->getAmount(),
            $this->getCreditDebit(),
            $this->getBookingText(),
            $this->getDescription1(),
            $this->getDescription2(),
            $this->getBookingDate() ? $this->getBookingDate()->format($dateFormat) : null,
            $this->getValutaDate() ? $this->getValutaDate()->format($dateFormat) : null
        );

        return implode(',', $properties);
    }
}
