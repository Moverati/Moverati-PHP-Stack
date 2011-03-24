<?php
namespace Core\Engine\Payment;

use Core\Engine\Payment;

class Transaction {
    const CURRENCY_USD = 'USD';

    protected $_allowedCurrencies = array(
        self::CURRENCY_USD,
    );

    protected $currency = self::CURRENCY_USD;
    
    /**
     * @var string
     */
    protected $invoiceId;
    
    /**
     * @var string
     */
    protected $description;

    /**
     *
     * @var Payment\Transaction\Contact
     */
    protected $customerContact;

    /**
     *
     * @var Payment\Transaction\Address
     */
    protected $customerAddress;

    /**
     *
     * @var Payment\Transaction\Contact
     */
    protected $shippingContact;

    /**
     *
     * @var Payment\Transaction\Address
     */
    protected $shippingAddress;
    
    /**
     * @var double
     */
    protected $subtotal;

    /**
     *
     * @var double
     */
    protected $shipping;

    /**
     *
     * @var double
     */
    protected $handling;

    /**
     * Pre-Calculated Tax
     *
     * @var double
     */
    protected $tax;

    /**
     *
     * @var Transaction\PaymentMethod
     */
    protected $paymentMethod;
    
    /**
     * @var array
     */
    protected $vendorOptions = array();
    
    /**
     *
     * @param array $options
     */
    public function __construct(array $options = array()) {
        \Core\Engine\Payment\Util::universalConstructor($this, $options);
    }

    /**
     *
     * @param string $currency
     * @return Transaction
     */
    public function setCurrency($currency) {
        if( !in_array($currency, $this->_allowedCurrencies) )
            throw new Payment\Exception("Currency {$currency} not allowed");

        $this->currency = strtoupper($currency);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Set internal invoice identification. This will NOT be the transaction ID
     * returned by your merchant.
     *
     * @param string $invoiceId
     * @return Transaction
     */
    public function setInvoiceId($invoiceId) {
        $this->invoiceId = (string)$invoiceId;
        return $this;
    }

    /**
     * Get internal invoice identification.
     * 
     * @return string
     */
    public function getInvoiceId() {
        return $this->invoiceId;
    }

    /**
     *
     * @param string $description
     * @return Transaction
     */
    public function setDescription($description) {
        $this->description = (string)$description;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     *
     * @return Payment\Transaction\Contact
     */
    public function getCustomerContact() {
        return $this->customerContact;
    }

    /**
     *
     * @param Payment\Transaction\Contact $customerContact
     * @return Transaction
     */
    public function setCustomerContact(Payment\Transaction\Contact $customerContact) {
        $this->customerContact = $customerContact;
        return $this;
    }

    /**
     *
     * @return Payment\Transaction\Address
     */
    public function getCustomerAddress() {
        return $this->customerAddress;
    }

    /**
     *
     * @param Payment\Transaction\Address $customerAddress
     * @return Transaction
     */
    public function setCustomerAddress(Payment\Transaction\Address $customerAddress) {
        $this->customerAddress = $customerAddress;
        return $this;
    }

    /**
     *
     * @return Payment\Transaction\Contact
     */
    public function getShippingContact() {
        return $this->shippingContact;
    }

    /**
     *
     * @param Payment\Transaction\Contact $shippingContact
     * @return Transaction
     */
    public function setShippingContact(Payment\Transaction\Contact $shippingContact) {
        $this->shippingContact = $shippingContact;
        return $this;
    }

    /**
     *
     * @return Payment\Transaction\Address
     */
    public function getShippingAddress() {
        return $this->shippingAddress;
    }

    /**
     *
     * @param Payment\Transaction\Address $shippingAddress
     * @return Transaction
     */
    public function setShippingAddress(Payment\Transaction\Address $shippingAddress) {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     *
     * @param double $subtotal
     * @return Transaction
     */
    public function setSubTotal($subtotal) {
        $this->subtotal = (double)$subtotal;
        return $this;
    }

    /**
     *
     * @return double
     */
    public function getSubTotal() {
        return $this->subtotal;
    }

    /**
     *
     * @param double $subtotal
     * @return Transaction
     */
    public function setShipping($shipping) {
        $this->shipping = (double)$shipping;
        return $this;
    }

    /**
     *
     * @return double
     */
    public function getShipping() {
        return $this->shipping;
    }

    /**
     *
     * @param double $subtotal
     * @return Transaction
     */
    public function setHandling($handling) {
        $this->handling = (double)$handling;
        return $this;
    }

    /**
     *
     * @return double
     */
    public function getHandling() {
        return $this->handling;
    }

    /**
     * Set Pre-Calculated Tax Amount (NOT percentage rate!)
     *
     * @param double $subtotal
     * @return Transaction
     */
    public function setTax($tax) {
        $this->tax = (double)$tax;
        return $this;
    }

    /**
     *
     * @return double
     */
    public function getTax() {
        return $this->tax;
    }

    /**
     *
     * @return double
     */
    public function getTotal() {
        return $this->getSubTotal()
             + $this->getShipping()
             + $this->getHandling()
             + $this->getTax();
    }

    /**
     *
     * @param \Core\Engine\Payment\Transaction\PaymentMethod $method
     * @return Transaction
     */
    public function setPaymentMethod(\Core\Engine\Payment\Transaction\PaymentMethod $method) {
        $this->paymentMethod = $method;
        return $this;
    }

    /**
     *
     * @return \Core\Engine\Payment\Transaction\PaymentMethod
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     *
     * @param string $vendor
     * @param string $option
     * @param mixed $value
     * @return Transaction
     */
    public function setVendorOption($vendor, $option, $value) {
        $this->vendorOptions[strtolower($vendor)][strtolower($option)] = $value;
        return $this;
    }

    /**
     *
     * @param array $vendors
     * @return Transaction
     */
    public function setVendorOptions(array $vendors) {
        foreach( $vendors as $vendor => $options ) {
            foreach( $options as $option => $value ) {
                $this->setVendorOption($vendor, $option, $value);
            }
        }
        return $this;
    }

    /**
     *
     * @param string $vendor
     * @param string $option
     * @return boolean
     */
    public function hasVendorOption($vendor, $option) {
        return isset($this->vendorOptions[strtolower($vendor)][strtolower($option)]);
    }

    /**
     *
     * @param string $vendor
     * @param string $option
     * @param string $default
     * @return mixed
     */
    public function getVendorOption($vendor, $option, $default = null) {
        if( $this->hasVendorOption($vendor, $option) )
            return $this->vendorOptions[strtolower($vendor)][strtolower($option)];
        else
            return $default;
    }

    /**
     *
     * @param string $vendor
     * @return array
     */
    public function getVendorOptions($vendor) {
        if( isset($this->vendorOptions[strtolower($vendor)]) )
            return $this->vendorOptions[strtolower($vendor)];
        else
            return null;
    }
    
    /**
     *
     * @param string $vendor
     * @return Transaction
     */
    public function clearVendorOptions($vendor = null) {
        if( $vendor )
           unset($this->vendorOptions[strtolower($vendor)]);
        else
           $this->vendorOptions = array();

        return $this;
    }
}