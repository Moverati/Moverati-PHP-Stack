<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  HalfPipe
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

/**
 * Project Path
 *
 * Path to the basedir of the project
 */
if (!defined('PATH_PROJECT')) {
	define('PATH_PROJECT', dirname(dirname(dirname(__FILE__))) . '/');
}

/**
 * Include paths
 */
include_once PATH_PROJECT . 'config/paths.php';
include_once __DIR__ . '/config/paths.php';

/**
 * @see PHPUnit_Util_Filter
 */
require_once 'PHPUnit/Util/Filter.php';

/**
 * @see Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

// Whitelist files for coverage report
PHPUnit_Util_Filter::addDirectoryToWhitelist(PATH_PROJECT . 'library/Core');
PHPUnit_Util_Filter::addDirectoryToWhitelist(PATH_PROJECT . 'application/modules/default/forms');

// Autoloader
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('PHPUnit')
           ->registerNamespace('Core')
           ->registerNamespace('Doctrine')
           ->registerNamespace('DoctrineExtensions')
           ->registerNamespace('Symfony');

// Selenium Functional Test Support
$config = PHPUnit_Util_Configuration::getInstance(__DIR__ . '/../functional/selenium/phpunit.xml');
\PHPUnit_Extensions_SeleniumTestCase::$browsers = $config->getSeleniumBrowserConfiguration();