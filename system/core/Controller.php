<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (PHP_VERSION_ID >= 80000 && class_exists('Attribute', false) && !class_exists('AllowDynamicProperties', false)) {
	#[Attribute(Attribute::TARGET_CLASS)]
	class AllowDynamicProperties {}
}

#[AllowDynamicProperties]
class CI_Controller {
	private static $instance;
	public $load;
	public $benchmark;
	public $hooks;
	public $config;
	public $log;
	public $utf8;
	public $uri;
	public $exceptions;
	public $router;
	public $output;
	public $security;
	public $input;
	public $lang;
	public $db;
	public $session;
	public $encryption;
	public $language;
	public $sendmail;
	public $loop;
	public function __construct()
	{
		self::$instance =& $this;
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}
		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
	}

	public static function &get_instance()
	{
		return self::$instance;
	}

	public function __set($name, $value)
	{
		$this->$name = $value;
	}
}