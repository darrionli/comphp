<?php

require_once(SYSPATH . '/core/Common.php');

$URI =& load_class('URI', 'core');

$RTR =& load_class('Router', 'core');

$e404 = FALSE;
$class = ucfirst($RTR->class);
$method = $RTR->method;

$reqFile = APPPATH . 'controllers'. DIRECTORY_SEPARATOR . $RTR->directory.$class.'.php';
if(empty($class) OR !file_exists($reqFile)){
	$e404 = TRUE;
}else{
	require_once($reqFile);
	if ( ! class_exists($class, FALSE) OR $method[0] === '_' OR method_exists('FavCI_Controller', $method))
	{
		$e404 = TRUE;
	}
	elseif ( ! method_exists($class, $method))
	{
		$e404 = TRUE;
	}
	elseif ( ! is_callable(array($class, $method)))
	{
		$reflection = new ReflectionMethod($class, $method);
		if ( ! $reflection->isPublic() OR $reflection->isConstructor())
		{
			$e404 = TRUE;
		}
	}
}

if($e404){
	exit('Not Found!');
}

$param = ['Hello'];
$FavCI = new $class();
call_user_func_array([&$FavCI, $method], $param);
