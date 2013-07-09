<?php 
namespace Speedy\Utility;



/**
 * SpeedyPHP Set object for manipulating arrays 
 * inspired by CakePHP Set
 *
 * @author Zachary Quintana
 * @since 1.0
 * @package Speedy
 * @subpackage Utility
 */
class Set {
	
	/**
	 * Turn object into an array
	 * mostly borrowed from CakePHP
	 * @param object $object
	 * @return array $array
	 */
	public static function toArray($object) {
		$array = [];
		if (is_a($object, 'XmlNode')) {
			$array = $object->toArray();
		} elseif (is_object($object)) {
			$props	= get_object_vars($object);
			if (isset($props['_name_'])) {
				$name	= $props['_name_'];
				unset($props['_name_']);
			}
			
			$new = [];
			foreach ($props as $prop => $value) {
				if (is_array($value)) {
					$new[$prop] = (array)self::toArray($value);
				} else {
					if (isset($value->_name_)) {
						$new = array_merge($new, self::toArray($value));
					} else {
						$new[$prop] = self::toArray($value);
					}
				}
			}
			
			if (isset($name)) 
				$array[$name]	= $new;
			else 
				$array	= $new;
		} elseif (is_array($object)) {
			foreach ($object as $key => $value) {
				$array[$key]	= self::toArray($value);
			}
		} else {
			$array = $object;
		}
		
		return $array;
	}
	
}
?>
