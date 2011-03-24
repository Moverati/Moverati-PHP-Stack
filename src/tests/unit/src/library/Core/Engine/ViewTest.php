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

namespace Core\Engine;

/**
 * View Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $view = new View();
        $view->setScriptPath(__DIR__ . '/View/Scripts');

        $output = $view->render('run.phtml');
//        $this->assertEquals('&gt;', $output);

        $view = new View();
        $view->setScriptPath(__DIR__ . '/View/Scripts')
             ->setStreamFlag(false);

        $output = $view->render('run.phtml');

        if (ini_get('short_open_tag')) {
            $this->assertEquals('>', $output);
        } else {
            $this->assertEquals('<?=@ \'>\'; ?>', $output);
        }
    }
}