<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

(defined('EXT')) OR define('EXT', '.php');

global $CFG;

is_array(Modules::$locations = $CFG->item('modules_locations')) OR Modules::$locations = array(
	APPPATH.'modules/' => '../modules/',
);

spl_autoload_register('Modules::autoload');

class Modules
{
	public static $routes, $registry, $locations;

	public static function run($module) 
	{	
		$method = 'index';
		
		if(($pos = strrpos($module, '/')) != FALSE) 
		{
			$method = substr($module, $pos + 1);		
			$module = substr($module, 0, $pos);
		}

		if($class = self::load($module)) 
		{	
			if (method_exists($class, $method))	{
				ob_start();
				$args = func_get_args();
				$output = call_user_func_array(array($class, $method), array_slice($args, 1));
				$buffer = ob_get_clean();
				return ($output !== NULL) ? $output : $buffer;
			}
		}
		
		log_message('error', "Module controller failed to run: {$module}/{$method}");
	}
	
	public static function load($module) 
	{
		(is_array($module)) ? list($module, $params) = each($module) : $params = NULL;	
		
		$alias = strtolower(basename($module));

		if ( ! isset(self::$registry[$alias])) 
		{
			list($class) = CI::$APP->router->locate(explode('/', $module));
	
			if (empty($class)) return;
	
			$path = APPPATH.'controllers/'.CI::$APP->router->directory;
			$class = $class.CI::$APP->config->item('controller_suffix');
			self::load_file(ucfirst($class), $path);
			$controller = ucfirst($class);	
			self::$registry[$alias] = new $controller($params);
		}
		return self::$registry[$alias];
	}
	
	public static function autoload($class) 
	{	
		if (strstr($class, 'CI_') OR strstr($class, config_item('subclass_prefix'))) return;

		if (strstr($class, 'MX_')) 
		{
			if (is_file($location = dirname(__FILE__).'/'.substr($class, 3).EXT)) 
			{
				include_once $location;
				return;
			}
			show_error('Failed to load MX core class: '.$class);
		}
		
		if(is_file($location = APPPATH.'core/'.ucfirst($class).EXT)) 
		{
			include_once $location;
			return;
		}		
		
		if(is_file($location = APPPATH.'libraries/'.ucfirst($class).EXT)) 
		{
			include_once $location;
			return;
		}		
	}

	public static function load_file($file, $path, $type = 'other', $result = TRUE)	
	{
		$file = str_replace(EXT, '', $file);		
		$location = $path.$file.EXT;
		
		if ($type === 'other') 
		{			
			if (class_exists($file, FALSE))	
			{
				log_message('debug', "File already loaded: {$location}");				
				return $result;
			}	
			include_once $location;
		} 
		else 
		{
			include $location;

			if ( ! isset($$type) OR ! is_array($$type))				
				show_error("{$location} does not contain a valid {$type} array");

			$result = $$type;
		}
		log_message('debug', "File loaded: {$location}");
		return $result;
	}

	public static function find($file, $module, $base) 
	{
		$segments = explode('/', $file);

		$file = array_pop($segments);
		$file_ext = (pathinfo($file, PATHINFO_EXTENSION)) ? $file : $file.EXT;
		
		$path = ltrim(implode('/', $segments).'/', '/');	
		$module ? $modules[$module] = $path : $modules = array();
		
		if ( ! empty($segments)) 
		{
			$modules[array_shift($segments)] = ltrim(implode('/', $segments).'/','/');
		}	

		foreach (Modules::$locations as $location => $offset) 
		{					
			foreach($modules as $module => $subpath) 
			{			
				$fullpath = $location.$module.'/'.$base.$subpath;
				
				if ($base == 'libraries/' OR $base == 'models/')
				{
					if(is_file($fullpath.ucfirst($file_ext))) return array($fullpath, ucfirst($file));
				}
				else{
					if (is_file($fullpath.$file_ext)) return array($fullpath, $file);
				}
			}
		}
		
		return array(FALSE, $file);	
	}
	
	public static function parse_routes($module, $uri) 
	{
		if ( ! isset(self::$routes[$module])) 
		{
			if (list($path) = self::find('routes', $module, 'config/'))
			{
				$path && self::$routes[$module] = self::load_file('routes', $path, 'route');
			}
		}

		if ( ! isset(self::$routes[$module])) return;
			
		foreach (self::$routes[$module] as $key => $val) 
		{						
			$key = str_replace(array(':any', ':num'), array('.+', '[0-9]+'), $key);
			
			if (preg_match('#^'.$key.'$#', $uri)) 
			{							
				if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) 
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}
				return explode('/', $module.'/'.$val);
			}
		}
	}
}