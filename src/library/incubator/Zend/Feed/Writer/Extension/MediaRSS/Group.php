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
 * Zend Feed Writer Extension for MediaRSS Group element
 *
 * contains methods for the mediarss group element
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
class Zend_Feed_Writer_Extension_MediaRSS_Group
{

    /**
     * Elements set for this entry
     *
     * @var Zend_Feed_Writer_Extension_MediaRSS_Elements
     */
    protected $_elements = null;

    /**
     * Content items for this entry
     * 
     * @var array
     */
    protected $_content = array();

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
     * @return Zend_Feed_Writer_Extension_MediaRSS_Group
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
     * create a media content object (for this entry) 
     * 
     * @return Zend_Feed_Writer_Extension_MediaRSS_Content
     */
    public function createMediaContent() 
    {
        require_once 'Zend/Feed/Writer/Extension/MediaRSS/Content.php';
        $c = new Zend_Feed_Writer_Extension_MediaRSS_Content();
        if ($this->getEncoding()) {
            $c->setEncoding($this->getEncoding());
        }
        return $c;
    }

    /**
     * create a media elements object (for this entry) 
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
     * add many media contents objs to this entry 
     * 
     * @param array $values of Zend_Feed_Writer_Extension_MediaRSS_Content
     * @return void
     */
    public function addMediaContents(array $values) 
    {
        foreach ($values as $v) {
            $this->addMediaContent($v);
        }
    }

    /**
     * get media contents 
     * 
     * @return array of Zend_Feed_Writer_Extension_MediaRSS_Content
     */
    public function getMediaContents() 
    {
        return $this->_content;
    }

    /**
     * add a media content obj to this entry 
     * 
     * @param Zend_Feed_Writer_Extension_MediaRSS_Content $c 
     * @return void
     */
    public function addMediaContent(Zend_Feed_Writer_Extension_MediaRSS_Content $c) 
    {
        $this->_content[] = $c;
    }

    /**
     * set media optional elements on this entry 
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
    public function getMediaElements() 
    {
        return $this->_elements;
    }

}
