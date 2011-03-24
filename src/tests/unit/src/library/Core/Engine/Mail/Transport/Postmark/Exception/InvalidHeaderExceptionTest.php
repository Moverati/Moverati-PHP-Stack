<?php
namespace Core\Engine\Mail\Transport\Postmark\Exception;

require_once 'PHPUnit/Framework.php';

/**
 * Test class for InvalidHeaderException.
 * Generated by PHPUnit on 2010-06-21 at 17:52:52.
 */
class InvalidHeaderExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InvalidHeaderException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new InvalidHeaderException;
    }

    public function testNoMessage()
    {
        $this->assertSame('Invalid Headers Sent', $this->object->getMessage());
    }

    public function testWithMessage()
    {
        $msg = 'Some Exception Message';
        $exc = new InvalidHeaderException($msg);
        $this->assertSame($msg, $exc->getMessage());
    }
}