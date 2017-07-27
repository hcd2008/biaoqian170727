<?php
// +----------------------------------------------------------------------
// | 后台入口
// +----------------------------------------------------------------------
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
header("Content-Type:text/html;charset=utf-8");
require __DIR__ . '/../thinkphp/base.php';
\think\Route::bind('admin');
\think\App::route(false);
\think\App::run()->send();