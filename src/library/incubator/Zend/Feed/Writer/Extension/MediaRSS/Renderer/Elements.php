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
 * Zend Feed Writer Renderer for MediaRSS optional elements
 *
 * renderer for mediarss optional elements
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
class Zend_Feed_Writer_Extension_MediaRSS_Renderer_Elements
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
     * container data
     * 
     * @var array
     */
    protected $_data;

    /**
     * Render feed
     * 
     * @return void
     */
    public function render()
    {
        $this->_data = $this->getDataContainer()->getData();
        if (!$this->_data || empty($this->_data)) {
            return;
        }

        $this->_addRatings($this->_dom, $this->_base);
        $this->_setTitle($this->_dom, $this->_base);
        $this->_setDescription($this->_dom, $this->_base);
        $this->_setKeywords($this->_dom, $this->_base);
        $this->_addThumbnails($this->_dom, $this->_base);
        $this->_addCategories($this->_dom, $this->_base);
        $this->_addHashes($this->_dom, $this->_base);
        $this->_setPlayer($this->_dom, $this->_base);
        $this->_addCredits($this->_dom, $this->_base);
        $this->_setCopyright($this->_dom, $this->_base);
        $this->_addTexts($this->_dom, $this->_base);
        $this->_addRestrictions($this->_dom, $this->_base);
        $this->_addCommunity($this->_dom, $this->_base);
        $this->_addComments($this->_dom, $this->_base);
        $this->_setEmbed($this->_dom, $this->_base);
        $this->_addResponses($this->_dom, $this->_base);
        $this->_addBackLinks($this->_dom, $this->_base);
        $this->_setStatus($this->_dom, $this->_base);
        $this->_addPrices($this->_dom, $this->_base);
        $this->_setLicense($this->_dom, $this->_base);
        $this->_addSubTitles($this->_dom, $this->_base);
        $this->_addPeerLinks($this->_dom, $this->_base);
        $this->_addLocations($this->_dom, $this->_base);
        $this->_setRights($this->_dom, $this->_base);
        $this->_addScenes($this->_dom, $this->_base);

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
     * add rating elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addRatings(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['rating'])) {
            return;
        }

        foreach ($this->_data['rating'] as $scheme => $value) {
            $r = $dom->createElement('media:rating');
            $r->nodeValue = $value;
            if (!empty($scheme)) {
                $r->setAttribute('scheme', $scheme);
            }
            $root->appendChild($r);
        }

        $this->_called = true;
    }

    /**
     * add a title element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setTitle(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['title'])) {
            return;
        }

        $t = $dom->createElement('media:title');
        $t->appendChild($dom->createCDATASection($this->_data['title']));
        $t->setAttribute('type', 'html');
        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * set description element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setDescription(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['description'])) {
            return;
        }

        $t = $dom->createElement('media:description');
        $t->appendChild($dom->createCDATASection($this->_data['description']));
        $t->setAttribute('type', 'html');
        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * add a keywords element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setKeywords(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['keywords'])) {
            return;
        }

        $t = $dom->createElement('media:keywords');
        $t->appendChild($dom->createCDATASection($this->_data['keywords']));
        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * Add thumbnail elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addThumbnails(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['thumbnail'])) {
            return;
        }

        foreach ($this->_data['thumbnail'] as $thumb) {
            $t = $dom->createElement('media:thumbnail');
            foreach ($thumb as $key => $value) {
                $t->setAttribute($key, $value);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * add category elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addCategories(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['category'])) {
            return;
        }

        foreach ($this->_data['category'] as $cat) {
            $t = $dom->createElement('media:category');
            $t->appendChild($dom->createCDATASection($cat['category']));
            unset($cat['category']);
            foreach ($cat as $key => $value) {
                $t->setAttribute($key, $value);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * add hash elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addHashes(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['hash'])) {
            return;
        }

        foreach ($this->_data['hash'] as $algo => $hash) {
            $t = $dom->createElement('media:hash');
            $t->nodeValue = $hash;
            if (!empty($algo)) {
                $t->setAttribute('algo', $algo);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * add player element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setPlayer(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['player'])) {
            return;
        }

        $t = $dom->createElement('media:player');
        foreach ($this->_data['player'] as $key => $value) {
            $t->setAttribute($key, $value);
        }
        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * add credit elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addCredits(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['credit'])) {
            return;
        }

        foreach ($this->_data['credit'] as $c) {
            $t = $dom->createElement('media:credit');
            $t->appendChild($dom->createCDATASection($c['entity']));
            if (!empty($c['role'])) {
                $t->setAttribute('role', $c['role']);
            }
            if (!empty($c['scheme'])) {
                $t->setAttribute('scheme', $scheme);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * set the copyright element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setCopyright(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['copyright'])) {
            return;
        }

        $t = $dom->createElement('media:copyright');
        $t->appendChild($dom->createCDATASection($this->_data['copyright']['value']));
        if (empty($this->_data['copyright']['url'])) {
            $t->setAttribute('url', $this->_data['copyright']['url']);
        }
        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * add text elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addTexts(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['text'])) {
            return;
        }

        foreach ($this->_data['text'] as $txt) {
            $t = $dom->createElement('media:text');
            $t->appendChild($dom->createCDATASection($txt['text']));
            unset($txt['text']);
            foreach ($txt as $key => $value) {
                $t->setAttribute($key, $value);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * add restriction elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addRestrictions(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['restriction'])) {
            return;
        }

        foreach ($this->_data['restriction'] as $type =>$r) {
            $t = $dom->createElement('media:restriction');
            if (isset($r['value'])) {
                $t->appendChild($dom->createCDATASection($r['value']));
            }
            unset($r['value']);
            if ($type !== null) {
                $t->setAttribute('type', $type);
            }
            foreach ($r as $key => $value) {
                $t->setAttribute($key, $value);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * add community elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addCommunity(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['starRating']) 
            && empty($this->_data['statistics'])
            && empty($this->_data['tags'])
           ) {
            return;
        }
        $t = $dom->createElement('media:community');

        if (!empty($this->_data['starRating'])) {
            $r = $dom->createElement('media:starRating');
            foreach ($this->_data['starRating'] as $key => $value) {
                $r->setAttribute($key, $value);
            }
            $t->appendChild($r);
        }
        if (!empty($this->_data['statistics'])) {
            $r = $dom->createElement('media:statistics');
            foreach ($this->_data['statistics'] as $key => $value) {
                $r->setAttribute($key, $value);
            }
            $t->appendChild($r);
        }
        if (!empty($this->_data['tags'])) {
            $r = $dom->createElement('media:tags');
            $r->appendChild($dom->createCDATASection($this->_data['tags']));
            $t->appendChild($r);
        }

        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * add comments elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addComments(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['comment'])) {
            return;
        }

        $cts = $dom->createElement('media:comments');

        foreach ($this->_data['comment'] as $c) {
            $t = $dom->createElement('media:comment');
            $t->appendChild($dom->createCDATASection($c));
            $cts->appendChild($t);
        }
        $root->appendChild($cts);

        $this->_called = true;
    }

    /**
     * add embed element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setEmbed(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['embed'])) {
            return;
        }

        $e = $dom->createElement('media:embed');
        $e->setAttribute('url', $this->_data['embed']['url']);
        if (!empty($this->_data['embed']['height'])) {
            $e->setAttribute('height', $this->_data['embed']['height']);
        }
        if (!empty($this->_data['embed']['width'])) {
            $e->setAttribute('width', $this->_data['embed']['width']);
        }
        if (!empty($this->_data['embed']['param'])) {
            foreach ($this->_data['embed']['param'] as $key => $value) {
                $p = $dom->createElement('media:param');
                $p->setAttribute('name', $key);
                $p->appendChild($dom->createCDATASection($value));
                $e->appendChild($p);
            }
        }
        $root->appendChild($e);

        $this->_called = true;
    }

    /**
     * add responses elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addResponses(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['response'])) {
            return;
        }

        $cts = $dom->createElement('media:responses');

        foreach ($this->_data['response'] as $c) {
            $t = $dom->createElement('media:response');
            $t->appendChild($dom->createCDATASection($c));
            $cts->appendChild($t);
        }
        $root->appendChild($cts);

        $this->_called = true;
    }

    /**
     * add backlinks elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addBackLinks(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['backLink'])) {
            return;
        }

        $cts = $dom->createElement('media:backLinks');

        foreach ($this->_data['backLink'] as $c) {
            $t = $dom->createElement('media:backLink');
            $t->nodeValue = $c;
            $cts->appendChild($t);
        }
        $root->appendChild($cts);

        $this->_called = true;
    }

    /**
     * set status element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setStatus(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['status'])) {
            return;
        }

        $t = $dom->createElement('media:status');
        $t->setAttribute('state', $this->_data['status']['state']);
        if (empty($this->_data['status']['reason'])) {
            $t->setAttribute('reason', $this->_data['status']['reason']);
        }
        $root->appendChild($t);

        $this->_called = true;
    }

    /**
     * add price elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addPrices(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['price'])) {
            return;
        }

        foreach ($this->_data['price'] as $p) {
            $t = $dom->createElement('media:price');
            foreach ($p as $key => $value) {
                $t->setAttribute($key, $value);
            }
            $root->appendChild($t);
        }

        $this->_called = true;
    }

    /**
     * set license element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setLicense(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['license'])) {
            return;
        }
        $l = $dom->createElement('media:license');
        $l->setAttribute('type', $this->_data['license']['type']);
        $l->setAttribute('href', $this->_data['license']['href']);
        $l->appendChild($dom->createCDATASection($this->_data['license']['value']));
        $root->appendChild($l);

        $this->_called = true;
    }

    /**
     * add subtitles elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addSubTitles(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['subTitle'])) {
            return;
        }

        foreach ($this->_data['subTitle'] as $s) {
            $l = $dom->createElement('media:subTitle');
            $l->setAttribute('type', $s['type']);
            $l->setAttribute('href', $s['href']);
            $l->setAttribute('lang', $s['lang']);
            $root->appendChild($l);
        }

        $this->_called = true;
    }

    /**
     * set peerlink element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addPeerLinks(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['peerLink'])) {
            return;
        }
        foreach ($this->_date['peerLink'] as $link) {
            $l = $dom->createElement('media:peerLink');
            $l->setAttribute('type', $link['type']);
            $l->setAttribute('href', $link['href']);
            $root->appendChild($l);
        }

        $this->_called = true;
    }

    /**
     * add geodata locations
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addLocations(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['location'])) {
            return;
        }

        foreach ($this->_data['location'] as $loc) {
            $l = $dom->createElement('media:location');
            $l->setAttribute('description', $loc['description']);
            if (!empty($loc['start'])) {
                $l->setAttribute('start', $loc['start']);
            }
            if (!empty($loc['end'])) {
                $l->setAttribute('end', $loc['end']);
            }
            //todo render georss node

            $root->appendChild($l);
        }

        $this->_called = true;
    }

    /**
     * set rights element
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _setRights(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['rights'])) {
            return;
        }

        $l = $dom->createElement('media:rights');
        $l->setAttribute('status', $this->_data['rights']);
        $root->appendChild($l);

        $this->_called = true;
    }

    /**
     * set scenes elements
     * 
     * @param DOMDocument $dom 
     * @param DOMElement $root 
     * @return void
     */
    protected function _addScenes(DOMDocument $dom, DOMElement $root)
    {
        if (empty($this->_data['scene'])) {
            return;
        }

        $scenes = $dom->createElement('media:scenes');
        foreach ($this->_data['scene'] as $s) {
            $e = $dom->createElement('media:scene');
            foreach ($s as $key => $value) {
                $p = $dom->createElement('media:'.$key);
                $p->appendChild($dom->createCDATASection($value)); 
                $e->appendChild($p);
            }
            $scenes->appendChild($e);
        }
        $root->appendChild($scenes);

        $this->_called = true;
    }

}
