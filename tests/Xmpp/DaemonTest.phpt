<?php
/**
 * Test: Xmpp\Deamon.
 *
 * @testCase AppTests\Xmpp\DaemonTest
 * @author Petr Sladek <petr.sladek@skaut.cz>
 */
namespace GcmTests\Xmpp;

use Gcm\Message;
use Gcm\Xmpp\Daemon;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class DaemonTest extends Tester\TestCase
{
	public function setUp()
	{
	}

	public function testAuthFail()
	{
		$daemon = new Daemon("YOUR_SENDER_ID", "YOUR_API_KEY", true);

		$daemon->onAuthFailure[] = function(Daemon $daemon, $reason) {
			Assert::equal('not-authorized', $reason);
		};
		$daemon->run();

		Assert::true(true);
	}



//	public function testSendMessages()
//	{
//		$daemon = new Daemon("YOUR_SENDER_ID", "YOUR_API_KEY", true);
//
//		$daemon->onReady[] = function(Daemon $daemon) {
//
//			// We send 5 messages to device
//			foreach([1,2,3,4,5] as $i) {
//				$message = new Message("DEVICE_GCM_ID", ['text'=>"$i.message from server"],  "collapse-key-$i");
//				$daemon->send($message);
//			}
//		};
//
//		$daemon->onAllSent[] = function(Daemon $daemon, $countMessages) {
//
//			Assert::equal(5, $countMessages);
//
//			$daemon->stop(); // We stopped listeng
//		};
//
//		$daemon->run();
//	}
}

(new DaemonTest())->run();