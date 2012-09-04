<?php
namespace Speedy\Utility;


define('ARRAY_ACCESS_VS', '.');

trait ArrayAccess {
	
	private $__aaCurrent;
	//private $__aaCurrentIds;
	
	
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
	
	
		$parts = explode(ARRAY_ACCESS_VS, $name);
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
		if (!empty($array[$name])) {
			return $array[$name];
		}
	
	
		$parts = explode(ARRAY_ACCESS_VS, $name);
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
		$keys 	= explode(ARRAY_ACCESS_VS, $name);
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
	
	protected function __dotMutateData($data) {
		if (empty($data)) return [];
		
		$mutated = [];
		foreach ($data as $key => $value) {
			$keys   = explode("/", $key);
			$total  = count($keys);
			$current        =& $mutated;
			//$currentIds     =& $this->__aaCurrentIds;
		
			for ($i = 0; $i < $total-1; $i++) {
				if (empty($current[$keys[$i]])) {
					$current[$keys[$i]]     = array();
					//$currentIds[$keys[$i]]  = array();
				}
		
				$current	=& $current[$keys[$i]];
				//$currentIds =& $currentIds[$keys[$i]];
			}
		
			$current[$keys[$i]]     = $value;
			//$currentIds[$keys[$i]]  = $result['Config']['id'];
		}
		
		return $mutated;
	}
	
}
?>