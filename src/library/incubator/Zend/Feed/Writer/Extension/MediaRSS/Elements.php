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
 * Zend Feed Writer Extension for MediaRSS Optional elements
 *
 * contains methods for the mediarss optional elements that can live in many places
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
class Zend_Feed_Writer_Extension_MediaRSS_Elements
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
     * Set the feed character encoding
     *
     * @return void
     */
    public function setEncoding($encoding)
    {
        if (empty($encoding) || !is_string($encoding)) {
            // require_once 'Zend/Feed/Exception.php';
            throw new Zend_Feed_Exception('Invalid parameter: parameter must be a non-empty string');
        }
        $this->_data['encoding'] = $encoding;
    }

    /**
     * Get the feed character encoding
     *
     * @return string|null
     */
    public function getEncoding()
    {
        if (!array_key_exists('encoding', $this->_data)) {
            return 'UTF-8';
        }
        return $this->_data['encoding'];
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
        if (!array_key_exists($point, $this->_data) || empty($this->_data[$point])) {
            return null;
        }
        return $this->_data[$point];
    }

    /**
     * Media rating scheme simple types (simple being default scheme)
     *
     * @var array
     */
    public static $rating_scheme_simple_types = array('adult','nonadult');

    /**
     * This allows the permissible audience to be declared. If this element 
     * is not included, it assumes that no restrictions are necessary. 
     * It has one optional attribute.  
     *
     * @param string $value 
     * @param string $scheme the URI that identifies the rating scheme. 
     *      It is an optional attribute. If this attribute is not included, 
     *      the default scheme is urn:simple (adult | nonadult)
     * @return void
     */
    public function addMediaRating($value, $scheme=null) 
    {
        if ($scheme === 'urn:simple' || is_null($scheme)) {
            if (!in_array($value, self::$rating_scheme_simple_types)) {
                throw new Zend_Feed_Exception('simple rating not valid');
            }
        }
        //todo validate scheme
        if ($value === '') {
            throw new Zend_Feed_Exception('rating must not be empty');
        }
        $this->_data['rating'][$scheme] = $value;
    }

    /**
     * add many media ratings at once 
     * 
     * @param array $vals each represented as $scheme => $rating
     * @return void
     */
    public function addMediaRatings(array $vals) 
    {
        foreach ($vals as $scheme => $rating) {
            $this->addMediaRating($rating, $scheme);
        }
    }

    /**
     * The title of the particular media object 
     * 
     * @param string $value 
     * @return void
     */
    public function setMediaTitle($value) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('title must not be empty');
        }
        $this->_data['title'] = $value;
    }

    /**
     * Short description describing the media object typically a sentence in length 
     * 
     * @param string $value 
     * @return void
     */
    public function setMediaDescription($value) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('description must not be empty');
        }
        $this->_data['description'] = $value;
    }

    /**
     * Highly relevant keywords describing the media object with 
     * typically a maximum of ten words. 
     * 
     * @param string|array $value 
     * @return void
     */
    public function setMediaKeywords($value) 
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        if ($value === '') {
            throw new Zend_Feed_Exception('keywords must not be empty');
        }
        $this->_data['keywords'] = $value;
    }

    /**
     * Allows particular images to be used as representative images for the 
     * media object. If multiple thumbnails are included, and time coding is 
     * not at play, it is assumed that the images are in order of importance. 
     * 
     * @param string $value the url of the thumbnail
     * @param int $height the height of the thumbnail. It is an optional attribute.
     * @param int $width the width of the thumbnail. It is an optional attribute
     * @param string $time the time offset in relation to the media object. 
     *      Typically this is used when creating multiple keyframes within a 
     *      single video. The format for this attribute should be in the DSM-CC's 
     *      Normal Play Time (NTP) as used in RTSP [RFC 2326 3.6 Normal Play Time]. 
     *      It is an optional attribute
     * @return void
     */
    public function addMediaThumbnail($value, $height=null, $width=null, $time=null) 
    {
        if (!Zend_Uri::check($value)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $thumb = array('url' => $value);

        if (!is_null($height)) {
            if (!is_int($height)) {
                throw new Zend_Feed_Exception('height must be int');
            }
            $thumb['height'] = $height;
        }
        if (!is_null($width)) {
            if (!is_int($width)) {
                throw new Zend_Feed_Exception('width must be int');
            }
            $thumb['width'] = $width;
        }
        if (!is_null($time)) {
            //todo validate NTP
            $thumb['time'] = $time;
        }

        $this->_data['thumbnail'][] = $thumb;
    }

    /**
     * add many thumbnails at once 
     * 
     * @param array $thumbs array of thumbs to add. thumbs are each represented as
     *      an array('url'=>$url, 'height'=>$height, 'width'=>$width, 'time'=>$time)
     * @return void
     */
    public function addMediaThumbnails(array $thumbs) 
    {
        foreach ($thumbs as $th) {
            $height = (empty($th['height'])) ? null : $th['height'];
            $width = (empty($th['width'])) ? null : $th['width'];
            $time = (empty($th['time'])) ? null : $th['time'];
            $this->addMediaThumbnail($th['url'], $height, $width, $time);
        }
    }

    /**
     * Allows a taxonomy to be set that gives an indication of the 
     * type of media content, and its particular contents 
     * 
     * @param string $value 
     * @param string $scheme the URI that identifies the categorization scheme. 
     *      It is an optional attribute. If this attribute is not included, 
     *      the default scheme is 'http://search.yahoo.com/mrss/category_schema'.
     * @param string $label the human readable label that can be displayed in 
     *      end user applications. It is an optional attribute.
     * @return void
     */
    public function addMediaCategory($value, $scheme = null, $label = null) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('category must not be empty');
        }
        $cat = array('category' => $value);
        if (!is_null($scheme)) {
            //todo validate scheme
            $cat['scheme'] = $scheme;
        }
        if (!is_null($label)) {
            $cat['label'] = $label;
        }

        $this->_data['category'][] = $cat;
    }

    /**
     * add many categories at once 
     * 
     * @param array $cats array of categories to add. categories are each represented as
     *      an array('category'=>$cat, 'scheme'=>$scheme, 'label'=>$label)
     * @return void
     */
    public function addMediaCategories(array $cats) 
    {
        foreach ($cats as $c) {
            $scheme = (empty($c['scheme'])) ? null : $c['scheme'];
            $label = (empty($c['label'])) ? null : $c['label'];
            $this->addMediaCategory($c['category'], $scheme, $label);
        }
    }

    /**
     * This is the hash of the binary media file. It can appear multiple times 
     * as long as each instance is a different algo 
     * 
     * @param string $value hash value
     * @param mixed $algo hash algorithm (one of md5, sha1)
     * @return void
     */
    public function addMediaHash($value, $algo=null) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('hash must not be empty');
        }
        //todo validate hash (len, chars)?
        //todo validate algo?
        $this->_data['hash'][$algo] = $value;
    }

    /**
     * add many media hashes at once 
     * 
     * @param array $hashes array of hashes to add represented by an
     *      array($algo => $hash)
     * @return void
     */
    public function addMediaHashes(array $hashes) 
    {
        foreach ($hashes as $algo => $value) {
            $this->addMediaHash($value, $algo);
        }
    }

    /**
     * Allows the media object to be accessed through a web browser media 
     * player console. This element is required only if a direct media url 
     * attribute is not specified in the <media:content> element 
     * 
     * @param string $value the url of the player console that plays the media. 
     * @param int $height the height of the browser window that the url should be opened in. 
     *      It is an optional attribute.
     * @param int $width the width of the browser window that the url should be opened in. 
     *      It is an optional attribute.
     * @return void
     */
    public function setMediaPlayer($value, $height = null, $width = null) 
    {
        if (!Zend_Uri::check($value)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $player = array('url' => $value);

        if (!is_null($height)) {
            if (!is_int($height)) {
                throw new Zend_Feed_Exception('height must be int');
            }
            $player['height'] = $height;
        }
        if (!is_null($width)) {
            if (!is_int($width)) {
                throw new Zend_Feed_Exception('width must be int');
            }
            $player['width'] = $width;
        }
        $this->_data['player'] = $player;
    }

    /**
     * media credit schemes
     *
     * @var array
     */
    public static $credit_schemes = array('urn:ebu','urn:yvs');

    /**
     * media credit role 'urn:yvs' possible values
     *
     * @var array
     */
    public static $credit_role_yvs = array('uploader','owner');

    /**
     * Notable entity and the contribution to the creation of the media object. 
     * Current entities can include people, companies, locations, etc. Specific 
     * entities can have multiple roles, and several entities can have the same role. 
     * These should appear as distinct <media:credit> elements. 
     * 
     * @param string $value 
     * @param string $role role specifies the role the entity played. 
     *      It is an optional attribute.
     * @param string $scheme scheme is the URI that identifies the role scheme. 
     *      It is an optional attribute and possible values for this attribute are 
     *      ( urn:ebu | urn:yvs ) . The default scheme is 'urn:ebu'. The list of 
     *      roles supported under urn:ebu scheme can be found at: 
     *      http://www.ebu.ch/en/technical/metadata/specifications/role_codes.php
     *      The roles supported under urn:yvs scheme are ( uploader | owner )
     * @return void
     */
    public function addMediaCredit($value, $role=null, $scheme=null) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('entity must not be empty');
        }
        $credit = array('entity' => $value);
        if (!is_null($role)) {
            if ($scheme === 'urn:yvs' && !in_array($role, self::$credit_role_yvs)) {
                throw new Zend_Feed_Exception('unsupported yvs role');
            }
            //todo validate urn:ebu
            $credit['role'] = $role;
        }
        if (!is_null($scheme)) {
            if (!in_array($scheme, self::$credit_schemes)) {
                throw new Zend_Feed_Exception('scheme unsupported');
            }
            $credit['scheme'] = $scheme;
        }
        $this->_data['credit'][] = $credit;
    }

    /**
     * add many media credits at once
     * 
     * @param array $credits array of credits represented as an
     *      array('name'=>$name, 'role'=>$role, 'scheme'=>$scheme)
     * @return void
     */
    public function addMediaCredits(array $credits) 
    {
        foreach ($credits as $c) {
            $role = (empty($c['role'])) ? null : $c['role'];
            $scheme = (empty($c['scheme'])) ? null : $c['scheme'];
            $this->addMediaCredit($c['entity'], $role, $scheme);
        }
    }

    /**
     * Copyright information for media object. 
     * 
     * @param string $value 
     * @param string $url the url for a terms of use page or additional copyright 
     *      information. If the media is operating under a Creative Commons license, 
     *      the Creative Commons module should be used instead. It is an optional attribute.
     * @return void
     */
    public function setMediaCopyright($value, $url=null) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('copyright value must not be empty');
        }
        $copy = array('value' => $value);
        if (!is_null($url)) {
            if (!Zend_Uri::check($url)) {
                throw new Zend_Feed_Exception('invalid url');
            }
            $copy['url'] = $url;
        }
        $this->_data['copyright'] = $copy;
    }

    /**
     * Allows the inclusion of a text transcript, closed captioning, or 
     * lyrics of the media content. Many of these elements are permitted 
     * to provide a time series of text. In such cases, it is encouraged, 
     * but not required, that the elements be grouped by language and appear 
     * in time sequence order based on the start time. Elements can have 
     * overlapping start and end times. 
     * 
     * @param string $value 
     * @param string $lang the primary language encapsulated in the media object. 
     *      Language codes possible are detailed in RFC 3066. This attribute is 
     *      used similar to the xml:lang attribute detailed in the XML 1.0 
     *      Specification (Third Edition). It is an optional attribute.
     * @param string $start the start time offset that the text starts being 
     *      relevant to the media object. An example of this would be for closed 
     *      captioning. It uses the NTP time code format (see: the time attribute 
     *      used in <media:thumbnail>).   It is an optional attribute.
     * @param string $end the end time that the text is relevant. If this attribute 
     *      is not provided, and a start time is used, it is expected that the end 
     *      time is either the end of the clip or the start of the next <media:text> element.
     * @return void
     */
    public function addMediaText($value, $lang=null, $start = null, $end = null) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('text must not be empty');
        }
        $txt = array('text' => $value);
        if (!is_null($lang)) {
            //todo validate lang
            $txt['lang'] = $lang;
        }
        if (!is_null($start)) {
            //todo validate ntp
            $txt['start'] = $start;
        }
        if (!is_null($end)) {
            //todo validate ntp
            $txt['end'] = $end;
        }
        $this->_data['text'][] = $txt;
    }

    /**
     * add many media texts at once
     * 
     * @param array $texts array of text entries represented with an
     *      array('text'=>$text, 'lang'=>$lang, 'start'=>$start, 'end'=>$end)
     * @return void
     */
    public function addMediaTexts(array $texts) 
    {
        foreach ($texts as $t) {
            $lang = (empty($t['lang'])) ? null : $t['lang'];
            $start = (empty($t['start'])) ? null : $t['start'];
            $end = (empty($t['end'])) ? null : $t['end'];
            $this->addMediaText($t['text'], $lang, $start, $end);
        }
    }

    /**
     * media restriction relationships
     *
     * @var array
     */
    public static $restriction_relationships = array('allow','deny');

    /**
     * media restriction types
     *
     * @var array
     */
    public static $restriction_types = array('country','uri','sharing');

    /**
     * Allows restrictions to be placed on the aggregator rendering the 
     * media in the feed. Currently, restrictions are based on distributor 
     * (uri), country codes and sharing of a media object. This element is 
     * purely informational and no obligation can be assumed or implied. Only 
     * one <media:restriction> element of the same type can be applied to a media 
     * object - all others will be ignored. Entities in this element should be space 
     * separated. To allow the producer to explicitly declare his/her intentions, 
     * two literals are reserved: 'all', 'none'. These literals can only be used once. 
     * 
     * @param string $value 
     * @param string $relationship ndicates the type of relationship that 
     *      the restriction represents (allow | deny).
     * @param string $type the type of restriction (country | uri | sharing ) 
     *      that the media can be syndicated. It is an optional attribute; 
     *      however can only be excluded when using one of the literal 
     *      values "all" or "none".
     *      * "country" allows restrictions to be placed based on 
     *          country code. [ISO 3166]
     *      * "uri" allows restrictions based on URI. 
     *          Examples: urn:apple, http://images.google.com, urn:yahoo, etc.
     *      * "sharing" allows restriction on sharing.deny means content 
     *          cannot be shared - e.g. via embed tags. If the sharing type 
     *          is not present, the default functionality is to allow sharing
     * @return void
     */
    public function addMediaRestriction($relationship, $type, $value=null) 
    {
        if (!in_array($relationship, self::$restriction_relationships)) {
            throw new Zend_Feed_Exception('relationship unsupported');
        }
        $rel = array('relationship' => $relationship);
        if (!is_null($value)) {
            $rel['value'] = $value;
        }
        if (!is_null($type)) {
            if (!in_array($type, self::$restriction_types)) {
                throw new Zend_Feed_Exception('type unsupported');
            }
        } else {
            if (!in_array($value, array('all', 'none'))) {
                throw new Zend_Feed_Exception('value must be all or none if no type');
            }
        }

        $this->_data['restriction'][$type] = $rel;
    }

    /**
     * add many media restrictions at once 
     * 
     * @param array $restricts array of restrictions represented by an
     *      array('relationship'=>$relationship, 'type'=>$type, 'value'=>$value)
     * @return void
     */
    public function addMediaRestrictions(array $restricts) 
    {
        foreach ($restricts as $type => $r) {
            $value = (empty($r['value'])) ? null : $r['value'];
            $type = (empty($r['type'])) ? null : $r['type'];
            $this->addMediaRestriction($r['relationship'], $type, $value);
        }
    }

    /**
     * specifies the rating related information about a media object.  
     * 
     * @param float $avg 
     * @param int $count 
     * @param int $min 
     * @param int $max 
     * @return void
     */
    public function setMediaStarRating($avg, $count=null, $min=null, $max=null) 
    {
        if (!is_numeric($avg)) {
            throw new Zend_Feed_Exception('avg should be float');
        }
        $star = array('average' => $avg);
        if (!is_null($count)) {
            if (!is_int($count)) {
                throw new Zend_Feed_Exception('count should be int');
            }
            $star['count'] = $count;
        }
        if (!is_null($min)) {
            if (!is_int($min)) {
                throw new Zend_Feed_Exception('min should be int');
            }
            $star['min'] = $min;
        }
        if (!is_null($max)) {
            if (!is_int($max)) {
                throw new Zend_Feed_Exception('max should be int');
            }
            $star['max'] = $max;
        }
        $this->_data['starRating'] = $star;
    }

    /**
     * specifies various statistics about a media object like 
     * the view count and the favorite count. 
     * 
     * @param int $views 
     * @param int $favorites 
     * @return void
     */
    public function setMediaStatistics($views=null, $favorites=null) 
    {
        $stat = array();
        if (!is_null($views)) {
            if (!is_int($views)) {
                throw new Zend_Feed_Exception('views should be int');
            }
            $stat['views'] = $views;
        }
        if (!is_null($favorites)) {
            if (!is_int($favorites)) {
                throw new Zend_Feed_Exception('favorites should be int');
            }
            $stat['favorites'] = $favorites;
        }
        $this->_data['statistics'] = $stat;
    }

    /**
     * contains user generated tags separated by commas in the decreasing 
     * order of each tag's weight. Each tag can be assigned an integer 
     * weight in <tag_name>:<weight> format. It's up to the provider to 
     * choose the way weight is determined for a tag, for example, number 
     * of occurence can be one way to decide weight of a particular tag. 
     * Default weight is 1. 
     * 
     * @param array $params key=tag, value=weight
     * @return void
     */
    public function setMediaTags(array $params) 
    {
        $strArray = array();
        foreach ($params as $tag => $weight) {
            //fixup non-weight items
            if (!is_numeric($weight)) {
                $params[$weight] = 1;
                unset($params[$tag]);
            }
        }
        arsort($params);
        foreach ($params as $tag => $weight) {
            if ($tag === '') {
                throw new Zend_Feed_Exception('tag should be not empty');
            }
            $strArray[] = $tag.':'.$weight;
        }
        $this->_data['tags'] = implode(',', $strArray);
    }

    /**
     * Allows inclusion of all the comments media object has received. 
     * 
     * @param string $value 
     * @return void
     */
    public function addMediaComment($value) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('comment should be not empty');
        }
        $this->_data['comment'][] = $value;
    }

    /**
     * add many media comments 
     * 
     * @param array $comments 
     * @return void
     */
    public function addMediaComments(array $comments) 
    {
        foreach ($comments as $c) {
            $this->addMediaComment($c);
        }
    }
    
    /**
     * Sometimes player specific embed code is needed for a player to play 
     * any video. <media:embed> allows inclusion of such information in the 
     * form of key value pairs 
     * 
     * @param string $value player url
     * @param int $height height in px
     * @param int $width width in px
     * @param array $params key-value params
     * @return void
     */
    public function setMediaEmbed($value, $height=null, $width=null, $params=array()) 
    {
        if (!Zend_Uri::check($value)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $embed = array('url' => $value);

        if (!is_null($height)) {
            if (!is_int($height)) {
                throw new Zend_Feed_Exception('height must be int');
            }
            $embed['height'] = $height;
        }
        if (!is_null($width)) {
            if (!is_int($width)) {
                throw new Zend_Feed_Exception('width must be int');
            }
            $embed['width'] = $width;
        }
        if (!is_null($params)) {
            $embed['param'] = $params;
        }
        $this->_data['embed'] = $embed;
    }

    /**
     * Allows inclusion of a list of all media responses a media object has received.
     * 
     * @param string $value 
     * @return void
     */
    public function addMediaResponse($value) 
    {
        if ($value === '') {
            throw new Zend_Feed_Exception('response should be not empty');
        }
        $this->_data['response'][] = $value;
    }

    /**
     * add many media responses at once 
     * 
     * @param array $responses array of responses
     * @return void
     */
    public function addMediaResponses(array $responses) 
    {
        foreach ($responses as $r) {
            $this->addMediaResponse($r);
        }
    }

    /**
     * Allows inclusion of all the urls pointing to a media object. 
     * 
     * @param int $value 
     * @return void
     */
    public function addMediaBackLink($value) 
    {
        if (!Zend_Uri::check($value)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $this->_data['backLink'][] = $value;
    }

    public function addMediaBackLinks(array $links) 
    {
        foreach ($links as $l) {
            $this->addMediaBackLink($l);
        }
    }

    /**
     * media status states
     *
     * @var array
     */
    public static $status_states = array('active','blocked','deleted');

    /**
     * specify the status of a media object - whether it's still active 
     * or it has been blocked/deleted. 
     * 
     * @param string $value can have values "active", "blocked" or "deleted". 
     *      "active" means a media object is active in the system, 
     *      "blocked" means a media object is blocked by the publisher, 
     *      "deleted" means a media object has been deleted by the publisher.
     * @param string $reason a reason explaining why a media object has been 
     *      blocked/deleted. It can be plain text or a url.
     * @return void
     */
    public function setMediaStatus($value, $reason=null) 
    {
        if (!in_array($value, self::$status_states)) {
            throw new Zend_Feed_Exception('unsupported state');
        }
        $status = array('state' => $value);
        if (!is_null($reason)) {
            $status['reason'] = $reason;
        }
        $this->_data['status'] = $status;
    }

    public static $price_types = array('rent', 'purchase', 'package', 'subscription');
    /**
     * tag to include pricing information about a media object. If this 
     * tag is not present, the media object is supposed to be free. 
     * One media object can have multiple instances of this tag for including 
     * different pricing structures. The presence of this tag would mean that 
     * media object is not free. 
     * 
     * @param string $type Valid values are "rent", "purchase", "package" or "subscription".
     *      If nothing is specified, then the media is free.
     * @param string $info if the type is "package" or "subscription", then info 
     *      is a url ponting to package or subscription information. 
     *      This is an optional attribute.
     * @param float $price the price of the media object. This is an optional attribute.
     * @param string $currency use [ISO 4217] for currency codes. 
     *      This is an optional attribute.
     * @return void
     */
    public function addMediaPrice($type=null, $info=null, $value=null, $currency=null) 
    {
        $price = array();
        if (!is_null($type)) {
            if (!in_array($type, self::$price_types)) {
                throw new Zend_Feed_Exception('invalid price type');
            }
            $price['type'] = $type;
        }
        if (!is_null($info)) {
            if (!Zend_Uri::check($info)) {
                throw new Zend_Feed_Exception('invalid url');
            }
            $price['info'] = $info;
        }
        if (!is_null($value)) {
            if (!is_numeric($value)) {
                throw new Zend_Feed_Exception('price must be float: '.$value);
            }
            $price['price'] = $value;
        }
        if (!is_null($currency)) {
            //todo validate currency
            $price['currency'] = $currency;
        }
        $this->_data['price'][] = $price;
    }

    /**
     * add many media prices at once
     * 
     * @param array $prices array of price elements represented by an
     *      array('type'=>$type, '$info'=>$info, 'price'=>$price, 'currency'=>$currency)
     * @return void
     */
    public function addMediaPrices(array $prices) 
    {
        foreach ($prices as $p) {
            $type = (empty($p['type'])) ? null : $p['type'];
            $info = (empty($p['info'])) ? null : $p['info'];
            $price = (empty($p['price'])) ? null : $p['price'];
            $currency = (empty($p['currency'])) ? null : $p['currency'];
            $this->addMediaPrice($type, $info, $price, $currency);
        }
    }

    /**
     * link to specify the machine readable license associated with the content. 
     * 
     * @param string $value 
     * @param string $href 
     * @param string $type 
     * @return void
     */
    public function setMediaLicense($value, $href, $type) 
    {
        //todo validate mime type
        if (!Zend_Uri::check($href)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $this->_data['license'] = array('value' => $value,
                                        'href' => $href,
                                        'type' => $type);
    }

    /**
     * element for subtitle/CC link. It contains type and language attributes. 
     * Language is based on RFC 3066. There can be more than one such tag per 
     * media element e.g. one per language. Please refer to Timed Text spec - 
     * W3C for more information on Timed Text and Real Time Subtitling 
     * 
     * @param string $type 
     * @param string $lang 
     * @param string $href 
     * @return void
     */
    public function addMediaSubTitle($type, $lang, $href) 
    {
        //todo validate mime type
        //todo validate lang
        if (!Zend_Uri::check($href)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $this->_data['subTitle'][] = array('type' => $type,
                                           'lang' => $lang,
                                           'href' => $href);
    }

    /**
     * add many media subtitle links at once 
     * 
     * @param array $subs array of subtitle entries represented as an
     *      array('type'=>$type, 'lang'=>$lang, 'href'=>$href)
     * @return void
     */
    public function addMediaSubTitles(array $subs) 
    {
        foreach ($subs as $s) {
            $this->addMediaSubTitle($s['type'], $s['lang'], $s['href']);
        }
    }

    /**
     * element for P2P link. 
     * 
     * @param string $type 
     * @param string $href 
     * @return void
     */
    public function addMediaPeerLink($type, $href) 
    {
        //todo validate mime type
        if (!Zend_Uri::check($href)) {
            throw new Zend_Feed_Exception('invalid url');
        }
        $this->_data['peerLink'][] = array('href' => $href,
                                        'type' => $type);

    }

    /**
     * add many peer links at once 
     * 
     * @param array $links as array('type'=>$type, 'href'=>$href);
     * @return void
     */
    public function addMediaPeerLinks(array $links)
    {
        foreach ($links as $l) {
            $this->addMediaPeerLink($l['type'], $l['href']);
        }
    }

    /**
     * element to specify geographical information about various 
     * locations captured in the content of a media object. 
     * The format conforms to geoRSS. 
     * 
     * @param string $value description of the place whose location is being specified.
     * @param string $start time at which the reference to a particular location 
     *      starts in the media object.
     * @param string $end time at which the reference to a particular location 
     *      ends in the media object.
     * @param mixed $params //todo georss element
     * @return void
     */
    public function addMediaLocation($value, $geo, $start=null, $end=null) 
    {
        //todo validate geo
        $loc = array('description' => $value,
                     'georss' => $geo);
        if (!is_null($start)) {
            //todo validate ntp
            $loc['start'] = $start;
        }
        if (!is_null($end)) {
            //todo validate ntp
            $loc['end'] = $end;
        }
        $this->_data['location'][] = $loc;
    }

    /**
     * media right status
     *
     * @var array
     */
    public static $rights_status = array('userCreated','official');

    /**
     * element to specify the rights information of a media object. 
     * 
     * @param string $value the status of the media object saying whether 
     *      a media object has been created by the publisher or they have 
     *      rights to circulate it. Supported values are "userCreated" and "official".
     * @return void
     */
    public function setMediaRights($value) 
    {
        if (!in_array($value, self::$rights_status)) {
            throw new Zend_Feed_Exception('unsupported status');
        }
        $this->_data['rights'] = $value;
    }

    /**
     * element to specify various scenes within a media object. It can 
     * have multiple child <media:scene> elements, where each <media:scene>
     * element contains information about a particular scene. <media:scene> 
     * has optional sub-elements as "sceneTitle","sceneDescription", 
     * "sceneStartTime" and "sceneEndTime", which contains title, description, 
     * start and end time of a particular scene in the media respectively. 
     * 
     * @param string $title 
     * @param string $description 
     * @param string $start 
     * @param string $end 
     * @return void
     */
    public function addMediaScene($title=null, $description=null, $start=null, $end=null) 
    {
        $scene = array();
        if (!is_null($title)) {
            $scene['title'] = $title;
        }
        if (!is_null($description)) {
            $scene['description'] = $description;
        }
        if (!is_null($start)) {
            //todo validate ntp
            $scene['start'] = $start;
        }
        if (!is_null($end)) {
            //todo validate ntp
            $scene['end'] = $end;
        }
        $this->_data['scene'][] = $scene;
    }

    /**
     * add many media scenes at once
     * 
     * @param array $scenes represented as an
     *      array('title'=>$title, 'description'=>$description, 'start'=>$start, 'end'=>$end)
     * @return void
     */
    public function addMediaScenes(array $scenes) 
    {
        foreach ($scenes as $s) {
            $title = (empty($s['title'])) ? null : $s['title'];
            $description = (empty($s['description'])) ? null : $s['description'];
            $start = (empty($s['start'])) ? null : $s['start'];
            $end = (empty($s['end'])) ? null : $s['end'];
            $this->addMediaScene($title, $description, $start, $end);
        }
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

    /**
     * lets us know if this object has any elements
     * 
     * @return bool
     */
    public function isEmpty() 
    {
        return empty($this->_data);
    }

}
