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

use Core\Engine;

/**
 * IdenticalField Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class IdenticalFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValidWithoutContextsThrowsException()
    {
        $this->setExpectedException('Zend_Validate_Exception');
        
        $validator = new IdenticalField('field');
        $result    = $validator->isValid('field');
    }
    
    public function testIsValidSetsValue()
    {
        $validator = new IdenticalField('field', array('field' => 'test'));
        $validator->isValid('test');
        
        $this->assertAttributeEquals('test', '_value', $validator);
    }

    public function testisValidUsesOverrideContext()
    {
        $validator = new IdenticalField('field', array('field' => 'test'));
        $result    = $validator->isValid('test', array('field' => 'bat'));

        $this->assertFalse($result);
        $this->assertArrayHasKey(IdenticalField::NOT_SAME, $validator->getMessages());

        $validator = new IdenticalField('field', array('field' => 'bat'));
        $result    = $validator->isValid('test', array('field' => 'test'));

        $this->assertTrue($result);
    }

    public function testIsValidWorksWithContextAsString()
    {
        $validator = new IdenticalField('field', 'test');
        $result    = $validator->isValid('test', 'bat');

        $this->assertFalse($result);
        $this->assertArrayHasKey(IdenticalField::NOT_SAME, $validator->getMessages());

        $validator = new IdenticalField('field', 'bat');
        $result    = $validator->isValid('test', 'test');

        $this->assertTrue($result);
    }
}