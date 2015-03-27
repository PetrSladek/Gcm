<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm\Http;


use Gcm\Message;

class Sender {

    /**
     * @var string
     */
    protected $url = 'https://android.googleapis.com/gcm/send';

    /**
     * @var string
     */
    protected $apiKey = false;


    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    public function send(Message $message)
    {

        if (!$this->apiKey)
            throw new IlegalApiKeyException("Api Key is empty");

        if (count($message->getTo()) > 1000)
            throw new RuntimeException("Recipients maximum is 1000 GCM Registration IDs");

        $data = $this->getPayload($message);
        if(!empty($data)) {
            if (strlen($data) > 4096)
                throw new RuntimeException("Data payload maximum is 4096 bytes");
        }

        $headers = array(
            'Content-Type: application/json',
            'Authorization: key='.$this->apiKey,
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $resultBody = curl_exec($ch);
        $resultHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch ($resultHttpCode) {
            case "200": break; // its ok
            case "400": throw new RuntimeException("HTTP Authentication Error [$resultHttpCode] $resultBody");
            case "401": throw new RuntimeException("HTTP Authentication Error [$resultHttpCode] $resultBody");
            default:    throw new RuntimeException("HTTP Error [$resultHttpCode] $resultBody");
        }

        $response =  new Response($message, $resultBody);
        return $response;
    }


    protected function getPayload(Message $message)
    {
        $data = array(
            'registration_ids' => (array) $message->getTo(),
            'collapse_key' => $message->getCollapseKey(),
            'data' => (array) $message->getData(),
            'delay_while_idle' => $message->getDelayWhileIdle(),
            'time_to_live' => $message->getTimeToLive(),
            'restricted_package_name' => $message->getRestrictedPackageName(),
            'dry_run' => $message->getDryRun(),
        );
        return json_encode($data);
    }

}