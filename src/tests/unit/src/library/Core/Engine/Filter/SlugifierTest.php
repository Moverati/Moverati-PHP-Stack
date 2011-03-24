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

namespace Core\Engine\Filter;

/**
 * Url Slugifier filter test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class SlugifierTest extends \PHPUnit_Framework_TestCase
{
    public function testStaticSlugify()
    {
        $slug = Slugifier::slugify('Hello There');
        $this->assertEquals('hello-there', $slug);
    }

    public function testSlugifierImplementsZendFilterInterface()
    {
        $slugifier = new Slugifier();
        $this->assertTrue($slugifier instanceof \Zend_Filter_Interface);
    }

    public function testSlugifierConvertsToLowercase()
    {
        $slugifier = new Slugifier();
        $this->assertEquals('hello-there', $slugifier->filter('HeLLO There'));
    }

    public function testSlugifierConvertsSpacesToDash()
    {
        $slugifier = new Slugifier();
        $this->assertEquals('hello-there', $slugifier->filter('HeLLO There'));
        $this->assertEquals('hello-there-whats', $slugifier->filter(' HeLLO There Whats '));
    }

    public function testSlugifierRemovesFunkyCharacters()
    {
        $slugifier = new Slugifier();
        $this->assertEquals('whats-ups-doc', $slugifier->filter('What\'s up"s doc!?><'));
        $this->assertEquals('hello123456789', $slugifier->filter(' hello123456789!@#$%^&*()_+{}|:"<>?,./;\'[]\`~ '));
        $this->assertEquals('you-could-be-todays-lottery-100-winner', $slugifier->filter('You Could Be Today\'s Lottery 100% winner'));
    }
}