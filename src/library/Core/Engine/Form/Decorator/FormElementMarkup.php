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
 * Markup Element's Decorator
 *
 * @author    Daniel Cousineau
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class FormElementMarkup extends \Zend_Form_Decorator_Abstract
{
    /**
     * Render
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Form\Element\Markup) {
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

        $markup = $element->getContent();

        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;

            case self::APPEND:
            default:
                return $content . $this->getSeparator() . $markup;
        }
    }
}