<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require dirname(__FILE__).'/Modules.php';

class MX_Router extends CI_Router
{
	public $module;
	private $located = 0;

	public function fetch_module()
	{
		return $this->module;
	}

	protected function _set_request($segments = array())
	{
		if ($this->translate_uri_dashes === TRUE)
		{
			foreach(range(0, 2) as $v)
			{
				isset($segments[$v]) && $segments[$v] = str_replace('-', '_', $segments[$v]);
			}
		}
		
		$segments = $this->locate($segments);

		if($this->located == -1)
		{
			$this->_set_404override_controller();
			return;
		}

		if(empty($segments))
		{
			$this->_set_default_controller();
			return;
		}
		
		$this->set_class($segments[0]);
		
		if (isset($segments[1]))
		{
			$this->set_method($segments[1]);
		}
		else
		{
			$segments[1] = 'index';
		}
       
		array_unshift($segments, NULL);
		unset($segments[0]);
		$this->uri->rsegments = $segments;
	}
	
	protected function _set_404override_controller()
	{
		$this->_set_module_path($this->routes['404_override']);
	}

	protected function _set_default_controller()
	{
		if (empty($this->directory))
		{
			$this->_set_module_path($this->default_controller);
		}

		parent::_set_default_controller();
		
		if(empty($this->class))
		{
			$this->_set_404override_controller();
		}
	}

	public function locate($segments)
	{
		$this->located = 0;
		$ext = $this->config->item('controller_suffix').EXT;

		if (isset($segments[0]) && $routes = Modules::parse_routes($segments[0], implode('/', $segments)))
		{
			$segments = $routes;
		}

		list($module, $directory, $controller) = array_pad($segments, 3, NULL);

		foreach (Modules::$locations as $location => $offset)
		{
			if (is_dir($source = $location.$module.'/controllers/'))
			{
				$this->module = $module;
				$this->directory = $offset.$module.'/controllers/';

				if($directory)
				{
					if(is_dir($source.$directory.'/'))
					{	
						$source .= $directory.'/';
						$this->directory .= $directory.'/';

						if($controller)
						{
							if(is_file($source.ucfirst($controller).$ext))
							{
								$this->located = 3;
								return array_slice($segments, 2);
							}
							else $this->located = -1;
						}
					}
					else
					if(is_file($source.ucfirst($directory).$ext))
					{
						$this->located = 2;
						return array_slice($segments, 1);
					}
					else $this->located = -1;
				}

				if(is_file($source.ucfirst($module).$ext))
				{
					$this->located = 1;
					return $segments;
				}
			}
		}

		if( ! empty($this->directory)) return;

		if($directory)
		{
			if(is_file(APPPATH.'controllers/'.$module.'/'.ucfirst($directory).$ext))
			{
				$this->directory = $module.'/';
				return array_slice($segments, 1);
			}

			if($controller)
			{ 
				if(is_file(APPPATH.'controllers/'.$module.'/'.$directory.'/'.ucfirst($controller).$ext))
				{
					$this->directory = $module.'/'.$directory.'/';
					return array_slice($segments, 2);
				}
			}
		}

		if (is_dir(APPPATH.'controllers/'.$module.'/'))
		{
			$this->directory = $module.'/';
			return array_slice($segments, 1);
		}

		if (is_file(APPPATH.'controllers/'.ucfirst($module).$ext))
		{
			return $segments;
		}
		
		$this->located = -1;
	}

	protected function _set_module_path(&$_route)
	{
		if ( ! empty($_route))
		{
			$sgs = sscanf($_route, '%[^/]/%[^/]/%[^/]/%s', $module, $directory, $class, $method);
			
			if ($this->locate(array($module, $directory, $class)))
			{
				switch ($sgs)
				{
					case 1:	$_route = $module.'/index';
						break;
					case 2: $_route = ($this->located < 2) ? $module.'/'.$directory : $directory.'/index';
						break;
					case 3: $_route = ($this->located == 2) ? $directory.'/'.$class : $class.'/index';
						break;
					case 4: $_route = ($this->located == 3) ? $class.'/'.$method : $method.'/index';
						break;
				}
			}
		}
	}

        public function set_class($class)
        {
            $suffix = $this->config->item('controller_suffix');
		if ($suffix && strpos($class, $suffix) === FALSE)
            {
                $class .= $suffix;
            }
            parent::set_class($class);
        } 
}	