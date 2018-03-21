<?php
require 'vendor/autoload.php';

// 项目根目录
define('BASEPATH', __DIR__);

// app路径
define('APPPATH', BASEPATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);

core\Bootstrap::run();
