<?php
namespace Core\Engine\Payment\Gateway\Adapter\PayPal;

class ExpressCheckout extends \Core\Engine\Payment\Gateway\Adapter\PayPal  {
    const METHOD_SET = 'SetExpressCheckout';
    const METHOD_GET = 'GetExpressCheckoutDetails';
    const METHOD_DO = 'DoExpressCheckoutPayment';
    
    /**
     * @var string
     */
    protected $paypalUrl;
    
    public function getName() {
        return 'expresscheckout';
    }
    
    /**
     * @param \Core\Engine\Payment\Transaction $transaction
     * @param array $processOptions
     * @return \Core\Engine\Payment\Gateway\Response
     */
    public function process(\Core\Engine\Payment\Transaction $transaction, array $processOptions = array()) {
        throw new \Exception('PAYPAL\EXPRESSCHECKOUT USES DEPRECATED METHODS DO NOT USE!');

        $method = $transaction->getVendorOption($this->getVendor(), 'method', self::METHOD_SET);
        
        switch($method) {
            case self::METHOD_SET:
                return $this->_setExpressCheckout($transaction, $processOptions);
                break;
            case self::METHOD_DO:
                return $this->_doExpressCheckout($transaction, $processOptions);
                break;
            default:
                throw new \Core\Engine\Payment\Gateway\Exception("Invalid PayPal Method '{$method}'");
        }
    }
    
    public function loadTransaction($token) {
        $parameters = array(
            'TOKEN' => $token,
        );
        
        $results = $this->_executePaypalAction(self::METHOD_GET, $parameters);
        
        if( strtolower($results['ACK']) == 'failure' ) {
            $i = 0;
            $exception = null;
            while( isset($results["L_ERRORCODE{$i}"]) ) {
                $exception = new \Core\Engine\Payment\Gateway\Exception(
                    sprintf(
                        "[%s][%s] %s",
                        $results["L_ERRORCODE{$i}"],
                        $results["L_SHORTMESSAGE{$i}"], 
                        $results["L_LONGMESSAGE{$i}"]
                    ),
                    $results["L_ERRORCODE{$i}"],
                    $exception //PREVIOUS
                );
            }
            
            throw $exception;
        }
        
        if(isset($results["L_AMT0"])) {
            $transaction = new \Core\Engine\Payment\Transaction\Cart();
        } else {
            $transaction = new \Core\Engine\Payment\Transaction();
        }
        
        $transaction->setVendorOptions(array(
            $this->getVendor() => array(
                'token' => $results['TOKEN'],
                'payerId' => $results['PAYERID'],
                'method' => self::METHOD_DO,
                '_raw' => $results,
            ),
        ));
        
        $i = 0;
        while(isset($results["L_AMT{$i}"]) ) {
            
            $transaction->addItem(new \Core\Engine\Payment\Transaction\Item(array(
                'uid' => (string)$results["L_NUMBER{$i}"],
                'amount' => (double)$results["L_AMT{$i}"],
            )), (int)$results["L_QTY{$i}"]);
            
            $i++;
        }
        
        return $transaction;
    }
    
    protected function _setExpressCheckout(\Core\Engine\Payment\Transaction $transaction, array $processOptions = array()) {
        $paramters = $this->_transactionToParameters($transaction);
        
        if( !$transaction->hasVendorOption($this->getVendor(), 'returnurl') )
            throw new \Core\Engine\Payment\Gateway\Exception($this->getVendor() . "::" . $this->getName() . "::" . self::METHOD_SET . " requires the vendor option RETURNURL");
        $parameters['RETURNURL'] = $transaction->getVendorOption($this->getVendor(), 'returnurl');
        
        if( !$transaction->hasVendorOption($this->getVendor(), 'cancelurl') )
            throw new \Core\Engine\Payment\Gateway\Exception($this->getVendor() . "::" . $this->getName() . "::" . self::METHOD_SET . " requires the vendor option CANCELURL");
        $parameters['CANCELURL'] = $transaction->getVendorOption($this->getVendor(), 'returnurl');
        
        $results = $this->_executePaypalAction(self::METHOD_SET, $parameters);
        
        if( strtolower($results['ACK']) == 'failure' ) {
            
            $i = 0;
            $errors = array();
            while( isset($results["L_ERRORCODE{$i}"]) ) {
                $errors[] = array(
                    'code' => $results["L_ERRORCODE{$i}"],
                    'message' => sprintf(
                        "[%s] %s",
                        $results["L_SHORTMESSAGE{$i}"], 
                        $results["L_LONGMESSAGE{$i}"]
                    ),
                );
                $i++;
            }
            
            $response = new \Core\Engine\Payment\Gateway\Response(array(
                'success' => false,
                'errors' => $errors,
                'vendorMetadata' => $results,
            ));
            
            return $response;
        }
        
        $response = new \Core\Engine\Payment\Gateway\Response(array(
            'success' => true,
            'redirect' => $this->getPaypalUrl() . urlencode($results['TOKEN']),
            'vendorMetadata' => $results,
        ));
        
        return $response;
    }
    
