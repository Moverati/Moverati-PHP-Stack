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

namespace Core\Engine\Form\Element;

use Core\Engine\Form;

/**
 * Date Element
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Date extends \Zend_Form_Element_Xhtml
{
    /**
     * Default date format
     */
    const DEFAULT_DATE_FORMAT = '%year%-%month%-%day%';

    /**
     * Return value as array
     */
    const RETURN_TYPE_ARRAY       = 'array';

    /**
     * Return value as a string date format
     */
    const RETURN_TYPE_DATE_FORMAT = 'string';

    /**
     * Return value as date time object
     */
    const RETURN_TYPE_DATETIME    = 'datetime';

    /**
     * Value return type
     *
     * @var string
     */
    private $returnType = self::RETURN_TYPE_DATETIME;

    /**
     * Date decorator
     *
     * @var Form\Decorator\FormElementDate
     */
    private $dateDecorator;

    /**
     * Date format
     *
     * @var string
     */
    private $dateFormat = self::DEFAULT_DATE_FORMAT;

    /**
     * Load default decorators
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }
        
        $selectParams = $this->getAttribs();

        $decorators = $this->getDecorators();
        if(!array_key_exists('Core\Engine\Form\Decorator\FormElementDate', $decorators)) {
            $this->addDecorator(new Form\Decorator\FormElementDate())
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array(
                    'tag' => 'p',
                    'class' => 'description')
                 )
                 ->addDecorator('HtmlTag', array(
                    'tag' => 'dd',
                    'id'  => $this->getName() . '-element')
                 )
                 ->addDecorator('Label', array('tag' => 'dt'));
        } else {
            $this->addDecorator($decorators['Core\Engine\Form\Decorator\FormElementDate'])
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array(
                    'tag'   => 'p',
                    'class' => 'description')
                 )
                 ->addDecorator('HtmlTag', array(
                    'tag' => 'dd',
                    'id'  => $this->getName() . '-element')
                 )
                 ->addDecorator('Label', array('tag' => 'dt'));
        }

    }

    /**
     * Get the return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * Set the return type
     *
     * @param string $returnType
     * @return Date
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * Set the date decorator
     *
     * @param Form\Decorator\FormElementDate $decorator
     */
    public function setDateDecorator(Form\Decorator\FormElementDate $decorator)
    {
        $this->dateDecorator = $this->addDecorator($decorator);
        return $this;
    }

    /**
     * Get the date decorator
     *
     * @return Form\Decorator\FormElementDate
     */
    public function getDateDecorator()
    {
        return $this->dateDecorator;
    }

    /**
     * Get the date format
     *
     * @return string 
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Set the date format
     *
     * @param string $dateFormat
     * @return Date
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * Set the value
     *
     * @param mixed $value
     * @return Date
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            $year  = !empty($value['year'])  ? $value['year'] : null;
            $month = !empty($value['month']) ? $value['month'] : null;
            $day   = !empty($value['day'])   ? $value['day'] : 1;
            if ($year && $month) {
                $date = new \DateTime();
                $date->setDate((int)$year, (int)$month, (int) $day);
                $date->setTime(0, 0, 0);
                $this->setAutoInsertNotEmptyValidator(false);
                $this->_value = $date;
            }
        } else {
            $this->_value = $value;
        }

        return $this;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function getValue()
    {
        switch ($this->getReturnType()) {
            case self::RETURN_TYPE_ARRAY:
                if ($this->_value === null) {
                    return array(
                        'year'  => null,
                        'month' => null,
                        'day'   => null
                    );
                }
                $date = array(
                    'year'  => date('Y', $this->_value->getTimestamp()),
                    'month' => date('m', $this->_value->getTimestamp()),
                    'day'   => date('d', $this->_value->getTimestamp())
                );

                array_walk_recursive($date, array($this, '_filterValue'));
                return $date;

            case self::RETURN_TYPE_DATETIME:
                if ($this->_value === null) {
                    return null;
                }
                $this->_filterValue($this->_value, $this->_value);
                return $this->_value;

            case self::RETURN_TYPE_DATE_FORMAT:
                if ($this->_value === null) {
                    return null;
                }

                $date = array(
                    date('Y', $this->_value->getTimestamp()),
                    date('m', $this->_value->getTimestamp()),
                    date('d', $this->_value->getTimestamp())
                );
                $dateFormat = str_replace(array('%year%', '%month%', '%day%'), $date, $this->getDateFormat());
                $this->_filterValue($dateFormat, $dateFormat);

                return $dateFormat;
        
            default:
                throw new \Zend_Form_Element_Exception('Unknown return type: ' . $this->getReturnType());
        }
    }
}