<?php
namespace core;

class Bootstrap 
{
	public static function run()
	{
		self::parseUrl();
	}

	// 拆分URL
	public static function parseUrl()
	{
		$request = $_SERVER['REQUEST_URI'];
		$segment = explode("/", $request);
	
		$class = isset($segment[2]) ? ucfirst($segment[2]) : 'Index';
		$method = isset($segment[3]) ? $segment[3] : 'index';
		$class = "\app\controller\\" . $class;
		(new $class)->$method();
	}
}