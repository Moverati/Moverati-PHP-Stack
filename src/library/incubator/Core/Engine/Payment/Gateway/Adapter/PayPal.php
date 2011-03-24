<?php
namespace Core\Engine\Payment\Gateway\Adapter;

abstract class PayPal extends \Core\Engine\Payment\Gateway\AdapterAbstract {
    /**
     * @var boolean
     */
    protected $sandbox = false;
    
    /**
     * @var string
     */
    protected $apiUrl;
    
    /**
     * @var string
     */
    protected $version = '63.0';
    
    /**
     * @var string
     */
    protected $user;
    
    /**
     * @var string
     */
    protected $pwd;
    
    /**
     * @var string
     */
    protected $signature;

    /**
     * 
     * @return string
     */
    public function getVendor() {
        return 'paypal';
    }

    /**
     *
     * @return boolean
     */
    public function getSandbox() {
        return $this->sandbox;
    }

    /**
     *
     * @param boolean $sandbox
     * @return PayPal
     */
    public function setSandbox($sandbox) {
        $this->sandbox = (boolean)$sandbox;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     *
     * @param string $version
     * @return PayPal
     */
    public function setVersion($version) {
        $this->version = (string)$version;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     *
     * @param string $user
     * @return PayPal
     */
    public function setUser($user) {
        $this->user = (string)$user;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPwd() {
        return $this->pwd;
    }

    /**
     *
     * @param string $pwd
     * @return PayPal
     */
    public function setPwd($pwd) {
        $this->pwd = (string)$pwd;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSignature() {
        return $this->signature;
    }

    /**
     *
     * @param string $signature
     * @return PayPal
     */
    public function setSignature($signature) {
        $this->signature = (string)$signature;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiUrl() {
        if( $this->apiUrl )
            return $this->apiUrl;
        elseif( $this->getSandbox() )
            return "https://api.sandbox.paypal.com/nvp";
        else
            return "https://api-3t.paypal.com/nvp";
    }

    /**
     *
     * @param string $url
     * @return PayPal
     */
    public function setApiUrl($url) {
        $this->apiUrl = $url;
        return $this;
    }

    /**
     *
     * @param string $method
     * @param array $parameters
     * @return array
     */
    protected function _executePaypalAction($method, array $parameters) {
        $request = array(
            'VERSION' => $this->getVersion(),
            'METHOD' => $method,
            'USER' => $this->getUser(),
            'PWD' => $this->getPwd(),
        );
        
        if( $this->getSignature() )
            $request['SIGNATURE'] = $this->getSignature();
        
        $request += $parameters;
        
        $response = $this->_makeRequest($request);
        
        return $response;
    }

    /**
     *
     * @param array $results
     * @return \Core\Engine\Payment\Gateway\Response
     */
    protected function _buildErrorResponse(array $results) {
        $i = 0;
        $errors = array();
        while( isset($results["L_ERRORCODE{$i}"]) ) {
            $errors[] = array(
                'code' => $results["L_ERRORCODE{$i}"],
                'message' => sprintf(
                    "[%s][%s] %s",
                    $results["L_SEVERITYCODE{$i}"],
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

    /**
     *
     * @param array $parameters
     * @return array
     */
    protected function _makeRequest(array $parameters) {
        $headers = array(
            'Content-Type: text/plain',
        );
        
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\n", $headers),
                'content' => $this->_encodeNvp($parameters),
            ),
        );
        
        $context = stream_context_create($options);
        
        $response = null;
        
        if( $resource = fopen($this->getApiUrl(), 'r', false, $context) ) {
            
            $data = stream_get_contents($resource);
            $metadata = $this->_formatStreamMetadata(stream_get_meta_data($resource));
            
            $response = $this->_decodeNvp($data);
            
            fclose($resource);
            
        } else {
            throw new \Exception("Could not connect to PayPal");
        }
        
        return $response;
    }
    
    /**
     * @param array $stream_meta_data
     * @return array
     */
    protected function _formatStreamMetadata(array $stream_meta_data) {
        $response = array(
            'code' => null,
            'headers' => array(),
        );
        
        foreach($stream_meta_data['wrapper_data'] as $header) {
            $parts = explode(':', $header, 2);
            
            if( count($parts) != 2 ) {
                $matches = array();
                if( preg_match("#^(?<protocol>[a-zA-Z/0-9\.]+)\s+(?<code>[0-9]+)\s+(?<reasonphrase>.*)$#", trim($parts[0]), $matches) ) {
                    $response['code'] = (int)$matches['code'];
                } else {
                    throw new \Exception("PayPal returned invalid HTTP/1.1 Response Line");
                }
            } else {
                $response['headers'][$this->_formatHeader(trim($parts[0]))] = trim($parts[1]);
            }
        }
        
        return $response;
    }
    
    /**
     * @param string $header
     * @return string
     */
    protected function _formatHeader($header) {
        $header = strtolower($header);
        
        $header = str_replace('-', ' ', $header);
        
        $header = ucwords($header);
        
        $header = str_replace(' ', '-', $header);
        
        return $header;
    }
    
    /**
     * @param array $parameters
     * @return string
     */
    protected function _encodeNvp(array $parameters) {
        return http_build_query(array_change_key_case($parameters, CASE_UPPER));
    }
    
    /**
     * @param string $string
     * @return array
     */
    protected function _decodeNvp($string) {
        $parameters = array();
        parse_str($string, $parameters);
        
        return array_change_key_case($parameters, CASE_UPPER);
    }
}