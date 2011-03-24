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

namespace Core\Engine\Form\Decorator;

use Core\Engine\Form;

/**
 * Date Element's Decorator
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class FormElementDate extends \Zend_Form_Decorator_Abstract
{
    /**
     * Default display format
     */
    const DEFAULT_DISPLAY_FORMAT = '%year% / %month% / %day%';

    /**
     * Start Year
     *
     * @var integer
     */
    protected $startYear;

    /**
     * End Year
     *
     * @var integer
     */
    protected $endYear;

    /**
     * Display Format
     *
     * @var string
     */
    protected $displayFormat = self::DEFAULT_DISPLAY_FORMAT;
    
    /**
     *
     * @var array
     */
    protected $selectParams;

    /**
     * Construct
     *
     * @param integer $startYear
     * @param integer $endYear
     */
    public function __construct($startYear = null, $endYear = null, $displayFormat = null)
    {
        $thisYear = date('Y');

        if ($startYear === null) {
            $startYear = $thisYear - 100;
        }

        $this->startYear = $startYear;

        if ($endYear === null) {
            $endYear   = $thisYear;
        }

        $this->endYear   = $endYear;

        if( $displayFormat !== null ) {
            $this->displayFormat = $displayFormat;
        }
    }

    /**
     * Render
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Form\Element\Date) {
            // Only want to render Date elements
            return $content;
        }

        $view = $element->getView();
        if (!$view instanceof \Zend_View_Interface) {
            // Using view helpers
            throw new \Zend_Form_Decorator_Exception(
                'View object is required for the FormElementDate decorator'
            );
        }

        $returnType = $element->getReturnType();


        $element->setReturnType(Form\Element\Date::RETURN_TYPE_DATETIME);

        $day   = null;
        $month = null;
        $year  = null;

        if ($element->getValue() instanceof \DateTime) {
            $day   = date('d', $element->getValue()->getTimestamp());
            $month = date('m', $element->getValue()->getTimestamp());
            $year  = date('Y', $element->getValue()->getTimestamp());
        }
        
        $name  = $element->getFullyQualifiedName();

        $element->setReturnType($returnType);

        $params = $element->getAttribs();

        $daysArray = range(1, 31);
        $days      = array(null => '-- DAY --') + array_combine($daysArray, $daysArray);

        $months = array(
            null => '-- MONTH --',
            1    => 'January',
            2    => 'Feburary',
            3    => 'March',
            4    => 'April',
            5    => 'May',
            6    => 'June',
            7    => 'July',
            8    => 'August',
            9    => 'September',
            10   => 'October',
            11   => 'November',
            12   => 'December'
        );

        $yrsArray = range($this->startYear, $this->endYear);
        $years    = array(null => '-- YEAR --') + array_reverse(array_combine($yrsArray, $yrsArray), true);

        $markup = str_replace(
            array(
                '%year%', '%month%', '%day%'
            ),
            array(
                $view->formSelect($name . '[year]', $year, $params, $years),
                $view->formSelect($name . '[month]', $month, $params, $months),
                $view->formSelect($name . '[day]', $day, $params, $days),
            ),
            $this->displayFormat
        );

        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;
                
            case self::APPEND:
            default:
                return $content . $this->getSeparator() . $markup;
        }
    }
}