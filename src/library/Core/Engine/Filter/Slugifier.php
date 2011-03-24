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

namespace Core\Engine\Filter;

/**
 * Slugify a string
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Slugifier implements \Zend_Filter_Interface
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        return self::Slugify($value);
    }

    /**
     * Generate a slug for a given string
     *
     * Note that it requires mbstring and an input string in
     * unicode (utf-8) encoding. Also note that the resulting
     * string is encoded in unicode (again)
     *
     * Credit to madoqua
     *
     * @param string $string
     * @return string
     */
    public static function slugify($string) {
        $string = utf8_decode($string);
        $string = htmlentities($string);
        $string = strtolower($string);

        // Convert Umlauts
        $string = preg_replace("/&(.)(uml);/", "$1e", $string);

        // Wierdos
        $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);

        // Spaces
        $string = str_replace(' ', '-', html_entity_decode($string));
        
        // Remove funky characters
        $string = preg_replace("/([^a-z0-9\-]+)/", "", html_entity_decode($string));

        // Trim the result (so it doesn't start/end with "-")
        $string = trim($string, "-");

        return utf8_encode($string);
    }
}