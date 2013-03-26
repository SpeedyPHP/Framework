<?php
namespace Speedy;


use Speedy\Exception\Mailer as MailerException;
use Speedy\View;
use Speedy\Utility\Inflector;

class Mailer extends Object {

	use Traits\Singleton;

	/**
	 * Defaults
	 * @var array
	 */
	protected $default = [];

	/**
	 * To address
	 * @var string
	 */
	private $_to;

	/**
	 * Mail subject
	 * @var string
	 */
	private $_subject;

	/**
	 * From address
	 * @var string
	 */
	private $_from;

	/**
	 * Headers hash for email
	 * @var array
	 */
	private $_headers = [];

	/**
	 * Multipart Boundary for current mailer
	 * @var string
	 */
	private $_multiPartBoundary;

	/**
	 * Message 
	 * @var string
	 */
	private $_message = '';

	/**
	 * Current class name
	 * @var string
	 */
	private $_class;

	/**
	 * Called method name
	 * @var string
	 */
	private $_method;

	/**
	 * End of line
	 * @var string
	 */
	private $_eol;



	/**
	 * Sets up a mailer
	 * @return Speedy\Mailer $this
	 */
	public static function mail($hash, $data = []) {
		$class = get_called_class();
		if (strpos($class, '\\')) {
			$aClass = explode('\\', $class);
			array_shift($aClass);
			array_shift($aClass);

			array_walk($aClass, function(&$item, $key) {
				$item = Inflector::underscore($item);
			});
			$class_path = implode(DS, $aClass);
		} else {
			$class_path = Inflector::underscore($class);
		}

		$data = array_merge($data, [
				'controller' => $class_path
			]);

		return $class::instance()->sendMail($hash, $data);
	}

	/**
	 * Add data to the object
	 * @return Speedy\Mailer $this
	 */
	protected function setData($data, $value = null) {
		$this->_data = $data;
		return $this;
	}

	/**
	 * Getter for php end of line
	 * @return string PHP_EOL
	 */
	public function eol() {
		return PHP_EOL;
	}

	/**
	 * Business end of setting up mailer
	 * @return boolean
	 */
	public function sendMail($hash, $data = []) {
		$options = array_merge($this->default, $hash);
		extract($options);

		if (!isset($to))
			throw new MailerException('Missing to address', 2);

		if (!isset($from)) 
			throw new MailerException("Missing from address", 3);

		$this->setData($data);
		$this->_to = $to;
		$this->_from	= $from;
		$this->_subject = (isset($subject)) ? $subject : '';
		$this->_headers = [];

		$trace = debug_backtrace();
		$this->_method = $this->action = Inflector::underscore($trace[2]['function']);

		$aClass	= explode('\\', $trace[2]['class']);
		$class 	= array_pop($aClass);
		$this->_class = Inflector::underscore($class);

		return $this->deliver();
	}

	/**
	 * Delivers the current setup mailer
	 * @return boolean
	 */
	public function deliver() {
		if (!isset($this->_to))
			throw new MailerException('Missing to address', 1);

		$this->addHeader('MIME-Version', '1.0')
			->addHeader('From', $this->_from)
			->addHeader('Content-Type', "multipart/alternative;boundary=" . $this->multiPartBoundary());


		$this->setPlainMessage($this->render('text'));
		$this->setHtmlMessage($this->render('html'));

		return mail($this->_to, $this->_subject, $this->message(), $this->headers());
	}

	/**
	 * Add a header for the current mailer
	 * @param $name string
	 * @param $value mixed optional
	 */
	public function addHeader($name, $value = null) {
		if ($value)
			$this->_headers[$name] = $value;
		else
			$this->_headers[]	= $name;
		return $this;
	}

	/**
	 * Getter for end message
	 * @return string _message property with appended boundary
	 */
	public function message() {
		return $this->_message . "--" . $this->multiPartBoundary() . "--" . $this->eol() . $this->eol();
	}

	/**
	 * Render type mailer
	 * @return string
	 */
	private function render($type) {
		return View::instance()
					->setData($this->data())
					->setParams($this->data())
					->render("{$this->controller}/{$this->action}", [], [], $type);
	}

	/**
	 * Returns the string of headers for mail send
	 * @return string
	 */
	private function headers() {
		$headers = '';
		foreach ($this->_headers as $key => $val) {
			if (is_int($key)) {
				$headers .= $val;
			} else {
				$headers .= "{$key}: {$val}" . $this->eol();
			}
		}

		return $headers;
	}

	/**
	 * Add html message
	 * @return object instance of Speedy\Mailer
	 */
	public function setHtmlMessage($html) {
		$this->_message .= $this->eol() . $this->eol() . "--" . $this->multiPartBoundary() . $this->eol();
		$this->_message .= "Content-type: text/html; charset=utf-8" . $this->eol();
		$this->_message .= "Content-Transfer-Encoding: quoted-printable" . $this->eol() . $this->eol();
		$this->_message .= $html . $this->eol() . $this->eol();
		return $this;
	}

	/**
	 * Add plain message for mailer
	 * @return object instance of Speedy\Mailer
	 */
	public function setPlainMessage($plain) {
		$this->_message .= $this->eol() . $this->eol() . "--" . $this->multiPartBoundary() . $this->eol();
		$this->_message .= "Content-type: text/plain; charset=utf-8" . $this->eol();
		$this->_message .= "Content-Transfer-Encoding: 8bit" . $this->eol() . $this->eol();
		$this->_message .= $plain . $this->eol() . $this->eol();
		return $this;
	}

	/**
	 * Getter for multipart boundary
	 * @return string
	 */
	private function multiPartBoundary() {
		if (!$this->_multiPartBoundary) {
			$this->_multiPartBoundary = uniqid('np');
		}	

		return $this->_multiPartBoundary;
	}

}
?>