<?php
namespace pwp\lib;
use pwp\Config;

class Session {
    //自定义会话管理
    public $cache;
    public $session;
    public $config;

    public function __construct($session='defalt'){
        $this->session = $session;
        $config = Config::getConfig();
        $this->config = $config['session'];
        if(!isset($config['expire_time'])||$config['expire_time']<0){
            $this->config['expire_time'] = ini_get('session.gc_maxlifetime');
        }
        
        if($this->config['cache']){   
            session_set_save_handler(
                [$this,'_open'],
                [$this,'_close'],
                [$this,'_read'],
                [$this,'_write'],
                [$this,'_destory'],
                [$this,'_clean']
            );
        }
        if(!isset($_SESSION)){
            session_start();
        }
    }

    protected function _open($savePath,$sessionName){
        if($this->cache){
            return true;
        }
        $this->cache = cache($this->config['cache']);
        //dump($this->cache);
        return true;
    }

    protected function _close(){
        $this->cache = NULL;
        unset($this->cache);
        return true;
    }

    protected function _write($sessionId,$sessionData){
        return $this->cache->set($this->config['prefix'].$sessionId,json_encode($sessionData),$this->config['expire_time'])?true:false;
    }

    protected function _read($sessionId){
        $data = json_decode($this->cache->get($this->config['prefix'].$sessionId),true);
        if(is_array($data)){
            $data = json_encode($data);
        }
        return (string) $data;
    }

    protected function _destory($sessionId){
        return $this->cache->del($this->config['prefix'].$sessionId)>1?true:false;
    }

    protected function _clean($time){
        $this->cache->get('*');
        return true;
    }

    /**
     * 读取操作
     * @param $key 
     */
    public function get($key){
        return $_SESSION[$this->config['prefix'].$key];
    }
    /**
     * 写入操作
     * @param $key
     * @param $value
     */
    public function set($key,$value){
        return $_SESSION[$this->config['prefix'].$key] = $value;
    }

    /**
     * 删除会话内容
     * @param $key
     */
    public function del($key){
        unset($_SESSION[$this->config['prefix'].$key]);
    }

    /**
     * 清空会话
     */
    public function clean(){
        session_unset();
        session_destroy();
    }
}