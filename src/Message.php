<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm;

use Gcm\Http\RuntimeException;

class Message
{

	/**
	 * Recipients - List of GCM registration IDs (from 1 to 1000 recipients allowed)
	 * @var array
	 */
	private $to = array();

	/**
	 * An arbitrary string (such as "Updates Available") that is used to collapse a group of like messages
	 * when the device is offline, so that only the last message gets sent to the client.
	 * This is intended to avoid sending too many messages to the phone when it comes back online.
	 * Note that since there is no guarantee of the order in which messages get sent, the "last" message
	 * may not actually be the last message sent by the application server.
	 * Collapse keys are also called send-to-sync messages.
	 *
	 * Optional.
	 *
	 * @var string|null
	 */
	private $collapseKey = null;

	/**
	 * Key-value pairs od messages payload data.
	 * Max payload size is 4kB
	 *
	 * Optional.
	 *
	 * @var array|null
	 */
	private $data = null;

	/**
	 * If included, indicates that the message should not be sent immediately if the device is idle.
	 * The server will wait for the device to become active, and then only the last message
	 * for each collapse_key value will be sent.
	 *
	 * Optional.
	 *
	 * @var boolean
	 */
	private $delayWhileIdle = false;

	/**
	 * How long (in seconds) the message should be kept on GCM storage if the device is offline.
	 *
	 * Optional (default time-to-live is 4 weeks).
	 *
	 * @var int
	 */
	private $timeToLive = null;

	/**
	 * A string containing the package name of your application.
	 * When set, messages will only be sent to registration IDs that match the package name.
	 *
	 * Optional.
	 *
	 * @var string|null
	 */
	private $restrictedPackageName = null;

	/**
	 * If included, allows developers to test their request without actually sending a message.
	 *
	 * Optional.
	 *
	 * @var boolean
	 */
	private $dryRun = false;

	public function __construct($toRegId = null, $data = null, $collapseKey = null)
	{
		if(is_array($toRegId))
		{
			foreach ($toRegId as $to)
			{
				$this->addTo($to);
			}
		}
		elseif($toRegId)
		{
			$this->setTo($toRegId);
		}

		$this->setData($data);
		$this->setCollapseKey($collapseKey);
	}


	public function getTo($onlyOne = false)
	{
		if($onlyOne)
			return current($this->to); // firstone
		return $this->to;
	}


	public function setTo($to)
	{
		$this->to = [];
		$this->addTo($to);

		return $this;
	}


	public function addTo($to)
	{
		if(!is_string($to))
			throw new WrongGcmIdException("Recipient must be string GCM Registration ID");

		$this->to[] = $to;
		return $this;
	}


	public function getCollapseKey()
	{
		return $this->collapseKey;
	}


	public function setCollapseKey($collapseKey)
	{
		$this->collapseKey = $collapseKey;
		return $this;
	}


	public function getData()
	{
		return $this->data;
	}


	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}


	public function getDelayWhileIdle()
	{
		return $this->delayWhileIdle;
	}


	public function setDelayWhileIdle($delayWhileIdle)
	{
		$this->delayWhileIdle = $delayWhileIdle;
		return $this;
	}


	public function getTimeToLive()
	{
		return $this->timeToLive;
	}


	public function setTimeToLive($timeToLive)
	{
		$this->timeToLive = $timeToLive;
		return $this;
	}


	public function getRestrictedPackageName()
	{
		return $this->restrictedPackageName;
	}


	public function setRestrictedPackageName($restrictedPackageName)
	{
		$this->restrictedPackageName = $restrictedPackageName;
		return $this;
	}


	public function getDryRun()
	{
		return $this->dryRun;
	}


	public function setDryRun($dryRun)
	{
		$this->dryRun = $dryRun;
		return $this;
	}

}