<?php
namespace Core\Engine\Payment\Transaction;

use \Core\Engine\Payment;

class Address
{
    /**
     *
     * @var string
     */
    protected $street;

    /**
     *
     * @var string
     */
    protected $street2;

    /**
     *
     * @var string
     */
    protected $city;

    /**
     *
     * @var string
     */
    protected $state;

    /**
     *
     * @var string
     */
    protected $country;

    /**
     *
     * @var string
     */
    protected $zip;

    /**
     *
     * @param array $options
     */
    public function __construct(array $options = array()) {
        Payment\Util::universalConstructor($this, $options);
    }

    /**
     *
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     *
     * @param string $street
     * @return Address
     */
    public function setStreet($street) {
        $this->street = $street;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getStreet2() {
        return $this->street2;
    }

    /**
     *
     * @param string $street2
     * @return Address
     */
    public function setStreet2($street2) {
        $this->street2 = $street2;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     *
     * @param string $state
     * @return Address
     */
    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     *
     * @param string $country
     * @return Address
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     *
     * @param string $zip
     * @return Address
     */
    public function setZip($zip) {
        $this->zip = $zip;
        return $this;
    }


}