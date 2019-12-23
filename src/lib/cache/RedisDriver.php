<?php
namespace pwp\lib\cache;

class RedisDriver implements CacheInterface{
    protected $config = [];
    protected $driver;

    public function __construct($config=[]){
        $this->config = array_merge([
            'server' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'group' => 0,
        ],(array)$config);
        $this->driver = new \Redis();
        $this->driver->connect($this->config['server'],$this->config['port']);
        if($this->config['password']){
            $this->driver->auth($this->config['password']);
        }
        $this->driver->select($this->config['group']);
    }

    public function get($key){
        return $this->driver->get($key);
    }

    public function set($key,$value,$expire_time=1800){
        if($expire_time){
            $this->driver->setex($key,$expire_time,$value);
        }
        return $this->driver->set($key,$value);
    }

    public function inc($key,$value=1){
        return $this->driver->incrBy($key,$value);
    }

    public function des($key,$value=1){
        return $this->driver->decrBy($key,$value);
    }

    public function del($key){
        return $this->driver->delete($key);
    }

    public function clear(){
        return $this->driver->flushDb();
    }
}