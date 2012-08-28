<?php 
namespace Speedy\Controller\Helper;


use \Speedy\Session as SessionMgr;
use \Speedy\Object;

class Session extends Object {
	
	public function session() {
		return SessionMgr::instance();
	}
	
}

?>