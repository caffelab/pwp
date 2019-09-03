<?php
/**
 * 配置文件读取
 * @author Artherson <695417298@qq.com>
 * @date 2019年08月27日
 */
namespace pwp;

class Config {

    protected static $config=[];
    //1.从项目根目录/config 遍历读取配置文件

    private function __construct(){
        $path = ROOT_PATH.'/app/config/config.php';
        $this->config = include($path);
    }

    static public function getConfig(){
        $config  = new Config();
        return $config::$config;
    }
}