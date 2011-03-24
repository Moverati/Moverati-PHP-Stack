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

namespace Core\Engine\View\Helper;

use Core\Engine,
    Core\Engine\View;

/**
 * Formats a date as the time since that date (e.g., â€œ4 weeksâ€�).
 *
 * This is useful for creating "Last updated 5 week and 4 days ago" strings
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TimeSince extends HelperAbstract
{
    /**
     * Intervals in code => name format
     *
     * @var array
     */
    protected $intervals = array(
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    /**
     * Formats a date as the time since that date (e.g., â€œ4 weeks agoâ€�).
     *
     * @param integer $timestamp
     * @param integer $time      Timestamp to use instead of time()
     */
    public function __invoke($timestamp, $time = null, $short = false)
    {
        $output     = '';
        $translator = $this->getView()->getHelper('translate');

        $chunks = $this->calculateChunks($timestamp, $time);

        if( empty($chunks) ) {
            $output = $translator->translate('less than a second');
        }

        $largeChunk = array_shift($chunks);
        if( $largeChunk ) {
            $largestChunk     = $largeChunk['chunk'];
            $largestChunkName = $largeChunk['name'] . (abs($largestChunk) > 1 ? 's' : '');
        }

        $smallChunk = array_shift($chunks);
        if( $smallChunk ) {
            $secondChunk     = $smallChunk['chunk'];
            $secondChunkName = $smallChunk['name'] . (abs($secondChunk) > 1 ? 's' : '');
        }

        if ($translator->getTranslator() === null) {
            if (isset($secondChunk) && !$short) {
                $output = sprintf("%d $largestChunkName and %d $secondChunkName", $largestChunk, $secondChunk);
            } else if (isset($largestChunk)) {
                $output = sprintf("%d $largestChunkName", $largestChunk);
            }
        } else {
            if (isset($secondChunk) && !$short) {
                $output = $translator->translate("%d $largestChunkName and %d $secondChunkName", $largestChunk, $secondChunk);
            } else if (isset($largestChunk)) {
                $output = $translator->translate("%d $largestChunkName", $largestChunk);
            }
        }

        return $output;
    }

    /**
     *
     * @param int $timestamp
     * @param int $time
     * @return int
     */
    protected function sign($timestamp, $time) {
        if( $timestamp <= $time )
            return 1;
        else
            return -1;
    }

    /**
     *
     * @param int $timestamp
     * @param int $time
     * @return array
     */
    protected function calculateChunks($timestamp, $time = null) {
        if ($time === null) {
            $time = time();
        }

        $sign = $this->sign($timestamp, $time);

        $timestamp = $this->dateTimeFromTimestamp($timestamp);
        $time = $this->dateTimeFromTimestamp($time);


        $diff = $time->diff($timestamp);

        $chunks = array();
        foreach( $this->intervals as $interval => $name ) {
            $amount = $diff->{$interval};

            if( $amount == 0 && count($chunks) >= 1 )
                break;
            else if( $amount == 0 )
                continue;

            if( $interval == 'd' && $amount >= 7 ) { //Weeks and Days

                $weeks = floor($amount / 7);
                $days = $amount - ($weeks * 7);

                $chunks['week'] = array(
                    'name'    => 'week',
                    'chunk'   => abs($weeks) * $sign,
                    //'seconds' => strtotime(sprintf("+%d week", abs($weeks)), 0),
                );

                if( $days == 0 )
                    continue;

                $chunks['day'] = array(
                    'name'    => 'day',
                    'chunk'   => abs($days) * $sign,
                    //'seconds' => strtotime(sprintf("+%d day", abs($days)), 0),
                );

            } else {
                $chunks[$name] = array(
                    'name'    => $name,
                    'chunk'   => abs($amount) * $sign,
                    //'seconds' => strtotime(sprintf("+%d %s", abs($amount), $name), 0),
                );
            }
        }

        return $chunks;
    }

    /**
     *
     * @param int $timestamp
     * @return \DateTime
     */
    protected function dateTimeFromTimestamp($timestamp) {
        $datetime = new \DateTime();
        $datetime->setTimestamp((int)$timestamp);

        return $datetime;
    }
}