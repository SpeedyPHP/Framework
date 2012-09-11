<?php
namespace Speedy\Utility;


define('ARRAY_ACCESS_DEFAULT_DELIMETER', '.');

trait ArrayAccess {
	
	private $__aaCurrent;
	
	private static $__aaDelimeter = ARRAY_ACCESS_DEFAULT_DELIMETER;
	//private $__aaCurrentIds;
	
	
	
	protected function __aaSetDelimeter($delimeter) {
		self::$__aaDelimeter = $delimeter;
		return $this;
	}
	
	protected function __dotIsset($name, &$array) {
		$value	= $this->__dotAccess($name, $array);
		return isset($value);
	}
	
	protected function __dotUnset($name, &$array) {
		if (!$array) return;
		if ($name === null) return;
		if (!empty($array[$name])) {
			return;
		}
	
	
		$parts = explode(self::$__aaDelimeter, $name);
		$return =& $array;
	
		for ($i = 0; $i < count($parts)-1; $i++) {
			if (isset($return[$parts[$i]]) && is_array($return[$parts[$i]])) {
				$return =& $return[$parts[$i]];
			} else {
				return;
			}
		}
	
		if (isset($return[$parts[$i]])) {
			unset($return[$parts[$i]]);
		}
	
		return;
	}
	
	/**
	 * Get data from array by dot accessor string
	 * @param string $name
	 * @param array $array
	 */
	protected function __dotAccess($name, &$array) {
		if (!$array)return null;
		if ($name === null) return $array;
		if (!is_array($name) && !empty($array[$name])) {
			return $array[$name];
		}
	
		if (is_array($name)) {
			$res = [];
			foreach ($name as $part) {
				$tmp	= $this->__dotAccess($part, $array);
				if (!is_array($tmp)) $tmp = [$tmp];
				
				$res = array_merge($res, $tmp);
			}
			
			return $res;
		}
	
		$parts = explode(self::$__aaDelimeter, $name);
		$return =& $array;
	
		for ($i = 0; $i < count($parts)-1; $i++) {
			if (isset($return[$parts[$i]]) && is_array($return[$parts[$i]])) {
				$return =& $return[$parts[$i]];
			} else {
				return null;
			}
		}
	
		if (isset($return[$parts[$i]])) {
			return $return[$parts[$i]];
		} else return null;
	
		return $return;
	}
	
	/**
	 * Dot string setter for array
	 * @param string $name
	 * @param mixed $value
	 * @param array $array
	 * @return $this;
	 */
	protected function __dotSetter($name, $value, &$array) {
		$keys 	= explode(self::$__aaDelimeter, $name);
		$total 	= count($keys);
		$current=& $array;
	
		for ($i = 0; $i < $total-1; $i++) {
			if (empty($current[$keys[$i]])) {
				$current[$keys[$i]] 	= array();
			}
	
			$current 	=& $current[$keys[$i]];
		}
	
		$current[$keys[$i]] 	= $value;
		return $this;
	}

	function mutateData($data) {
		if (empty($data)) return [];
	
		$mutated = [];
		foreach ($data as $key => $value) {
			$keys   = explode("/", $key);
			$total  = count($keys);
			$current        =& $mutated;
	
			for ($i = 0; $i < $total-1; $i++) {
				if (empty($current[$keys[$i]])) {
					$current[$keys[$i]]     = array();
				}
	
				$current	=& $current[$keys[$i]];
			}
			
			if (isset($current[$keys[$i]]) && !is_array($current[$keys[$i]])) {
				$current[$keys[$i]] 	= [$current[$keys[$i]], $value];
			} elseif (isset($current[$keys[$i]]) && is_array($current[$keys[$i]])) {
				$current[$keys[$i]][] 	= $value;
			} else {
				$current[$keys[$i]] 	= $value;
			}
		}
	
		return $mutated;
	}
	
	function mutateDataWithKeyValue($data, $key, $value) {
		if (empty($data)) return [];
	
		$mutated = [];
		foreach ($data as $item) {
			$keys   = explode("/", $item[$key]);
			$total  = count($keys);
			$current        =& $mutated;
	
			for ($i = 0; $i < $total-1; $i++) {
				if (empty($current[$keys[$i]])) {
					$current[$keys[$i]]     = array();
				}
	
				$current	=& $current[$keys[$i]];
			}
	
			if (isset($current[$keys[$i]]) && !is_array($current[$keys[$i]])) {
				$current[$keys[$i]][] 	= $item[$value];
			} elseif (isset($current[$keys[$i]]) && is_array($current[$keys[$i]])) {
				$current[$keys[$i]] 	= [$current[$keys[$i]], $item[$value]];
			} else {
				$current[$keys[$i]] 	= $item[$value];
			}
		}
	
		return $mutated;
	}
}
?>