    protected function _doExpressCheckout(\Core\Engine\Payment\Transaction $transaction, array $processOptions = array()) {
        $parameters = $this->_transactionToParameters($transaction);
        
        $parameters['TOKEN'] = $transaction->getVendorOption('paypal', 'token');
        $parameters['PAYERID'] = $transaction->getVendorOption('paypal', 'payerId');
        
        $parameters['PAYMENTACTION'] = 'Sale';
        
        if( !$transaction->hasVendorOption($this->getVendor(), 'returnurl') )
            throw new \Core\Engine\Payment\Gateway\Exception($this->getVendor() . "::" . $this->getName() . "::" . self::METHOD_DO . " requires the vendor option RETURNURL");
        $parameters['RETURNURL'] = $transaction->getVendorOption($this->getVendor(), 'returnurl');
        
        if( !$transaction->hasVendorOption($this->getVendor(), 'cancelurl') )
            throw new \Core\Engine\Payment\Gateway\Exception($this->getVendor() . "::" . $this->getName() . "::" . self::METHOD_DO . " requires the vendor option CANCELURL");
        $parameters['CANCELURL'] = $transaction->getVendorOption($this->getVendor(), 'returnurl');
        
        $results = $this->_executePaypalAction(self::METHOD_DO, $parameters);
        
        if( strtolower($results['ACK']) == 'failure' ) {
            
            $i = 0;
            $errors = array();
            while( isset($results["L_ERRORCODE{$i}"]) ) {
                $errors[] = array(
                    'code' => $results["L_ERRORCODE{$i}"],
                    'message' => sprintf(
                        "[%s] %s",
                        $results["L_SHORTMESSAGE{$i}"], 
                        $results["L_LONGMESSAGE{$i}"]
                    ),
                );
                $i++;
            }
            
            $response = new \Core\Engine\Payment\Gateway\Response(array(
                'success' => false,
                'errors' => $errors,
                'vendorMetadata' => $results,
            ));
            
            return $response;
        }
        
        $response = new \Core\Engine\Payment\Gateway\Response(array(
            'success' => true,
            'transactionId' => $results['TRANSACTIONID'],
            'vendorMetadata' => $results,
        ));
        
        return $response;
    }
    
    protected function _transactionToParameters(\Core\Engine\Payment\Transaction $transaction) {
        $parameters = array();
        
        $parameters['ITEMAMT'] = $transaction->getSubtotal();
        $parameters['SHIPPINGAMT'] = 0.0; //STUB
        $parameters['HANDLINGAMT'] = 0.0; //STUB
        $parameters['TAXAMT'] = 0.0; //STUB
        $parameters['INSURANCEAMT'] = 0.0; //STUB
        $parameters['SHIPDISCAMT'] = 0.0; //STUB
        $parameters['AMT'] = $parameters['ITEMAMT'] 
                           + $parameters['SHIPPINGAMT'] 
                           + $parameters['HANDLINGAMT'] 
                           + $parameters['TAXAMT'] 
                           + $parameters['INSURANCEAMT'] 
                           + $parameters['SHIPDISCAMT'];
        
        if( $transaction instanceOf \Core\Engine\Payment\Transaction\Cart ) {
            $i = 0;
            foreach( $transaction->getItems() as $item ) {
                $parameters["L_AMT{$i}"] = $item['item']->getPaymentTransactionAmount();
                $parameters["L_NUMBER{$i}"] = $item['item']->getPaymentTransactionUid();
                $parameters["L_QTY{$i}"] = $item['qty'];
                $i++;
            }
        }
        
        return $parameters;
    }
    
    /**
     * @return string
     */
    public function getPaypalUrl() {
        if( $this->paypalUrl )
            return $this->paypalUrl;
        elseif( $this->getSandbox() )
            return "https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=";
        else
            return "https://www.paypal.com/webscr&cmd=_express-checkout&token=";
    }
}