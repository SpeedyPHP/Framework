<?php 
namespace Speedy\Router\Routes;


class Regex extends Match {
	
	/**
	 * Process the format for a match
	 * @return object $this
	 */
	protected function processFormat() {
		$format	= "#" . $this->format() . "#";
		$this->setPattern($format);
		return $this;
	}
	
	/**
	 * Matches the route
	 * @param \Speedy\Request $request
	 * @return boolean
	 */
	public function match(\Speedy\Request $request) {
		// Find matches
		$uri	= $request->url();
		if (!$uri || strlen($uri) < 1) $uri = '/';
		$success 	= preg_match_all($this->pattern(), $uri, $matches);
		$base		= array_shift($matches);
		$params		= array( 'ext' => ($request->hasParam('ext')) ? $request->param('ext') : 'html' );
		//debug(array($uri, $success, $params, $this->pattern()));
		
		// Fail if it doesn't match
		if (!$success) return false;
		
		// Loop the matches to find the token values
		$params = [];
		foreach($matches as $key => $value) {
			$value = $value[0];
			$params[]	= $value;
		}
		
		$this->setParams($params);
		return true;
	}
	
}
?>