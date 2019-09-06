<?php

namespace pwp;

class Kernel
{
    static public function run(){

    }

    protected function router(){
        $url = $_SERVER['REQUEST_URI'];
        $url = trim($url,'/');
        $arr = explode('/',$url);
        if(!defined('APP_NAME')){
            define('APP_NAME','app');
        }
        $module_name = $arr[0];
        $layer_name = $arr[1];
        $role_name = $arr[2];
        $action_name = $arr[3];
        //LAYOUT 层名
        //MODULE 模块名
        //ACTION  函数名称
        //APPNAME 类名称
        //目录为  app\module\layout\appname.php/action
    }
}