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

namespace Core\Engine\Mail\Transport\Postmark\Exception;

use Core\Engine;

/**
 * Unauthorized Exception
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class UnauthorizedException extends Engine\Exception
{
    /**
     * Construct the exception
     *
     * @param  string    $msg
     * @param  integer   $code
     * @param  Exception $previous
     * @return void
     */
    public function __construct($msg = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct('Missing or incorrect API Key header', 401, $previous);
    }
}