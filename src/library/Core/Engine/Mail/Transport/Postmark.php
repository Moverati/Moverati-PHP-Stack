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

use Core\Engine;

/**
 * Postmark Mail Transport Class for Zend_Mail
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Postmark extends \Zend_Mail_Transport_Abstract
{
    /**
     * Postmark API URL
     */
    const API_URL = 'http://api.postmarkapp.com/email';

    /**
     * Postmark API Key
     *
     * @var string
     */
    private $apiKey;

    /**
     * HTTP Client
     *
     * @var \Zend_Http_Client
     */
    private $httpClient;

    /**
     * Construct
     *
     * @param string $apiKey Postmark API Key
     */
    public function __construct($apiKey)
    {
        // Init http client
        $client = new \Zend_Http_Client(static::API_URL);

        $this->apiKey     = $apiKey;
        $this->httpClient = $client;
    }

    /**
     * Get the Postmark API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set the Postmark API Key
     *
     * @param string $apiKey
     * @return Postmark
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Get the http client
     *
     * @return \Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the http client
     *
     * @param Zend_Http_Client $client
     * @return Postmark
     */
    public function setHttpClient(\Zend_Http_Client $client)
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Send mail using PHP native mail()
     *
     * @access public
     * @return void
     * @throws Zend_Mail_Transport_Exception if parameters is set
     *         but not a string
     * @throws Zend_Mail_Transport_Exception on mail() failure
     */
    public function _sendMail()
    {
        $mail = $this->_mail;

        // Get the mail headers
        $headers = $this->_headers;

        $to = array();
        if (isset($headers['To'])) {
            foreach($headers['To'] as $key => $val ) {
                if($key !== 'append' ) {
                    $to[] = $val;
                }
            }
            unset($headers['To']);
        }

        $cc = array();
        if (isset($headers['Cc'])) {
            foreach($headers['Cc'] as $key => $val ) {
                if ($key !== 'append' ) {
                    $cc[] = $val;
                }
            }
            unset($headers['Cc']);
        }

        $bcc = array();
        if (isset($headers['Bcc'])) {
            foreach($headers['Bcc'] as $key => $val ) {
                if ($key !== 'append') {
                    $bcc[] = $val;
                }
            }
            unset($headers['Bcc']);
        }

        $from = array();
        if (isset($headers['From'])) {
            foreach($headers['From'] as $key => $val ) {
                if ($key !== 'append') {
                    $from[] = $val;
                }
            }
            unset($headers['From']);
        }

        $replyTo = array();
        if (isset($headers['Reply-To'])) {
            foreach($headers['Reply-To'] as $key => $val ) {
                if ($key !== 'append' ) {
                    $replyTo[] = $val;
                }
            }
            unset($headers['Reply-To']);
        }

        // Remove unneeded headers
        unset($headers['Content-Type']);
        unset($headers['Subject']);
        unset($headers['MIME-Version']);

        $customHeaders = array();
        if (count($headers)) {
            foreach ($headers as $key => $value) {
                if (\strcasecmp('date', $key) === 0) {
                    continue;
                }

                $ch = array();
                foreach ($value as $k => $v) {
                    if ($k !== 'append') {
                        $ch[] = $v;
                    }
                }

                $customHeaders[] = array(
                    'Name'  => $key,
                    'Value' => implode(',', $ch)
                );
            }
        }

        $postData = array(
            'From'    => implode(',', $from),
            'To'      => implode(',', $to),
            'Cc'      => implode(',', $cc),
            'Bcc'     => implode(',', $bcc),
            'Subject' => $this->_mail->getSubject(),
            'ReplyTo' => implode(',', $replyTo),
            'Headers' => $customHeaders
        );

        // We first check if the relevant content exists (returned as a Zend_Mime_Part)
        if ($mail->getBodyText()) {
            /* @var $part \Zend_Mail_Part_Interface */
            $part = $mail->getBodyText();
            $part->encoding = false;
            $postData['TextBody'] = $part->getContent();
        }

        if ($mail->getBodyHtml()) {
            /* @var $part \Zend_Mail_Part_Interface */
            $part = $mail->getBodyHtml();
            $part->encoding = false;
            $postData['HtmlBody'] = $part->getContent();
        }

        // Get the http client
        $client = $this->getHttpClient();

        // Set the uri if empty
        if ($client->getUri(true) == '') {
            $client->setUri(static::API_URL);
        }

        $client->setHeaders( array(
            'Accept'                 => 'application/json',
            'X-Postmark-Server-Token' => $this->getApiKey()
        ));

        $client->setRawData(\Zend_Json::encode($postData), 'application/json');

        // Make the request
        $response = $client->request(\Zend_Http_Client::POST);

        switch ($response->getStatus()) {
            case 401:
                // Unauthorized
                throw new Postmark\Exception\UnauthorizedException();
                break;

            case 422:
                $entity = \Zend_Json::decode($response->getBody());
                throw new Postmark\Exception\UnprocessableEntityException($entity['Message'], $entity['ErrorCode']);
                break;

            case 200:
                break;

            default:
                // Unknown exception occurred
                throw new \Zend_Mail_Transport_Exception(
                    'Mail not sent - Postmark returned ' . $response->getStatus() . ' - ' . $response->getMessage(), $response->getStatus()
                );
                break;
        }
    }
}
