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

 * Something with the message is not quite right (either malformed JSON
 * or incorrect fields). In this case, the response body contains JSON
 * {ErrorCode: 405, Message: "details"} with an error code and an error
 * message containing details on what went wrong.
 *
 * 407 – Bounce not found
 * You requested a bounce by ID, but we could not find an entry in our 
 * database.
 * 
 * 408 – Bounce query exception
 * You provided bad arguments as a bounces filter.
 * 
 * 406 – Inactive recipient
 * You tried to send to a recipient that has been marked as inactive. 
 * Inactive recipients are ones that have generated a hard bounce or a spam 
 * complaint.
 * 
 * 403 – Incompatible JSON
 * The JSON input you provided is syntactically correct, but still not the 
 * one we expect.
 * 
 * 300 – Invalid email request
 * Validation failed for the email request JSON data that you provided.
 * 
 * 402 – Invalid JSON
 * The JSON input you provided is syntactically incorrect.
 * 
 * 409 – JSON required
 * Your HTTP request does not have the Accept and Content-Type headers 
 * set to application/json.
 * 
 * 0 – Bad or missing API token
 * Your request did not submit the correct API token in the 
 * X-Postmark-Server-Token header.
 * 
 * 401 – Sender signature not confirmed
 * You are trying to send email with a From address that does not have 
 * a corresponding confirmed sender signature.
 * 
 * 400 – Sender signature not found
 * You are trying to send email with a From address that does not have 
 * a sender signature.
 * 
 * 405 – Not allowed to send
 * You ran out of credits. 
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class UnprocessableEntityException extends Engine\Exception
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
        if (empty($msg)) {
            $msg = 'Unprocessable Entity';
        }
        
        parent::__construct($msg, $code, $previous);
    }
}