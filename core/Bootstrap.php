<?php
namespace core;

class Bootstrap
{
	public static function run()
	{
		self::parseUri();
	}

	// 拆分URL
	public static function parseUri()
	{
		$request = $_SERVER['REQUEST_URI'];
		$segment = explode("/", $request);

		$class = isset($segment[2]) ? ucfirst($segment[2]) : 'Index';
		$method = isset($segment[3]) ? $segment[3] : 'index';
		$class = "\app\controller\\" . $class;
		// (new $class)->$method();
		call_user_func([(new $class), $method]);
	}
}
