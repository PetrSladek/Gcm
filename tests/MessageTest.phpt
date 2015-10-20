<?php
/**
 * Test: Gcm\Message.
 * @author Petr Sladek <petr.sladek@skaut.cz>
 */
namespace GcmTests;

use Gcm\Message;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';

class MessageTest extends Tester\TestCase
{
    public function setUp()
    {
    }

    public function testAddToRecipients()
    {
        $message = new Message();
        $message->addTo('gcmId1');
        $message->addTo('gcmId2');
        $message->addTo('gcmId3');

        Assert::count(3, $message->getTo());
    }

    public function testRecipients()
    {
        $message = new Message(['gcmId1','gcmId2','gcmId3']);

        Assert::count(3, $message->getTo());
    }

    public function testBothRecipients()
    {
        $message = new Message('gcmId1');
        $message->addTo('gcmId2');
        $message->addTo('gcmId3');

        Assert::count(3, $message->getTo());
    }
}

(new MessageTest())->run();