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

namespace Core\Engine\Mail\Transport;

/**
 * Postmark Mail Transport Class for Zend_Mail Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class PostmarkTest extends \PHPUnit_Framework_TestCase
{
    public function testPostmarkExtendsZendMailTransport()
    {
        $transport = new Postmark(null);
        $this->assertTrue($transport instanceof \Zend_Mail_Transport_Abstract);
    }

    public function testConstructprSetsApiKey()
    {
        $transport = new Postmark('test');
        $this->assertAttributeEquals('test', 'apiKey', $transport);

        $this->assertEquals('test', $transport->getApiKey());
    }

    public function testConstructorSetsHttpClient()
    {
        $transport = new Postmark(null);
        $client    = $transport->getHttpClient();
        $this->assertTrue($client instanceof \Zend_Http_Client);
    }

    public function testGetApiKeyReturnsApiKey()
    {
        $transport = new Postmark(null);
        $this->assertAttributeEquals(null, 'apiKey', $transport);

        $transport->setApiKey('test');
        $this->assertAttributeEquals('test', 'apiKey', $transport);

        $apiKey = $transport->getApiKey();
        $this->assertEquals($apiKey, 'test');
    }

    public function testSetApiKeyReturnsItself()
    {
        $transport = new Postmark(null);
        $return    = $transport->setApiKey('test');

        $this->assertSame($transport, $return);
    }

    public function testSetApiKeySetsTheApiKey()
    {
        $transport = new Postmark(null);
        $transport->setApiKey('test');

        $this->assertEquals('test', $transport->getApiKey());
    }

    public function testGetHttpClientReturnsZendHttpClient()
    {
        $httpClient = new \Zend_Http_Client();

        $transport = new Postmark(null);
        $transport->setHttpClient($httpClient);

        $client = $transport->getHttpClient();

        $this->assertSame($httpClient, $client);
    }

    public function testSetHttpClientSetsTheHttpClient()
    {
        $httpClient = new \Zend_Http_Client();

        $transport = new Postmark(null);
        $transport->setHttpClient($httpClient);

        $client = $transport->getHttpClient();

        $this->assertSame($httpClient, $client);
    }

    public function testSetHttpClientReturnsItself()
    {
        $httpClient = new \Zend_Http_Client();

        $transport = new Postmark(null);
        $return    = $transport->setHttpClient($httpClient);

        $this->assertSame($transport, $return);
    }

    public function testSendMailThrowsUnauthorizedExceptionWhenUnauthorized()
    {
        $this->setExpectedException(
            'Core\Engine\Mail\Transport\Postmark\Exception\UnauthorizedException',
            null,
            401
        );

        $response = new \Zend_Http_Response(401, array(), '');

        $adapter = new \Zend_Http_Client_Adapter_Test();
        $adapter->setResponse($response);


        $client = new \Zend_Http_Client();
        $client->setAdapter($adapter);

        $mail = new \Zend_Mail();
        $mail->setBodyText('test');

        $transport = new Postmark('myInvalidApiKey');
        $transport->setHttpClient($client)
                  ->send($mail);
    }

    public function testSendMailThrowsUnprocessableEntityExceptionWhen422Received()
    {
        $this->setExpectedException(
            'Core\Engine\Mail\Transport\Postmark\Exception\UnprocessableEntityException',
            null,
            0
        );

        $response = new \Zend_Http_Response(422, array(), '{"ErrorCode": 0, "Message": "details"}');

        $adapter = new \Zend_Http_Client_Adapter_Test();
        $adapter->setResponse($response);

        $client = new \Zend_Http_Client();
        $client->setAdapter($adapter);

        $mail = new \Zend_Mail();
        $mail->setBodyText('test');

        $transport = new Postmark(null);
        $transport->setHttpClient($client)
                  ->send($mail);
    }

    public function testSendMailThrowsZendMailTransportExceptionOnUnknownError()
    {
        $this->setExpectedException(
            'Zend_Mail_Transport_Exception',
            null,
            0
        );

        $response = new \Zend_Http_Response(500, array(), '{"ErrorCode": 0, "Message": "details"}');

        $adapter = new \Zend_Http_Client_Adapter_Test();
        $adapter->setResponse($response);

        $client = new \Zend_Http_Client();
        $client->setAdapter($adapter);

        $mail = new \Zend_Mail();
        $mail->setBodyText('test');

        $transport = new Postmark(null);
        $transport->setHttpClient($client)
                  ->send($mail);
    }

    public function testSendMailSuccessWithMultipleHeaders()
    {
        $response = new \Zend_Http_Response(200, array(), '{"ErrorCode": 0, "Message": "details"}');

        $adapter = new \Zend_Http_Client_Adapter_Test();
        $adapter->setResponse($response);

        $client = new \Zend_Http_Client();
        $client->setAdapter($adapter);

        $mail = new \Zend_Mail();
        $mail->setBodyText('test')
             ->setBodyHtml('<html></html>')
             ->addBcc('sjobs@apple.com')
             ->addBcc('test@coreactdion.com')
             ->addCc('test@coreactidon.com', 'Ben Gatzke')
             ->addCc('test@bar.com')
             ->addTo('geoffrey.tran@gmail', 'Geoffrey Tran')
             ->addTo('joe@msn.com')
             ->setSubject('subject')
             ->setFrom('Joe@msn.com', 'Joe')
             ->setReplyTo('joe@msn.com', 'joe')
             ->addHeader('test', 'test')
             ->addHeader('test', 'test', true)
             ->addHeader('bar','bar');

        $transport = new Postmark(null);
        $transport->setHttpClient($client)
                  ->send($mail);

        $request = $client->getLastRequest();
        $expected = array(
            'From'     => 'Joe <Joe@msn.com>',
            'To'       => 'Geoffrey Tran <geoffrey.tran@gmail>,joe@msn.com',
            'Cc'       => 'Ben Gatzke <test@coreactidon.com>,test@bar.com',
            'Bcc'      => 'sjobs@apple.com,test@coreactdion.com',
            'Subject'  => 'subject',
            'ReplyTo'  => 'joe <joe@msn.com>',
            'Headers'  => array(
                array(
                    'Name'  => 'test',
                    'Value' => 'test,test'
                ),
                array(
                    'Name'  => 'bar',
                    'Value' => 'bar'
                )
            ),
            'TextBody' => 'test',
            'HtmlBody' => '<html></html>'
        );

        $this->assertContains(\Zend_Json::encode($expected), $request);
    }

    public function testSendMailRemovesDate()
    {
        $response = new \Zend_Http_Response(200, array(), '{"ErrorCode": 0, "Message": "details"}');

        $adapter = new \Zend_Http_Client_Adapter_Test();
        $adapter->setResponse($response);

        $client = new \Zend_Http_Client();
        $client->setAdapter($adapter);

        $mail = new \Zend_Mail();
        $mail->setBodyText('test')
             ->setBodyHtml('<html></html>')
             ->addBcc('sjobs@apple.com')
             ->addBcc('test@coreactdion.com')
             ->addCc('test@coreactidon.com', 'Ben Gatzke')
             ->addCc('test@bar.com')
             ->addTo('geoffrey.tran@gmail', 'Geoffrey Tran')
             ->addTo('joe@msn.com')
             ->setSubject('subject')
             ->setFrom('Joe@msn.com', 'Joe')
             ->setReplyTo('joe@msn.com', 'joe')
             ->addHeader('test', 'test')
             ->addHeader('test', 'test', true)
             ->setDate();

        $transport = new Postmark(null);
        $transport->setHttpClient($client)
                  ->send($mail);

        $request = $client->getLastRequest();
        $expected = array(
            'From'     => 'Joe <Joe@msn.com>',
            'To'       => 'Geoffrey Tran <geoffrey.tran@gmail>,joe@msn.com',
            'Cc'       => 'Ben Gatzke <test@coreactidon.com>,test@bar.com',
            'Bcc'      => 'sjobs@apple.com,test@coreactdion.com',
            'Subject'  => 'subject',
            'ReplyTo'  => 'joe <joe@msn.com>',
            'Headers'  => array(
                array(
                    'Name'  => 'test',
                    'Value' => 'test,test'
                )
            ),
            'TextBody' => 'test',
            'HtmlBody' => '<html></html>'
        );

        $this->assertContains(\Zend_Json::encode($expected), $request);
    }

    public function testSendMailSuccessWithOneHeader()
    {
        $response = new \Zend_Http_Response(200, array(), '{"ErrorCode": 0, "Message": "details"}');

        $adapter = new \Zend_Http_Client_Adapter_Test();
        $adapter->setResponse($response);

        $client = new \Zend_Http_Client();
        $client->setAdapter($adapter);

        $mail = new \Zend_Mail();
        $mail->setBodyText('test')
             ->setBodyHtml('<html></html>')
             ->addBcc('sjobs@apple.com')
             ->addBcc('test@coreactdion.com')
             ->addCc('test@coreactidon.com', 'Ben Gatzke')
             ->addCc('test@bar.com')
             ->addTo('geoffrey.tran@gmail', 'Geoffrey Tran')
             ->addTo('joe@msn.com')
             ->setSubject('subject')
             ->setFrom('Joe@msn.com', 'Joe')
             ->setReplyTo('joe@msn.com', 'joe')
             ->addHeader('test', 'test');

        $transport = new Postmark(null);
        $transport->setHttpClient($client)
                  ->send($mail);

        $request = $client->getLastRequest();
        $expected = array(
            'From'     => 'Joe <Joe@msn.com>',
            'To'       => 'Geoffrey Tran <geoffrey.tran@gmail>,joe@msn.com',
            'Cc'       => 'Ben Gatzke <test@coreactidon.com>,test@bar.com',
            'Bcc'      => 'sjobs@apple.com,test@coreactdion.com',
            'Subject'  => 'subject',
            'ReplyTo'  => 'joe <joe@msn.com>',
            'Headers'  => array(
                array(
                    'Name'  => 'test',
                    'Value' => 'test'
                )
            ),
            'TextBody' => 'test',
            'HtmlBody' => '<html></html>'
        );

        $this->assertContains(\Zend_Json::encode($expected), $request);
    }
}