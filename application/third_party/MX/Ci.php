<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require_once dirname(__FILE__).'/Lang.php';
require_once dirname(__FILE__).'/Config.php';

class CI
{
	public static $APP;
	
	public function __construct() {
		self::$APP = CI_Controller::get_instance();
		global $LANG, $CFG;
		if ( ! $LANG instanceof MX_Lang) $LANG = new MX_Lang;
		if ( ! $CFG instanceof MX_Config) $CFG = new MX_Config;
	}
}

new CI;