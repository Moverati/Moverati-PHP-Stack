<?php
namespace Core\Engine\Payment\Transaction\Cart;

class Item implements ItemInterface {
    /**
     * @var string
     */
    protected $uid;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var double
     */
    protected $amount;
    
    /**
     * @param array
     */
    public function __construct(array $options = array()) {
        \Core\Engine\Payment\Util::universalConstructor($this, $options);
    }
    
    /**
     * @param string $uid
     */
    public function setUid($uid) {
        $this->uid = (string)$uid;
    }
    
    /**
     * @return string
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @param double $amount
     */
    public function setAmount($amount) {
        $this->amount = (double)$amount;
    }
    
    /**
     * @return double
     */
    public function getAmount() {
        return $this->amount;
    }
    
    
    public function getPaymentTransactionUid() {
        return $this->getUid();
    }
    
    public function getPaymentTransactionName() {
        return $this->getName();
    }
    
    public function getPaymentTransactionAmount() {
        return $this->getAmount();
    }
}