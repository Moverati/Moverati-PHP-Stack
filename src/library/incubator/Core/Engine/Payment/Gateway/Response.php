<?php
namespace Core\Engine\Payment\Gateway;

class Response {
    protected $success = false;
    protected $transactionId;
    protected $errors = array();
    protected $redirect;
    protected $vendorMetadata = array();
    
    public function __construct(array $options = array()) {
        \Core\Engine\Payment\Util::universalConstructor($this, $options);
    }
    
    public function setSuccess($success) {
        $this->success = (boolean)$success;
        return $this;
    }
    
    public function isSuccess() {
        return $this->success;
    }
    
    public function setTransactionId($transactionId) {
        $this->transactionId = $transactionId;
        return $this;
    }
    
    public function getTransactionId() {
        return $this->transactionId;
    }
    
    public function setErrors(array $errors) {
        $this->clearErrors();
        foreach( $errors as $error ) {
            $this->addError($error);
        }
        return $this;
    }
    
    public function addError(array $error) {
        $this->errors[] = $error;
        return $this;
    }
    
    public function clearErrors() {
        $this->errors = array();
        return $this;
    }
    
    public function hasErrors() {
        return count($this->errors) > 0;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function setRedirect($url) {
        $this->redirect = $url;
        return $this;
    }
    
    public function isRedirect() {
        return (boolean)$this->redirect;
    }
    
    public function getRedirect() {
        return $this->redirect;
    }
    
    public function setVendorMetadata(array $metadata) {
        $this->vendorMetadata = $metadata;
        return $this;
    }
    
    public function getVendorMetadata() {
        return $this->vendorMetadata;
    }
}