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

namespace Core\Engine\Validate;

/**
 * Validates all fields are equal
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class IdenticalField extends \Zend_Validate_Abstract
{
    /**
     * Validation key for not equal
     *
     */
    const NOT_SAME = 'notSame';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SAME => 'Values are not the same',
    );

    /**
     * Field to validate with
     *
     * @var string
     */
    protected $field;

    /**
     * Context
     *
     * @var string|array
     */
    protected $context;

    /**
     * Construct
     *
     */
    public function __construct($field, $context = null)
    {
        $this->field   = $field;
        $this->context = $context;
    }

    /**
     * Validate to a context
     *
     * @param string $value
     * @param array|string $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        // Set value
        $this->_setValue($value);

        if ($context === null && $this->context === null) {
            throw new \Zend_Validate_Exception(sprintf(
                'Validator "%s" contexts is not setup', get_class($this)
            ));
        }

        // Use instance context if not provided
        $context = ($context === null) ? $this->context : $context;

        // Validate string
        if (is_string($context) && $value == $context) {
             return true;
        }

        // Validate from array
        if (is_array($context) && isset($context[$this->field])
            && $value == $context[$this->field]) {
            return true;
        }

        $this->_error(self::NOT_SAME);
        return false;
    }
}

