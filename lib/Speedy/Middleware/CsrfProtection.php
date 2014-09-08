<?php 
namespace Speedy\Middleware;

use Speedy\Config;
use Speedy\Loader;
use Speedy\Router;
use Speedy\Middleware\Base as MiddlewareBase;
use Speedy\Request;
use Speedy\Session;
use DateTime;


class CsrfProtection extends MiddlewareBase {

	/* public $types	= array(
		'javascript',
		'style',
		'images'
	);
	
	public $path;
	
	private $_debug	= false; */
	
	
	public function getApp() {
		$stack = $this->getStack();
		return $stack[$stack->total() - 1];
	}
	
	public function call() {
		// Get last request time
		Session::write('Csrf.last_active', time());

		// Check if csrf token is present and valid
		if ($this->request()->method() != Request::GET) {

		}

		// Set new last request time
		
		$this->next()->call();
	}
	
	public function request() {
		return Router::instance()->request();
	}
	
}

