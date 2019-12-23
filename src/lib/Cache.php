<?php
namespace pwp\lib;
use pwp\Config;

class Cache {
    protected $driver;
    protected static $objArr = [];
    protected $cache;
    protected $config;
    protected $group = 0;

    public function __construct($cache='file',$group=0){
        $config = Config::getConfig();
        $config = $config['cache'];
        $this->cache = $cache;
        $this->config =$config;
        $cache_type = $config['cache_type'];
        $this->group = $config[$cache_type]['group'];
    }

    public function __call($method,$args){
        $key = md5($this->cache.$this->group);
        if(!isset(self::$objArr[$key])){
            $driver = __NAMESPACE__."\cache\\".ucfirst($this->config['cache_type'])."Driver";
            if(!class_exists($driver)){
                throw new Exception("类不存在");
            }
            self::$objArr[$key] = new $driver($this->config,$this->group);
        }
        return call_user_func_array([self::$objArr[$key],$method],$args);
    }
}