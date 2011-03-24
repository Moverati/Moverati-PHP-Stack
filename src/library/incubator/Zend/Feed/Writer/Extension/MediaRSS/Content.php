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

require_once 'Zend/Feed/Exception.php';

/**
 * Zend Feed Writer Extension for MediaRSS Content element
 *
 * contains methods for the mediarss content element that can live in groups or entries
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
class Zend_Feed_Writer_Extension_MediaRSS_Content
{

    /**
     * Internal array containing all data associated with this entry or item.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Elements set for this entry
     *
     * @var Zend_Feed_Writer_Extension_MediaRSS_Elements
     */
    protected $_elements = null;

    /**
     * Set feed encoding
     * 
     * @param  string $enc 
     * @return Zend_Feed_Writer_Extension_MediaRSS_Entry
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
     * Unset a specific data point
     *
     * @param string $name
     */
    public function remove($name)
    {
        if (isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
    }

    /**
     * Overloading to mediarss content specific setters
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
        if (!array_key_exists($point, $this->_data) || empty($this->_data[$point])) {
            return null;
        }
        return $this->_data[$point];
    }

    /**
     * create a media elements object for this content 
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
     * set the media elements object for this content 
     * 
     * @param Zend_Feed_Writer_Extension_MediaRSS_Elements $e 
     * @return void
     */
    public function setMediaElements(Zend_Feed_Writer_Extension_MediaRSS_Elements $e) 
    {
        $this->_elements = $e;
    }

    /**
     * get media elements 
     * 
     * @return Zend_Feed_Writer_Extension_MediaRSS_Elements
     */
    public function getMediaElements() {
        return $this->_elements;
    }

    /**
     * url should specify the direct url to the media object. If not included, a <media:player> element must be specified. 
     * 
     * @param string|Zend_Uri $value 
     * @return void
     */
    public function setMediaContentUrl($value) 
    {
        if (!Zend_Uri::check($value)) {
            throw new Zend_Feed_Exception('uri is invalid');
        }
        $this->_data['url'] = $value;
    }

    /**
     * fileSize is the number of bytes of the media object. It is an optional attribute.
     * 
     * @param int $value 
     * @return void
     */
    public function setMediaContentFileSize($value) 
    {
        if (!is_int($value)) {
            throw new Zend_Feed_Exception('Filesize must be int in bytes');
        }
        $this->_data['fileSize'] = $value;
    }

    /**
     * type is the standard MIME type of the object. It is an optional attribute. 
     * 
     * @param string $value 
     * @return void
     */
    public function setMediaContentType($value) 
    {
        //todo validate mime type
        $this->_data['type'] = $value;
    }

    /**
     * Content Medium Types
     *
     * @var array
     */
    public static $medium_types = array('image','video','audio','document','executable');

    /**
     * medium is the type of object (image | audio | video | document | executable). 
     * While this attribute can at times seem redundant if type is supplied, 
     * it is included because it simplifies decision making on the reader side, 
     * as well as flushes out any ambiguities between MIME type and object type. 
     * It is an optional attribute. 
     * 
     * @param string $value one of self::$medium_types
     * @return void
     */
    public function setMediaContentMedium($value) 
    {
        if (!in_array($value, self::$medium_types)) {
            throw new Zend_Feed_Exception('medium not supported');
        }
        $this->_data['medium'] = $value;
    }

    /**
     * isDefault determines if this is the default object that should be used 
     * for the <media:group>. There should only be one default object per 
     * <media:group>. It is an optional attribute 
     * 
     * @param bool|string $value (true|false)
     * @return void
     */
    public function setMediaContentIsDefault($value) 
    {
        if (is_bool($value)) {
            $value = ($value) ? 'true' : 'false';
        } else {
            if ($value != 'true' && $value != 'false') {
                throw new Zend_Feed_Exception('isDefault value not supported');
            }
        }
        $this->_data['isDefault'] = $value;
    }

    /**
     * Media Content expression types
     *
     * @var array
     */
    public static $expression_types = array('sample','full','nonstop');

    /**
     * expression determines if the object is a sample or the full version of 
     * the object, or even if it is a continuous stream (sample | full | nonstop). 
     * Default value is 'full'. It is an optional attribute 
     * 
     * @param string $value one of self::$expression_types
     * @return void
     */
    public function setMediaContentExpression($value) 
    {
        if (!in_array($value, self::$expression_types)) {
            throw new Zend_Feed_Exception('expression not supported');
        }
        $this->_data['expression'] = $value;
    }

    /**
     * bitrate is the kilobits per second rate of media. It is an optional attribute 
     * 
     * @param float $value 
     * @return void
     */
    public function setMediaContentBitrate($value) 
    {
        if (!is_numeric($value)) {
            throw new Zend_Feed_Exception('bitrate must be number in kbps');
        }
        $this->_data['bitrate'] = $value;
    }

    /**
     * framerate is the number of frames per second for the media object. 
     * It is an optional attribute. 
     * 
     * @param float $value 
     * @return void
     */
    public function setMediaContentFramerate($value) 
    {
        if (!is_numeric($value)) {
            throw new Zend_Feed_Exception('framerate must be number in fps');
        }
        $this->_data['framerate'] = $value;
    }

    /**
     * samplingrate is the number of samples per second taken to create 
     * the media object. It is expressed in thousands of samples per second (kHz). 
     * It is an optional attribute 
     * 
     * @param float $value 
     * @return void
     */
    public function setMediaContentSamplingrate($value) 
    {
        if (!is_numeric($value)) {
            throw new Zend_Feed_Exception('samplerate must be number in khz');
        }
        $this->_data['samplingrate'] = $value;
    }

    /**
     * channels is number of audio channels in the media object. It is an optional attribute. 
     * 
     * @param int $value 
     * @return void
     */
    public function setMediaContentChannels($value) 
    {
        if (!is_int($value)) {
            throw new Zend_Feed_Exception('channels must be number');
        }
        $this->_data['channels'] = $value;
    }

    /**
     * duration is the number of seconds the media object plays. It is an optional attribute. 
     * 
     * @param float $value 
     * @return void
     */
    public function setMediaContentDuration($value) 
    {
        if (!is_numeric($value)) {
            throw new Zend_Feed_Exception('duration must be number in seconds');
        }
        $this->_data['duration'] = $value;
    }

    /**
     * height is the height of the media object. It is an optional attribute. 
     * 
     * @param int $value 
     * @return void
     */
    public function setMediaContentHeight($value) 
    {
        if (!is_int($value)) {
            throw new Zend_Feed_Exception('height must be int in pixels');
        }
        $this->_data['height'] = $value;
    }

    /**
     * width is the width of the media object. It is an optional attribute. 
     * 
     * @param int $value 
     * @return void
     */
    public function setMediaContentWidth($value) 
    {
        if (!is_int($value)) {
            throw new Zend_Feed_Exception('width must be int in pixels');
        }
        $this->_data['width'] = $value;
    }

    /**
     * lang is the primary language encapsulated in the media object. 
     * Language codes possible are detailed in RFC 3066. This attribute is used 
     * similar to the xml:lang attribute detailed in the XML 1.0 Specification 
     * (Third Edition). It is an optional attribute. 
     * 
     * @param string $value 
     * @return void
     */
    public function setMediaContentLang($value) 
    {
        //todo validate lang
        $this->_data['lang'] = $value;
    }

    /**
     * getData 
     * 
     * @return array
     */
    public function getData() 
    {
        return $this->_data;
    }

}
