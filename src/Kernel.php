<?php

namespace pwp;

use EasyWeChat\Kernel\Exceptions\Exception;

class Kernel
{
    //static public $instance;
    public static $classes = [];

    public function run(){
        $whoops = new \Whoops\Run;
        $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
        $this->autoload();
        $this->router();
    }

    public function autoload(){
        spl_autoload_register([__CLASS__,'loadClass']);
    }

    public function loadClass($class){
        $classFile = str_replace(['\\'],'/',$class).".php";
        $file = ROOT_PATH.$classFile;
        if (!isset(self::$classes[$file])) {
            if (!file_exists($file)) {
                return false;
            }
            self::$classes[$classFile] = $file;
            require_once $file;
        }
        return true;
    }
    protected function router(){
        $url = $_SERVER['REQUEST_URI'];
        $url = trim($url,'/');
        $arr = explode('/',$url);
        $config = Config::getConfig();
        $router = $config['router'];
        if(!defined('APP_NAME')){
            define('APP_NAME',$router['app_name']);
        }
        $router = array_flip($router);
        if(!defined('LAYOUT_NAME')){
            define('LAYOUT_NAME',$router[$arr[0]]);
        }
        if(!defined('MODULE_NAME')){
            define('MODULE_NAME',$arr[1]);
        }
        if(!defined('ROLE_NAME')){
            define('ROLE_NAME',$arr[2]);
        }
        if(!defined('ACTION_NAME')){
            define('ACTION_NAME',$arr[3]);
        }
        $class = '\\'.APP_NAME.'\\'.MODULE_NAME.'\\'.LAYOUT_NAME.'\\'.ucfirst(ROLE_NAME).ucfirst(LAYOUT_NAME);
        //echo $class;
        if(!class_exists($class)){
             throw new Exception("<h1>{$class} is Not Exits</h1>");
        }
        $action = ACTION_NAME;
        $module = new $class();
        $module->$action();
    }
}