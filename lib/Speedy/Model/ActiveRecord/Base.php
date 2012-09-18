<?php 
namespace Speedy\Model\ActiveRecord;

class Base extends \Speedy\ActiveRecord\Model {
	
	static $before_save;
	
	static $before_create;
	 
	static $before_update;
	
	static $before_validation;
	
	static $before_validation_on_create;
	
	static $before_validation_on_update;
	
	static $before_destroy;
	
	static $after_save;
	
	static $after_create;
	
	static $after_update;
	
	static $after_validation;
	
	static $after_validation_on_create;
	
	static $after_validation_on_update;
	
	static $after_destroy;
	
	
	
	public function __construct(array $attributes=array(), $guard_attributes=true, $instantiating_via_find=false, $new_record=true) {
		$this->_construct();
		
		parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
	}
	
	public function _construct() {}
	
}
?>