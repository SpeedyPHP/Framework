<?php
namespace Speedy;


use \App as AppInUse;
use Speedy\Http\Exception as HttpException;
use Speedy\View;
use Speedy\Request;

class Error {

	/**
	 * Exception error caught
	 * @var Exception
	 */
	protected $error;


	/**
	 * Flag for reporting errors
	 * @var bool
	 */
	protected $report = false;


	/**
	 * Error handler for Speedy Apps
	 * @param $e Exception
	 * @return $this Speedy\Error
	 */
	public function __construct(\Exception $e) {
		$this->handleError($e);
		$this->report = AppInUse::instance()->config('errors.report');

		return $this;
	}

	private function shouldReport() {
		return $this->report;
	}

	/**
	 * Handle error entry point
	 * @param $e Exception
	 * @return null
	 */
	protected function handleError($e) {
		$this->setError($e);

		$className = str_replace('\\', '', get_class($e));
		if ($e instanceof HttpException) {
			return $this->handleHttpException();
		}

		http_response_code(500);
	}

	/**
	 * Handle HTTP Exceptions
	 * @return null
	 */
	protected function handleHttpException() {
		$code = $this->error()->getCode();
		http_response_code($code);

		$viewFile = "errors" . DS . $code;
		if (!View::instance()->findFile($viewFile)) {
			return $this->default404(); // Default view
		}

		echo View::instance()->render($viewFile);
	}

	/**
	 * Default 404
	 * @return null
	 */
	protected function default404() {
		$html = [
			'<html>',
			'<head>',
			'</head>',
			'<body>',
			'<div class="page-header">',
			'<h1>404</h1>',
			'</div>',
			'<div class="description"><p>%s</p></div>',
			'<div class="params">',
			'<h2>Request Parameters</h2>',
			'<pre>%s</pre>',
			'</div>',
			'</body>',
			'</html>'
			];
			$html = implode("\n", $html);
		echo sprintf($html, 
					$this->error()->getMessage(), 
					print_r(Request::instance()->params(), true));
		return;
	}

	/**
	 * Setter for error
	 * @param $e Exception
	 * @return $this Speedy\Error
	 */
	protected function setError($e) {
		$this->error = $e;
		return $this;
	}

	/**
	 * Getter for error
	 * @return $this->error Exception
	 */
	public function error() {
		return $this->error;
	}

}