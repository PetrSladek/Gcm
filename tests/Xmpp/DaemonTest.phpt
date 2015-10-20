<?php
/**
 * Test: Xmpp\Deamon.
 *
 * @testCase AppTests\Xmpp\DaemonTest
 * @author Petr Sladek <petr.sladek@skaut.cz>
 */
namespace GcmTests\Xmpp;

use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class DaemonTest extends Tester\TestCase
{
    public function setUp()
    {
    }

    public function testFirst()
    {
    	Assert::true(true);
    }
}

(new DaemonTest())->run();