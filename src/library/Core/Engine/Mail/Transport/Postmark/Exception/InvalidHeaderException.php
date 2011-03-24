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
 * Unprocessable Entity
 *
 * @author    Josh Team
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class InvalidHeaderException extends Engine\Exception
{
    /**
     * Construct the exception
     *
     * @param  string    $msg
     * @param  integer   $code
     * @param  Exception $previous
     * @return void
     */
    public function __construct($msg = '', $code = null, $previous = null)
    {
        if (empty($msg)) {
            $msg = 'Invalid Headers Sent';
        }
        
        parent::__construct($msg, $code, $previous);
    }
}