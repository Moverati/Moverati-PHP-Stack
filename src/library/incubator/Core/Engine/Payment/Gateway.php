<?php
namespace Core\Engine\Payment;

class Gateway {
    /**
     * @var \Core\Engine\Payment\Gateway\Adapterabstract $adapter
     */
    protected $adapter;
    
    /**
     *
     * @param array $options
     */
    public function __construct(array $options = array()) {
        \Core\Engine\Payment\Util::universalConstructor($this, $options);
    }

    /**
     *
     * @param \Core\Engine\Payment\Transaction $transaction
     * @param array $processOptions
     * @return \Core\Engine\Payment\Gateway\Response
     */
    public function process(\Core\Engine\Payment\Transaction $transaction, array $processOptions = array()) {
        $response = $this->getAdapter()->process($transaction, $processOptions);
        
        return $response;
    }
    
    /**
     *
     * @param \Core\Engine\Payment\Gateway\Adapterabstract $adapter
     */
    public function setAdapter(\Core\Engine\Payment\Gateway\Adapterabstract $adapter) {
        $this->adapter = $adapter;
    }
    
    /**
     *
     * @return \Core\Engine\Payment\Gateway\Adapterabstract
     */
    public function getAdapter() {
        return $this->adapter;
    }
}