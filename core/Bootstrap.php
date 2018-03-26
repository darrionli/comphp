<?php
namespace core;

use core\Uri;

class Bootstrap
{
	public static function run()
	{
		// Uri::parseUrl();
		self::parseUrl();
	}

	/**
	 * 解析REQUEST_URI并自动检测URI
	 * 必要时修复查询字符串
	 */
	public static function parseUrl()
	{
		$request = $_SERVER['REQUEST_URI'];
		$segment = explode("/", $request);

		$class = isset($segment[2]) ? ucfirst($segment[2]) : 'Index';
		$method = isset($segment[3]) ? $segment[3] : 'index';
		$class = "\app\controller\\" . $class;
		echo (new $class)->$method();
		// call_user_func([(new $class), $method]);
	}

	// 解析URI，并修复查询字符串
	public static function parseUri()
	{
		if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
		{
			return '';
		}

		$uri = parse_url('http://dummy' . $_SERVER['REQUEST_URI']);
		$query = isset($uri['query']) ? $uri['query'] : '';
		$path = isset($uri['path']) ? $uri['path'] : '';

		if (isset($_SERVER['SCRIPT_NAME'][0]))
		{
			if (strpos($path, $_SERVER['SCRIPT_NAME']) === 0)
			{
				$path = (string) substr($path, strlen($_SERVER['SCRIPT_NAME']));
			}
			elseif (strpos($path, dirname($_SERVER['SCRIPT_NAME'])) === 0)
			{
				$path = (string) substr($path, strlen(dirname($_SERVER['SCRIPT_NAME'])));
			}
		}

		if (trim($path, '/') === '' && strncmp($query, '/', 1) === 0)
		{
			$query = explode('?', $query, 2);
			$path = $query[0];
			$_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
		}
		else
		{
			$_SERVER['QUERY_STRING'] = $query;
		}

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		if ($path === '/' OR $path === '')
		{
			return '/';
		}
	}

}
