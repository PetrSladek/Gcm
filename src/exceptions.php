<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm;

class Exception extends \Exception {}

class LogicException extends Exception {}
class RuntimeException extends Exception {}

class IlegalApiKeyException extends LogicException {}
class NotRecipientException extends LogicException {}
class TooManyRecipientsException extends LogicException {}
class TooBigPayloadException extends LogicException {}
class WrongGcmIdException extends LogicException {}

class HttpException extends RuntimeException {}
class AuthenticationException extends RuntimeException {}
