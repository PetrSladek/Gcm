# PHP Google Cloud Messiging
Google Cloud Messaging PHP library. Gcm\Http\Sender for sending Messages over HTTP and Gcm\Xmpp\Daemon for sending and recieving messages over XMPP (CCS).

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

$gcm = new Sender("YOUR_API_KEY");
$response = $gcm->send($message);

var_dump($response);
```


### XMPP Daemon recieving message
```php
use Gcm\Xmpp\Daemon;

$daemon = new Daemon("SENDER_ID", "API_KEY", $testMode = false);;

$daemon->onReady[] = function(Daemon $daemon) {
    print "Ready / Auth success. Waiting for Messages";
};
$daemon->onAuthFailure[] = function(Daemon $daemon, $reason) {
    print "Auth failure (reason $reason)";
};
$daemon->onStop[] = function(Daemon $daemon) {
    print 'Daemon has stopped by $daemon->stop()';
};
$daemon->onDisconnect[] = function(Daemon $daemon) {
    print "Daemon has been disconected";
};
$daemon->onMessage[] = function(Daemon $daemon, \Gcm\RecievedMessage $message) {
    print "Recieved message from GCM";
    print_r($message);
};


$daemon->run(); // running until call $daemon->stop() or kill process;
```


### XMPP Daemon sending messages
```php
use Gcm\Xmpp\Daemon;
use Gcm\Message;


$daemon = new Daemon("SENDER_ID", "API_KEY", $testMode = false);

$daemon->onReady[] = function(Daemon $daemon) {
    print "Ready / Auth success. Waiting for Messages";
    
    // We send 5 messages to device
    foreach([1,2,3,4,5] as $i) {
      $message = new Message("DEVICE_GCM_ID", ['text'=>"$i.message from server"],  "collapse-key-$i");
      $daemon->send($message);
    }
};
$daemon->onAuthFailure[] = function(Daemon $daemon, $reason) {
    print "Auth failure (reason $reason)";
};
$daemon->onStop[] = function(Daemon $daemon) {
    print 'Daemon has stopped by $daemon->stop()';
};
$daemon->onDisconnect[] = function(Daemon $daemon) {
    print "Daemon has been disconected";
};
$daemon->onMessage[] = function(Daemon $daemon, \Gcm\RecievedMessage $message) {
    print "Recieved message from GCM";
    print_r($message);
};
$daemon->onAllSent[] = function(Daemon $daemon, $countMessages) {
    print "Has been sent all of $countMessages";
    // On all of 5 messages has been sent and confirmed from server
    $deamom->stop(); // We stopped listeng
};

$daemon->run(); // Start sending messaging
```
