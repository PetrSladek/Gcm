<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm\Http;

class Exception extends \Exception {}

class LogicException extends Exception {}
class RuntimeException extends Exception {}

class IlegalApiKeyException extends LogicException {}
