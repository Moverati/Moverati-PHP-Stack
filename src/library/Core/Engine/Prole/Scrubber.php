<?php

namespace Core\Engine\Prole;

/**
 * Normalizes & Scrubs numeric RFID tags to a standard format
 *
 * @author Josh Team
 */
class Scrubber
{
    /**
     * Scrubs & Normalizes the RFID
     *
     * @param string|int $rfid the RFID to scrub
     * @return string $rfid;
     */
    static public function scrub($rfid, $length=24)
    {
        $rfid = trim($rfid);
        
        if(empty($rfid)) {
            return null;
        }

        //Standardize for tags e.g. (e2003412dc03011815003452 to 003452)
        if(strtolower(substr($rfid, 0, 4)) == 'e200') {
            $rfid = substr($rfid, -6);
        }

        $number = $rfid;


//        $tag = str_ireplace('o', 0, $rfid);

        if (\is_numeric($number)) {
            $number = str_pad((int)$number, $length, 0, STR_PAD_LEFT);
        }
        
        return $number;
    }

    static public function getNumericValue($rfid)
    {
        $tag = self::scrub($rfid);
        return (int) $rfid;
    }

}
