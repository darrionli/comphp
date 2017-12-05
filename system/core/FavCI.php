<?php

require_once(SYSPATH . '/core/Common.php');

$URI =& load_class('URI', 'core');

$RTR =& load_class('Router', 'core');

$e404 = false;
$class = ucfirst($RTR->class);
$method = $RTR->method;
echo $class.'|'.$method;


