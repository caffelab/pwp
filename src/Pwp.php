<?php

namespace pwp;

use EasyWeChat\Kernel\Exceptions\Exception;

class Pwp
{
    //导入类的调用，类似于TP的model,action
    public static function instance($className,$layoutName='model'){
        $className = explode('/',$className);
        $count = count($className);
        $module = '';
        $role = '';
        if($count==1){
            $module = MODULE_NAME;
            $role = $className[0];
        }else if($count==2){
            $role = $className[0];
            $module = $className[1];
        }
        $class = '\\app\\'.$module.'\\'.$layoutName.'\\'.$role.\ucfirst($layoutName);
        if(!class_exists($class)){
            die("类{$class}不存在");
        }
        $obj = new \ReflectionClass($class);
        $ret = $obj->newInstance();
        return $ret;
    }
}