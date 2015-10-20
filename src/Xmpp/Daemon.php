<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm\Xmpp;

use Gcm\Http\RuntimeException;
use Gcm\Message;
use Gcm\NotRecipientException;
use Gcm\RecievedMessage;
use Gcm\TooManyRecipientsException;
use Gcm\Xmpp\Jaxl\Jaxl;
use Nette\Utils\Json;


class Daemon extends \Nette\Object {

    //Production server and port
    const HOST = "gcm.googleapis.com";
    const PORT = "5235";
    //DEV server and port
    const TEST_HOST = 'gcm-preprod.googleapis.com';
    const TEST_PORT = '5236';

    protected $client;

//    public $onConnect = [];
    public $onReady = [];
    public $onAuthFailure = [];
    public $onMessage = [];
    public $onAllSent = [];
    public $onSentSuccess = [];
    public $onSentError = [];
    public $onStop = [];
    public $onDisconnect = [];


    const ERROR_BAD_ACK = 'BAD_ACK';
    const ERROR_CONNECTION_DRAINING = 'CONNECTION_DRAINING';
    const ERROR_BAD_REGISTRATION = 'BAD_REGISTRATION';
    const ERROR_DEVICE_UNREGISTERED = 'DEVICE_UNREGISTERED';
    const ERROR_INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    const ERROR_INVALID_JSON = 'INVALID_JSON';
    const ERROR_DEVICE_MESSAGE_RATE_EXCEEDED = 'DEVICE_MESSAGE_RATE_EXCEEDED';
    const ERROR_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
    const ERROR_QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';

    protected $messagesSent = 0;
    protected $messagesAcked = 0;

    public function __construct($senderId, $apiKey, $testMode = false)
    {
        $this->client = new Jaxl(array(
            'jid'=> "$senderId@gcm.googleapis.com",
            'pass'=> $apiKey,
            'auth_type'=> 'PLAIN',
            'protocol' => 'ssl',
            'host' => $testMode ? self::TEST_HOST : self::HOST,
            'port' => $testMode ? self::TEST_PORT : self::PORT,
            'strict' => false,
            'force_tls' => true,
        ));

        // Catch signal
        if (function_exists('pcntl_signal'))
        {
            pcntl_signal(SIGINT, function($signal) { $this->stop(); });
            pcntl_signal(SIGKILL, function($signal) { $this->stop(); });
            pcntl_signal(SIGTERM, function($signal) { $this->stop(); });
        }


        $this->client->add_cb('on_auth_success', function() {
            $this->onReady($this);
        });

        $this->client->add_cb('on_auth_failure', function($reason) {
            $this->onAuthFailure($this, $reason);
            $this->stop();
        });

        $this->client->add_cb('on_disconnect', function() {
            $this->onDisconnect($this);
        });

        $this->client->add_cb("on_normal_message", function($stanza) {
            $data = $this->getDataFromStanza($stanza);
            $message = new RecievedMessage(@$data['category'], @$data['data'], @$data['time_to_live'], @$data['message_id'], @$data['from'] );

            $this->sendAck($message);
            $this->onMessage($this, $message);
        });

        $this->client->add_cb("on__message", function($stanza) { //on__message gcm xmpp protocol is a funny one
            $data = $this->getDataFromStanza($stanza);
            $messageType = $data['message_type'];
            $messageId = $data['message_id']; //message id which was sent by us
            $from = $data['from']; //gcm key;

            if ($messageType == 'nack')
            {
                $errorDescription = @$data['error_description']; //usually empty ...
                $error = $data['error'];
                $this->onSentError($this, $from, $messageId, $error, $errorDescription);
            }
            else
            { // ACK
                $this->messagesAcked++;

                $this->onSentSucces($this, $from, $messageId, $this->messagesAcked, $this->messagesSent);

                if ($this->messagesSent == $this->messagesAcked)
                {
                    $this->onAllSent($this, $this->messagesSent);
                }
            }
        });


    }

    /**
     * Run daamon
     * Daemon go unitl call stop() method
     */
    public function run()
    {
        $this->client->start();
    }

    /**
     * Stop daemon
     */
    public function stop()
    {
        $this->onStop($this);
        $this->client->send_end_stream();
    }


    public function send(Message $message) {

        if(count($message->getTo()) == 0) {
            throw new NotRecipientException("Recipient must set use");
        }

        if(count($message->getTo()) > 1) {
            throw new TooManyRecipientsException("Recipient must by only one");
        }

        $this->sendGcmMessage([
                'to' => $message->getTo(true),
                'collapse_key' => $message->getCollapseKey(), // Could be unset
                'time_to_live' => $message->getTimeToLive(), //Could be unset
                'delay_while_idle' => $message->getDelayWhileIdle(), //Could be unset
                'message_id' => (string) microtime(),
                'data' => $message->getData(),
        ]);
    }

    /**
     * Send ack
     * @param RecievedMessage $message
     */
    protected function sendAck(RecievedMessage $message)
    {
        $this->sendGcmMessage([
            'to' => $message->getFrom(),
            'message_type' => 'ack',
            'message_id' => $message->getMessageId(),
        ]);
    }

    /**
     * Send GCM message
     * @param mixed $payload
     */
    protected function sendGcmMessage($payload)
    {
        $message = '<message id=""><gcm xmlns="google:mobile:data">' . Json::encode($payload) . '</gcm></message>';
        $this->client->send_raw($message);
    }

    /**
     * @return Jaxl
     */
    public function getXMPPClient()
    {
        return $this->client;
    }

    /**
     * @param \XMPPStanza $stanza
     * @return array data;
     */
    protected function getDataFromStanza(\XMPPStanza $stanza)
    {
        $data = Json::decode(html_entity_decode($stanza->childrens[0]->text));
        return $data;
    }




}