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
 * HTML5 Email Element
 *
 * @author    Daniel Cousineau
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class Email extends \Zend_Form_Element_Text
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formEmail';
    
    public function init() {
    }
}