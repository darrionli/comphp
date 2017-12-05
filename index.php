<?php
/**
 * 入口文件
 */
define('BASEPATH', __DIR__);

define('APPPATH', BASEPATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);

define('SYSPATH', BASEPATH . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);

require_once SYSPATH . 'core/FavCI.php';
