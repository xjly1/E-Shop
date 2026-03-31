<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require dirname(__FILE__).'/Base.php';

class MX_Controller 
{
	public $autoload = array();
	public $load;
	
	public function __construct() 
	{
		$controller_suffix = CI::$APP->config->item('controller_suffix');
		$class = $controller_suffix !== null ? str_replace($controller_suffix, '', get_class($this)) : get_class($this);
		log_message('debug', $class." MX_Controller Initialized");
		Modules::$registry[strtolower($class)] = $this;	
		
		$this->load = clone load_class('Loader');
		$this->load->initialize($this);
		$this->load->_autoloader($this->autoload);
	}
	
	public function __get($class) 
	{
		return CI::$APP->$class;
	}
}