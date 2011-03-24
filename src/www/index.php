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
 * HalfPipe Bootstrap
 *
 * @author    Geoffrey Tran
 * @category  CoreVideo
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

/**
 * Project Path
 *
 * Path to the basedir of the project
 */
if (!defined('PATH_PROJECT')) {
        define('PATH_PROJECT', dirname(dirname(__FILE__)) . '/');
}

/**
 * Include paths
 */
include_once PATH_PROJECT . 'config/paths.php';

/**
 * @see Zend_Application
 */
require_once 'Zend/Application.php';

// Environment
$environment = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'production';

// Application configuration
$applicationConfig = PATH_PROJECT . 'config/application.xml';

// Initialize application
$application = new Zend_Application($environment, $applicationConfig);
$application->bootstrap()
            ->run();
