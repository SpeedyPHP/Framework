<?php 
namespace Speedy\Model\ActiveRecord;

class Base extends \Speedy\ActiveRecord\Model {
	
	public function __construct(array $attributes=array(), $guard_attributes=true, $instantiating_via_find=false, $new_record=true) {
		parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
			
	}
	
}
?>