<?php

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['blog/(:num)'] = 'blog/article/$1';
