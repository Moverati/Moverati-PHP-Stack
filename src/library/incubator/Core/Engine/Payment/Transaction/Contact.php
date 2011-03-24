<?php
namespace Core\Engine\Payment\Transaction;

use \Core\Engine\Payment;

class Contact
{
    /**
     *
     * @var string
     */
    protected $firstName;

    /**
     *
     * @var string
     */
    protected $lastName;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $phone;

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
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     *
     * @param string $firstName
     * @return Contact
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     *
     * @param string $lastName
     * @return Contact
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     *
     * @param string $email
     * @return Contact
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     *
     * @param string $phone
     * @return Contact
     */
    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }
}