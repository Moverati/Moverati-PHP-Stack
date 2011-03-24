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
 * Markup Element
 *
 * @author    Daniel Cousineau
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Markup extends \Zend_Form_Element_Xhtml
{
    protected $content;

    /**
     *
     * @param string $content
     * @return markup
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

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

        $this->setDecorators(array(
            new Form\Decorator\FormElementMarkup(),
        ));
    }
}