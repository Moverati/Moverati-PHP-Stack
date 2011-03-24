<?php
namespace Core\Engine\Payment\Gateway\Adapter\PayPal;

use \Core\Engine\Payment;

class DirectPayment extends Payment\Gateway\Adapter\PayPal {
    const METHOD = 'DoDirectPayment';
    
    public function getName() {
        return 'directpayment';
    }
    
    /**
     * @param \Core\Engine\Payment\Transaction $transaction
     * @param array $processOptions
     * @return \Core\Engine\Payment\Gateway\Response
     */
    public function process(Payment\Transaction $transaction, array $processOptions = array()) {
        $parameters = $this->_transactionToParameters($transaction ,$processOptions);

        $results = $this->_executePaypalAction(self::METHOD, $parameters);

        if( strtolower($results['ACK']) == 'failure' ) {
            return $this->_buildErrorResponse($results);
        }

        $response = new Payment\Gateway\Response(array(
            'success'        => true,
            'transactionId'  => $results['TRANSACTIONID'],
            'vendorMetadata' => $results,
        ));

        return $response;
    }

    /**
     *
     * @param \Core\Engine\Payment\Transaction $transaction
     * @param array $processOptions
     * @return array
     */
    protected function _transactionToParameters(Payment\Transaction $transaction, array $processOptions = array()) {
        $parameters = array();

        //GENERAL OPTIONS
        $parameters['IPADDRESS']        = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $parameters['RETURNFMFDETAILS'] = 0; //Currently we're not doing anything with FMF details

        $parameters['DESC'] = $transaction->getDescription();
        $parameters['INVNUM'] = $transaction->getInvoiceId();

        //PAYMENT METHOD
        if( $transaction->getPaymentMethod() instanceOf Payment\Transaction\PaymentMethod\CreditCard ) {
            $card = $transaction->getPaymentMethod();
            /* @var $card \Core\Engine\Payment\Transaction\PaymentMethod\CreditCard */

            $parameters['CREDITCARDTYPE'] = $card->getType();
            $parameters['ACCT']           = $card->getNumber();
            $parameters['EXPDATE']        = sprintf('%02d%04d', $card->getExpirationDate('month'), $card->getExpirationDate('year'));
            $parameters['CVV2']           = $card->getSecurityCode();

        } else {
            throw new Payment\Exception("Invalid payment method: DirectPayment only works with creditcards.");
        }

        //PAYER INFORMATION
        if( $transaction->getCustomerContact() instanceOf Payment\Transaction\Contact ) {
            $parameters['EMAIL']     = $transaction->getCustomerContact()->getEmail();
            $parameters['FIRSTNAME'] = $transaction->getCustomerContact()->getFirstName();
            $parameters['LASTNAME']  = $transaction->getCustomerContact()->getLastName();
        }

        //ADDRESS FIELDS
        if( $transaction->getCustomerAddress() instanceOf Payment\Transaction\Address ) {
            $parameters['STREET']      = $transaction->getCustomerAddress()->getStreet();
            $parameters['STREET2']     = $transaction->getCustomerAddress()->getStreet2();
            $parameters['CITY']        = $transaction->getCustomerAddress()->getCity();
            $parameters['STATE']       = $transaction->getCustomerAddress()->getState();
            $parameters['COUNTRYCODE'] = $transaction->getCustomerAddress()->getCountry();
            $parameters['ZIP']         = $transaction->getCustomerAddress()->getZip();
        }

        if( $transaction instanceOf Payment\Transaction\Cart ) {
            //CART DETAILS
            /* @todo CART DETAILS FOR DIRECT PAYMENT */
            throw new Payment\Gateway\Exception("Processing Cart Tranasactions not implemented yet");
        }

        $parameters['CURRENCYCODE'] = Payment\Transaction::CURRENCY_USD; //Default to USD until we add support later
        $parameters['AMT']          = $transaction->getTotal();
        $parameters['ITEMAMT']      = $transaction->getSubTotal();
        $parameters['SHIPPINGAMT']  = $transaction->getShipping();
        $parameters['HANDLINGAMT']  = $transaction->getHandling();
        $parameters['TAXAMT']       = $transaction->getTax();

        //SHIPPING FIELDS
        /* @todo SHIPPING FOR DIRECT PAYMENT */

        return $parameters;
    }
}