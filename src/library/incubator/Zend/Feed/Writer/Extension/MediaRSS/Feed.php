<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @subpackage Zend_Feed_Writer_Extensions_MediaRSS
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend Feed Writer Extension for MediaRSS Feed element
 *
 * Allows addition of mediarss elements to the feed element
 *
 * @link http://video.search.yahoo.com/mrss
 * @link http://www.rssboard.org/media-rss
 *
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @subpackage Zend_Feed_Writer_Extensions_MediaRSS
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Extension_MediaRSS_Feed
{

    /**
     * Elements set for this feed
     *
     * @var Zend_Feed_Writer_Extension_MediaRSS_Elements
     */
    protected $_elements = null;

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Set feed encoding
     * 
     * @param  string $enc 
     * @return Zend_Feed_Writer_Extension_MediaRSS_Feed
     */
    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Create a MediaRSS Optional Elements object
     * 
     * @return Zend_Feed_Writer_Extension_MediaRSS_Elements
     */
    public function createMediaElements() 
    {
        require_once 'Zend/Feed/Writer/Extension/MediaRSS/Elements.php';
        $e = new Zend_Feed_Writer_Extension_MediaRSS_Elements();
        if ($this->getEncoding()) {
            $e->setEncoding($this->getEncoding());
        }
        return $e;
    }

    /**
     * Set this feed's MediaRSS Optional Elements
     * 
     * @param Zend_Feed_Writer_Extension_MediaRSS_Elements $e 
     * @return void
     */
    public function setMediaElements(Zend_Feed_Writer_Extension_MediaRSS_Elements $e) 
    {
        $this->_elements = $e;
    }

    /**
     * get current media elements obj
     * 
     * @return Zend_Feed_Writer_Extension_MediaRSS_Elements
     */
    public function getMediaElements() 
    {
        return $this->_elements;
    }

    /**
     * Overloading: proxy to internal setters
     * 
     * @param  string $method 
     * @param  array $params 
     * @return mixed
     */
    public function __call($method, array $params)
    {
        if (!method_exists($this, $method) && !method_exists($this, $method)) {
            require_once 'Zend/Feed/Writer/Exception/InvalidMethodException.php';
            throw new Zend_Feed_Writer_Exception_InvalidMethodException(
                'invalid method: ' . $method
            );
        }
        return null;
    }

}
