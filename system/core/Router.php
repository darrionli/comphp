<?php
/**
*
*/
class FavCI_Router
{
	// 路由列表
	public $route = array();

	// 类
	public $class = '';

	// 方法
	public $method = 'index';

	// 目录
	public $directory;

	// 默认控制器
	public $default_controller;

	public $translate_uri_dashes = FALSE;

	public function __construct($router = NULL)
	{
		$this->uri =& load_class('URI', 'core');
		$this->_set_routing();
	}

	protected function _set_routing()
	{
		if(file_exists(APPPATH . 'config/routes.php')){
			include(APPPATH.'config/routes.php');
		}

		if (isset($route) && is_array($route))
		{
			isset($route['default_controller']) && $this->default_controller = $route['default_controller'];
			isset($route['translate_uri_dashes']) && $this->translate_uri_dashes = $route['translate_uri_dashes'];
			unset($route['default_controller'], $route['translate_uri_dashes']);
			$this->routes = $route;
		}

		if ($this->uri->uri_string !== '')
		{
			$this->_parse_routes();
		}
		else
		{
			$this->_set_default_controller();
		}
	}

	protected function _parse_routes()
	{
		$this->_set_request(array_values($this->uri->segments));
	}

	protected function _set_request($segments = array())
	{
		$segments = $this->_validate_request($segments);
		if (empty($segments))
		{
			$this->_set_default_controller();
			return;
		}

		if ($this->translate_uri_dashes === TRUE)
		{
			$segments[0] = str_replace('-', '_', $segments[0]);
			if (isset($segments[1]))
			{
				$segments[1] = str_replace('-', '_', $segments[1]);
			}
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

	// 提取并设置URI中的目录部分
	protected function _validate_request($segments)
	{
		$c = count($segments);
		$directory_override = isset($this->directory);
		while ($c-- > 0)
		{
			$test = $this->directory
				.ucfirst($this->translate_uri_dashes === TRUE ? str_replace('-', '_', $segments[0]) : $segments[0]);
			if ( ! file_exists(APPPATH.'controllers/'.$test.'.php')
				&& $directory_override === FALSE
				&& is_dir(APPPATH.'controllers/'.$this->directory.$segments[0])
			)
			{
				$this->set_directory(array_shift($segments), TRUE);
				continue;
			}

			return $segments;
		}

		return $segments;
	}

	// 设置默认的控制器
	protected function _set_default_controller()
	{
		if (empty($this->default_controller))
		{
			exit('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
		}

		if (sscanf($this->default_controller, '%[^/]/%s', $class, $method) !== 2)
		{
			$method = 'index';
		}

		if ( ! file_exists(APPPATH.'controllers/'.$this->directory.ucfirst($class).'.php'))
		{
			exit('controllers/'.$this->directory.ucfirst($class).'.php'.'不存在');
			return;
		}

		$this->set_class($class);
		$this->set_method($method);

		$this->uri->rsegments = array(
			1 => $class,
			2 => $method
		);
	}


	public function set_class($class)
	{
		$this->class = str_replace(array('/', '.'), '', $class);
	}


	public function fetch_class()
	{
		return $this->class;
	}


	public function set_method($method)
	{
		$this->method = $method;
	}

	/**
	 * Set directory name
	 */
	public function set_directory($dir, $append = FALSE)
	{
		if ($append !== TRUE OR empty($this->directory))
		{
			$this->directory = str_replace('.', '', trim($dir, '/')).'/';
		}
		else
		{
			$this->directory .= str_replace('.', '', trim($dir, '/')).'/';
		}
	}
}
