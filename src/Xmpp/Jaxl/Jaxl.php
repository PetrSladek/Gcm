<?php
/**
 * @author: Petr /Peggy/ Sladek
 * @package: PetrSladek/Gcm
 */

namespace Gcm\Xmpp\Jaxl;


class Jaxl extends \JAXL  {

	public function get_socket_path() {
		$protocol = $this->cfg['port'] == 5223 ? "ssl" : "tcp";
		if(!empty($this->cfg['protocol']))
			$protocol = $this->cfg['protocol'];
		return $protocol."://".$this->cfg['host'].":".$this->cfg['port'];
	}

}