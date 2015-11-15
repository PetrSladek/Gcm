<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm\Http;

use Gcm\Message;
use Nette\Utils\Json;

class Response
{

	/**
	 * Unique ID (number) identifying the multicast message.
	 *
	 * @var integer
	 */
	private $multicastId = null;

	/**
	 * Number of messages that were processed without an error.
	 *
	 * @var integer
	 */
	private $success = null;

	/**
	 * Number of messages that could not be processed.
	 *
	 * @var integer
	 */
	private $failure = null;

	/**
	 * Number of results that contain a canonical registration ID.
	 *
	 * @var integer
	 */
	private $canonicalIds = null;

	/**
	 * Array of objects representing the status of the messages processed.
	 * The objects are listed in the same order as the request
	 * (i.e., for each registration ID in the request, its result is listed in the same index in the response)
	 * and they can have these fields:
	 *      message_id:         String representing the message when it was successfully processed.
	 *      registration_id:    If set, means that GCM processed the message but it has another canonical registration ID for that device, so sender should replace the IDs on future requests
	 *                          (otherwise they might be rejected). This field is never set if there is an error in the request.
	 *      error:              String describing an error that occurred while processing the message for that recipient. The possible values are the same as documented in the above table, plus "Unavailable"
	 *                          (meaning GCM servers were busy and could not process the message for that particular recipient, so it could be retried).
	 *
	 * @var array
	 */
	private $results = [];

	public function __construct(Message $message, $body)
	{
		$data = Json::decode($body);

		$this->multicastId = $data->multicast_id;
		$this->failure = $data->failure;
		$this->success = $data->success;
		$this->canonicalIds = $data->canonical_ids;

		foreach ($message->getTo() as $key => $to)
		{
			$this->results[$to] = $data->results[$key];
		}
	}

	public function getMulticastId()
	{
		return $this->multicastId;
	}

	public function getSuccessCount()
	{
		return $this->success;
	}

	public function getFailureCount()
	{
		return $this->failure;
	}

	public function getNewRegistrationIdsCount()
	{
		return $this->canonicalIds;
	}

	public function getResults()
	{
		return $this->results;
	}

}