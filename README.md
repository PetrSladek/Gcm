# PHP Google Cloud Messiging
Google Cloud Messaging PHP library. Gcm\Http\Sender for sending Messages over HTTP amd Gcm\Xmpp\Deamon for sending and recieving messages over XMPP (CCS).

## Dependencies
- Nette/Utils ~2.2.0
- JAXL 3.0.0

## Install

Best way is [Composer](http://getcomposer.org/):

```sh
$ composer require petrsladek/gcm:dev-master
```

## Usage

### Http Sender

```php
use Gcm\Message;
use Gcm\Http\Sender;

$message = new Message("DEVICE_GCM_ID", ['foo'=>'bar', 'baz'=>[1,2,3]], "collapse-key-1");
$message->addTo("ANOTHER_DEVICE_GCM_ID");
$message->timeToLive(3600); // TTL 1 hour

$gcm = new Sender("<YOUR_API_KEY");
$response = $gcm->send($message);

var_dump($response);
```


### XMPP Deamon recieving message
```php
use Gcm\Xmpp\Deamon;

$deamon = new Deamon;

$deamon->onReady[] = function(Deamon $deamon) {
    print "Ready / Auth success. Waiting for Messages";
};
$deamon->onAuthFailure[] = function(Deamon $deamon, $reason) {
    print "Auth failure (reason $reason)";
};
$deamon->onStop[] = function(Deamon $deamon) {
    print 'Deamon has stopped by $deamon->stop()';
};
$deamon->onDisconnect[] = function(Deamon $deamon) {
    print "Deamon has been disconected";
};
$deamon->onMessage[] = function(Deamon $deamon, \Gcm\RecievedMessage $message) {
    print "Recieved message from GCM";
    print_r($message);
};


$deamon->run(); // running until call $deamon->stop() or kill process;
```


### XMPP Deamon sending messages
```php
use Gcm\Xmpp\Deamon;
use Gcm\Message;


$deamon = new Deamon(SENDER_ID, API_KEY, $testMode = false);

$deamon->onReady[] = function(Deamon $deamon) {
    print "Ready / Auth success. Waiting for Messages";
    
    // We send 5 messages to device
    foreach([1,2,3,4,5] as $i) {
      $message = new Message("DEVICE_GCM_ID", ['text'=>"$i.message from server"],  "collapse-key-$i");
      $deamon->send($message);
    }
};
$deamon->onAuthFailure[] = function(Deamon $deamon, $reason) {
    print "Auth failure (reason $reason)";
};
$deamon->onStop[] = function(Deamon $deamon) {
    print 'Deamon has stopped by $deamon->stop()';
};
$deamon->onDisconnect[] = function(Deamon $deamon) {
    print "Deamon has been disconected";
};
$deamon->onMessage[] = function(Deamon $deamon, \Gcm\RecievedMessage $message) {
    print "Recieved message from GCM";
    print_r($message);
};
$deamon->onAllSent[] = function(Deamon $deamon, $countMessages) {
    print "Has been sent all of $countMessages";
    // On all of 5 messages has been sent and confirmed from server
    $deamom->stop(); // We stopped listeng
};

$deamon->run(); // Start sending messaging
```
