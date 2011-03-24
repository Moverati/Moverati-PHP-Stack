<?php
namespace Core\Engine\Payment\Transaction\PaymentMethod;

use \Core\Engine\Payment;

class CreditCard extends Payment\Transaction\PaymentMethod
{
    const TYPE_VISA       = 'visa';
    const TYPE_MASTERCARD = 'mastercard';
    const TYPE_DISCOVER   = 'discover';
    const TYPE_AMEX       = 'amex';

    protected $_acceptedTypes = array(
        self::TYPE_VISA, self::TYPE_MASTERCARD, self::TYPE_DISCOVER, self::TYPE_AMEX,
    );

    /**
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var string
     */
    protected $number;

    /**
     *
     * @var array
     */
    protected $expirationDate;

    /**
     *
     * @var string
     */
    protected $securityCode;

    public function __construct(array $options = array()) {
        \Core\Engine\Payment\Util::universalConstructor($this, $options);
    }

    /**
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     *
     * @param string $type
     * @return CreditCard
     */
    public function setType($type) {
        if( !in_array($type, $this->_acceptedTypes) ) {
            throw new \Core\Engine\Payment\Exception("Invalid Credit Card Type");
        }

        $this->type = $type;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     *
     * @param string $number
     * @return CreditCard
     */
    public function setNumber($number) {
        $this->number = $number;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getExpirationDate($part = null) {
        if( $part === null )
            return $this->expirationDate;
        elseif( isset($this->expirationDate[$part]) )
            return $this->expirationDate[$part];
        else
            throw new Payment\Exception("Invalid Expiration Date Part");
    }

    /**
     *
     * @param mixed $expirationDate
     * @return CreditCard
     */
    public function setExpirationDate($expirationDate) {
        if( is_string($expirationDate) ) {
            if( strlen($expirationDate) == 6 ) {
                $expirationTimestamp = \DateTime::createFromFormat ('mY', $expirationDate)->getTimestamp();
            }
            else {
                $expirationDate = new \DateTime($expirationDate);
                $expirationTimestamp = $expirationDate->getTimestamp();
            }

            $expirationDate = array(
                'month' => date('m', $expirationTimestamp),
                'year' => date('Y', $expirationTimestamp),
            );
        } elseif ($expirationDate instanceOf \DateTime) {
            $expirationTimestamp = $expirationDate->getTimestamp();
            
            $expirationDate = array(
                'month' => date('m', $expirationTimestamp),
                'year' => date('Y', $expirationTimestamp),
            );
        } elseif (is_array($expirationDate)) {
            if(!isset($expirationDate['month']) || !isset($expirationDate['year'])) {
                throw new Payment\Exception("Expiration Date array expects 'month' and 'year' keys");

                $expirationDate = array(
                    'month' => (int)$expirationDate['month'],
                    'year' => (int)$expirationDate['year'],
                );
            }
        }

        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSecurityCode() {
        return $this->securityCode;
    }

    /**
     *
     * @param string $securityCode
     * @return CreditCard
     */
    public function setSecurityCode($securityCode) {
        $this->securityCode = $securityCode;
        return $this;
    }
}