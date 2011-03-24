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

namespace Core\Engine\View\Filter;

/**
 * ShortTags Test
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class ShortTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * View filter
     *
     * @var ShortTags
     */
    private $filter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->filter = new ShortTags();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->filter);
    }
/*
    public function testFilterReturnsIfPhpHandles()
    {
        if (!ini_get('short_open_tags')) {
            $this->markTestSkipped('Enable php ini short_open_tags to run this test');
        }

        $string = $this->filter->filter('<? ?>');
        $this->assertEquals('<? ?>', $string);

        $string = $this->filter->filter('<?= $foo ?>');
        $this->assertEquals('<?= $foo ?>', $string);
    }
*/
    public function testFilterReturnsFiltered()
    {
        $string = $this->filter->filter('<? echo "" ?>');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->filter->filter('<? echo ""; ?>');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->filter->filter('<?= $foo ?>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->filter->filter('<?= $foo; ?>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->filter->filter('<?=@ $foo; ?>');
        $this->assertEquals('<?php echo $this->escape($foo); ?>', $string);

        $string = $this->filter->filter('<? echo $foo; echo $bar; ?>');
        $this->assertEquals('<?php echo $foo; echo $bar; ?>', $string);
    }

    public function testFilterReturnsFilteredWithoutCloseTag()
    {
        $this->markTestSkipped('Does not support this yet');

        $string = $this->filter->filter('<? echo ""');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->filter->filter('<? echo "";');
        $this->assertEquals('<?php echo ""; ?>', $string);

        $string = $this->filter->filter('<?= $foo');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->filter->filter('<?= $foo;');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->filter->filter('<?=@ $foo');
        $this->assertEquals('<?php echo $this->escape($foo); ?>', $string);

        $string = $this->filter->filter('<?=@ $foo;');
        $this->assertEquals('<?php echo $this->escape($foo); ?>', $string);
    }

    public function testFiltersMultiLine()
    {
        $string = $this->filter->filter('<? echo "
        "?>');
        $this->assertEquals('<?php echo "
        "; ?>', $string);

        $string = $this->filter->filter('<? echo "
        ";?>');
        $this->assertEquals('<?php echo "
        "; ?>', $string);

        $string = $this->filter->filter('<?=
        $foo?>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->filter->filter('<?=
        $foo;?>');
        $this->assertEquals('<?php echo $foo; ?>', $string);

        $string = $this->filter->filter('<?=@
        $foo?>');
        $this->assertEquals('<?php echo $this->escape($foo); ?>', $string);

        $string = $this->filter->filter('<?=@
        $foo;?>');
        $this->assertEquals('<?php echo $this->escape($foo); ?>', $string);
    }
}