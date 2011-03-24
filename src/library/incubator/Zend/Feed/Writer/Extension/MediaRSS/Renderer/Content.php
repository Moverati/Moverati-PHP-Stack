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
 * @see Zend_Feed_Writer_Extension_RendererAbstract
 */
require_once 'Zend/Feed/Writer/Extension/RendererAbstract.php';

/**
 * Zend Feed Writer Renderer for MediaRSS Content Element
 *
 * renderer for mediarss content element items
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
class Zend_Feed_Writer_Extension_MediaRSS_Renderer_Content
extends Zend_Feed_Writer_Extension_RendererAbstract
{

    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $_called = false;

    /**
     * Render feed
     * 
     * @return void
     */
    public function render()
    {
        $c = $this->_dom->createElement('media:content');
        $this->_setContentAttributes($this->_dom, $c);

        $this->_renderElements($this->_dom, $c);

        $this->_base->appendChild($c);
    }

    /**
     * Append feed namespaces
     * 
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:media',
                                              'http://search.yahoo.com/mrss/');  
    }

    /**
     * delegate rendering to elements
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _renderElements(DOMDocument $dom, DOMElement $root)
    {
        $e = $this->getDataContainer()->getMediaElements();
        if (!$e || empty($e) || $e->isEmpty()) {
            return;
        }

        require_once 'Zend/Feed/Writer/Extension/MediaRSS/Renderer/Elements.php';
        $r = new Zend_Feed_Writer_Extension_MediaRSS_Renderer_Elements($e);
        $r->setDomDocument($dom, $root);
        $r->setEncoding($this->getEncoding());
        $r->setType($this->getType());
        $r->setRootElement($this->getRootElement());
        $r->render();

        $this->_called = true;
    }

    /**
     * content attributes
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _setContentAttributes(DOMDocument $dom, DOMElement $root) {

        $data = $this->getDataContainer()->getData();

        foreach ($data as $key => $value) {
            $root->setAttribute($key, $value);
        }

        $this->_called = true;
    }

}
