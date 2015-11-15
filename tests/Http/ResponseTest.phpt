<?php
/**
 * @author Petr Sladek <petr.sladek@skaut.cz>
 * @package Gcm
 */
namespace GcmTests\Http;

use Gcm\AuthenticationException;
use Gcm\Http\Response;
use Gcm\Http\Sender;
use Gcm\IlegalApiKeyException;
use Gcm\Message;
use Gcm\TooBigPayloadException;
use Gcm\TooManyRecipientsException;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class ResponseTest extends Tester\TestCase
{


	public function testCreate()
	{

		$body = json_encode([
			'multicast_id' => 1,
			'failure' => 1,
			'success' => 1,
			'canonical_ids' => 1,
			'results' => [
				['data'=>true],
			]
		]);
		$message = new Message('recipient1', ['testKey'=>'testValue']);

		$response = new Response($message, $body);

		Assert::equal(1, $response->getMulticastId());
		Assert::equal(1, $response->getFailureCount());
		Assert::equal(1, $response->getSuccessCount());
		Assert::equal(1, $response->getNewRegistrationIdsCount());
		Assert::equal(['recipient1' => (object) ['data'=>true]], $response->getResults());
		Assert::equal(count($message->getTo()), count($response->getResults()));
	}


}

(new ResponseTest())->run();