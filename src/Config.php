<?php
/**
 * 配置文件读取
 * @author Artherson <695417298@qq.com>
 * @date 2019年08月27日
 */
namespace pwp;

class Config {

    public $config=[];
    //1.从项目根目录/config 遍历读取配置文件

    private function __construct(){
        $path = ROOT_PATH.'/app/config/config.php';
        $this->config = include($path);
        $this->loadFunc();
    }

    static public function getConfig(){
        $config  = new Config();
        return $config->config;
    }

    public function loadFunc(){
        if(is_file(__DIR__.'/Function.php')){
            require_once(__DIR__.'/Function.php');
        }
    }
}