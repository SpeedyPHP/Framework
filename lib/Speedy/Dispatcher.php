<?php 
namespace Speedy;


use \Speedy\Utility\Inflector;

class Dispatcher extends Object {

	public static function run(\Speedy\Router $router) {
		$response	= new Response();
		$route		= $router->route();
		
		$required	= array( 'controller', 'action' );
		$keys		= array_keys($route);
		
		$intersect = array_intersect($required, $keys);
		if (count($required) !== count($intersect)) {
			var_dump('Route is broken');
		}
		
		$app		= App::instance();
		$namespace	= $app->ns();
		$name		= (isset($route['namespace'])) ? $route['namespace'] : $app->name();
		$path		= strtolower($route['controller']);
		
		// Parse controller
		/*if (strpos($path, '/')) {
			$path	= str_replace('/', '.', $path);
		}*/
		
		$pathArr	= $route['controller'] = explode('/', $path);
		foreach ($pathArr as &$part) {
			$part = Inflector::camelize($part);
		}
		
		$className	= Inflector::camelize(array_pop($pathArr));
		$fullName	= (count($pathArr) > 0) ? implode('\\', $pathArr) . '\\' . $className : $className;
		$fullName	= "\\{$name}\\Controllers\\{$fullName}";
		
		/*$import		= (count($pathArr) > 0) ? implode('.', $pathArr) . '.' . Inflector::underscore($className) : Inflector::underscore($className);
		$import		= $namespace . '.controllers.' . $import;*/
		
		if (!class_exists($fullName)) {
			// TODO: Error controller not found
			print "Controller not found for $fullName";
			exit;
		}
		
		$router->request()->addParams($route);
		$controllerObj	= new $fullName($router->request(), $response);
		if (!method_exists($controllerObj, $route['action'])) {
			// TODO: Error action not found in controller
			print "Action {$route['action']} not found in $fullName";
			exit;
		}
		
		if (!$controllerObj instanceof \Speedy\Controller) {
			// TODO: Error controller not an instance of \Speedy\Controller
			print "Controller $fullName is not an instance of \Speedy\Controller";
			exit;
		}
		
		$controllerObj->__run($route['action']);
		return $response;
	}
	
}

?>