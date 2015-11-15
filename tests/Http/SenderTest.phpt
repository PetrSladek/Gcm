<?php
/**
 * @author Petr Sladek <petr.sladek@skaut.cz>
 * @package Gcm
 */
namespace GcmTests\Http;

use Gcm\AuthenticationException;
use Gcm\Http\Sender;
use Gcm\IlegalApiKeyException;
use Gcm\Message;
use Gcm\TooBigPayloadException;
use Gcm\TooManyRecipientsException;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class SenderTest extends Tester\TestCase
{

	/**
	 * @var Sender
	 */
	private $sender;

	/**
	 * @var Message
	 */
	private $message;

	public function setUp()
	{
		$this->message = new Message('APA91bHun4MxP5egoKMwt2KZFBaFUH-1RYqx..', ['testKey'=>'testValue']);
	}


	public function testEmptyApiKey()
	{

		$this->sender = new Sender('');

		Assert::throws(function() {
			$this->sender->send($this->message);
		}, IlegalApiKeyException::class);
	}

	public function testNullApiKey()
	{

		$this->sender = new Sender(null);

		Assert::throws(function() {
			$this->sender->send($this->message);
		}, IlegalApiKeyException::class);
	}


	public function testTooManyRecipients()
	{

		for($i = 0; $i < 1001; $i++)
		{
			$this->message->addTo("recipient".$i);
		}

		$this->sender = new Sender("YOUR_API_KEY");

		Assert::throws(function() {
			$this->sender->send($this->message);
		}, TooManyRecipientsException::class);

	}


	public function testTooBigPayload()
	{

		$data = [];
		for($i = 0; $i < 4069; $i++)
		{
			$data['key'.$i] = rand(0, $i*300);
		}

		$this->message->setData($data);
		$this->sender = new Sender("YOUR_API_KEY");

		Assert::throws(function() {
			$this->sender->send($this->message);
		}, TooBigPayloadException::class);

	}


	public function testAuthenticationError()
	{
		$this->sender = new Sender("YOUR_API_KEY");

		Assert::throws(function() {
			$this->sender->send($this->message);
		}, AuthenticationException::class);
	}

}

(new SenderTest())->run();