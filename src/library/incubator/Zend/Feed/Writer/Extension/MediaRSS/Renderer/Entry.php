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
 * Zend Feed Writer Renderer for MediaRSS Entry Element
 *
 * renderer for mediarss entry element items
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
class Zend_Feed_Writer_Extension_MediaRSS_Renderer_Entry
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
     * Render entry
     * 
     * @return void
     */
    public function render()
    {
        $this->_renderGroups($this->_dom, $this->_base);
        $this->_renderContents($this->_dom, $this->_base);
        $this->_renderElements($this->_dom, $this->_base);
        if ($this->_called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append namespaces to entry root
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
     * delegate rendering to contents
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _renderContents(DOMDocument $dom, DOMElement $root)
    {
        $contents = $this->getDataContainer()->getMediaContents();
        if (!$contents || empty($contents)) {
            return;
        }

        require_once 'Zend/Feed/Writer/Extension/MediaRSS/Renderer/Content.php';
        foreach ($contents as $c) {
            $r = new Zend_Feed_Writer_Extension_MediaRSS_Renderer_Content($c);
            $r->setDomDocument($dom, $root);
            $r->setEncoding($this->getEncoding());
            $r->setType($this->getType());
            $r->setRootElement($this->getRootElement());
            $r->render();
        }

        $this->_called = true;
    }

    /**
     * delegate rendering to groups
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _renderGroups(DOMDocument $dom, DOMElement $root)
    {
        $groups = $this->getDataContainer()->getMediaGroups();
        if (!$groups || empty($groups)) {
            return;
        }

        require_once 'Zend/Feed/Writer/Extension/MediaRSS/Renderer/Group.php';
        foreach ($groups as $g) {
            $r = new Zend_Feed_Writer_Extension_MediaRSS_Renderer_Group($g);
            $r->setDomDocument($dom, $root);
            $r->setEncoding($this->getEncoding());
            $r->setType($this->getType());
            $r->setRootElement($this->getRootElement());
            $r->render();
        }

        $this->_called = true;
    }

}
