<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm;

class RecievedMessage {

	/**
	 * Category
	 * @var string
	 */
	protected $category;

	/**
	 * Data
	 * @var \stdClass
	 */
	protected $data;

	/**
	 * Time to live in seconds
	 * @var int timeToLive seconds
	 */
	protected $timeToLive;

	/**
	 * Message ID
	 * @var string
	 */
	protected $messageId;

	/**
	 * From device (GCM Registration ID)
	 * @var string
	 */
	protected $from;


	function __construct($category, $data, $timeToLive, $messageId, $from)
	{
		$this->category = (string) $category;
		$this->data = (object) $data;
		$this->timeToLive = (int) $timeToLive;
		$this->messageId = (string) $messageId;
		$this->from = (string) $from;
	}


	/**
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}


	/**
	 * @return \stdClass
	 */
	public function getData()
	{
		return $this->data;
	}


	/**
	 * @return int
	 */
	public function getTimeToLive()
	{
		return $this->timeToLive;
	}


	/**
	 * @return string
	 */
	public function getMessageId()
	{
		return $this->messageId;
	}


	/**
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}
}