<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Engine\Payment\Gateway\Adapter\PayPal;

use Core\Engine\Payment;

/**
 * Test class for DirectPayment.
 * Generated by PHPUnit on 2010-10-29 at 19:00:39.
 */
class DirectPaymentTest extends \PHPUnit_Framework_TestCase {
    /**
     *
     * @var array
     */
    protected $apiCredentials = array();

    /**
     *
     * @var Payment\Transaction\PaymentMethod\CreditCard
     */
    protected $cc;

    public function setUp() {

        $this->apiCredentials = array(
            'sandbox' => true,
            'user' => 'dcousi_1286230335_biz_api1.moverati.com',
            'pwd' => '1286230465',
            'signature' => 'A0rgg4NB9KitNLr4Rt.FGXCy1u1QAs4X2pW-V0bRcwcO9cnqfTRb7TWj',
        );

        $this->cc = new Payment\Transaction\PaymentMethod\CreditCard(array(
            'type'           => Payment\Transaction\PaymentMethod\CreditCard::TYPE_VISA,
            'number'         => '4154595830697517',
            'expirationDate' => '102015',
            'securityCode'   => '000',
        ));
    }

    public function tearDown() {
        
    }

    public function testRealTransaction() {
        $gateway = new Payment\Gateway(array(
            'adapter' => $adapter = new Payment\Gateway\Adapter\PayPal\DirectPayment(array(
                'sandbox'   => $this->apiCredentials['sandbox'],
                'user'      => $this->apiCredentials['user'],
                'pwd'       => $this->apiCredentials['pwd'],
                'signature' => $this->apiCredentials['signature'],
            )),
        ));

        if( !$this->_url_exists($adapter->getApiUrl()) )
            $this->markTestSkipped("PayPal API not accessible, skipping real test");

        $transaction = new Payment\Transaction(array(
            'currency'        => Payment\Transaction::CURRENCY_USD,
            'invoiceId'       => 'PHPUNIT-' . microtime(),
            'description'     => 'Automated Testing Transaction',
            'customerContact' => new Payment\Transaction\Contact(array(
                'firstName' => 'Foo',
                'lastName'  => 'Bar',
                'email'     => 'foo@bar.com',
                'phone'     => '555-555-5555',
            )),
            'customerAddress' => new Payment\Transaction\Address(array(
                'street'  => '1234 Foo',
                'street2' => 'APT 1234',
                'city'    => 'Hell',
                'state'   => 'MI',
                'country' => 'US',
                'zip'     => '48169',
            )),
            'shippingContact' => new Payment\Transaction\Contact(array(
                'firstName' => 'Foo',
                'lastName'  => 'Bar',
                'email'     => 'foo@bar.com',
                'phone'     => '555-555-5555',
            )),
            'shippingAddress' => new Payment\Transaction\Address(array(
                'street'  => '1234 Foo',
                'street2' => 'APT 1234',
                'city'    => 'Hell',
                'state'   => 'MI',
                'country' => 'US',
                'zip'     => '48169',
            )),
            'subtotal'        => 10.00,
            'shipping'        => 10.00,
            'handling'        => 10.00,
            'tax'             => 10.00,
            'paymentMethod'   => $this->cc,
        ));

        $response = $gateway->process($transaction);

        $this->assertTrue($response->isSuccess(), 'Expect payment to be success');

        $vendorMetadata = $response->getVendorMetadata();

        $this->assertEquals($transaction->getTotal(), $vendorMetadata['AMT'], 'Assert processed amount equals requested amount');
    }

    /**
     * @todo Complete cart transactions for direct payment
     */
    public function testRealCartTransaction() {
        $this->markTestIncomplete("To Be Implemented");
    }

    protected function _url_exists($url) {
        if( !function_exists('curl_init') )
            return false;

        if( !$fp = curl_init($url) ) return false;

        curl_close($fp);

        return true;
    }
}
