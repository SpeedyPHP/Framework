<?php 
namespace Speedy\Controller\Helper;


use \Speedy\Session as SessionMgr;

class Session {
	
	public function session() {
		return SessionMgr::instance();
	}
	
}

?>