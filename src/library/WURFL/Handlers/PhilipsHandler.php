<?php
/**
 * WURFL API
 *
 * LICENSE
 *
 * This file is released under the GNU General Public License. Refer to the
 * COPYING file distributed with this package.
 *
 * Copyright (c) 2008-2009, WURFL-Pro S.r.l., Rome, Italy
 * 
 * 
 *
 * @category   WURFL
 * @package    WURFL_Handlers
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    $id$
 */

/**
 * PhilipsUserAgentHandler
 * 
 *
 * @category   WURFL
 * @package    WURFL_Handlers
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    $id$
 */
class WURFL_Handlers_PhilipsHandler extends WURFL_Handlers_Handler {
	
	function __construct($wurflContext, $userAgentNormalizer = null) {
		parent::__construct ( $wurflContext, $userAgentNormalizer );
	}
	
	/**
	 * Intercept all UAs starting with either "Philips" or "PHILIPS"
	 *
	 * @param string $userAgent
	 * @return string
	 */
	public function canHandle($userAgent) {
		return WURFL_Handlers_Utils::checkIfStartsWith ( $userAgent, "Philips" ) || WURFL_Handlers_Utils::checkIfStartsWith ( $userAgent, "PHILIPS" );
	}
	
	protected $prefix = "PHILIPS";
}
?>