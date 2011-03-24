<?php
namespace Core\Engine\Payment\Gateway;

abstract class AdapterAbstract {
    /**
     * 
     * @param array $options
     */
    public function __construct(array $options = array()) {
        \Core\Engine\Payment\Util::universalConstructor($this, $options);
    }
    
    /**
     * @return string
     */
    abstract public function getVendor();
    
    /**
     * @return string
     */
    abstract public function getName();
    
    /**
     * @param \Core\Engine\Payment\Transaction $transaction
     * @param array $processOptions
     * @return \Core\Engine\Payment\Gateway\Response
     */
    abstract public function process(\Core\Engine\Payment\Transaction $transaction, array $processOptions = array());
}