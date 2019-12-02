<?php

namespace pwp\lib;
use pwp\Config;
use EasyWeChat\Kernel\Exceptions\Exception;

class Tpl
{
    protected $config;
    protected $templatePath;
    protected $cachePath;
    protected $engine;
    public function __construct(){
        $config = Config::getConfig();
        $this->config = $config['template'];
        $this->engine = $this->config['engine'];
        $driver = '\\pwp\lib\template\\'.ucfirst($this->engine).'Driver';
        if(strlen($this->config['cache_path'])==0){
            $cachePath = ROOT_PATH.'cache';
        }else{
           $cachePath = ROOT_PATH.$this->config['cache_path']; 
        }
        if(!is_dir($cachePath)){
            mkdir($cachePath);
        }
        if(!is_writable($cachePath)){
            chmod($cachePath,0777);
        }
        if(strlen($this->config['template_path'])==0){
            $templatePath = ROOT_PATH.APP_NAME.'/'.MODULE_NAME.'/view/'.LAYOUT_NAME.'/';
        }else{
            $templatePath = $this->config['template_path'].MODULE_NAME.'/view/'.LAYOUT_NAME.'/';
        }
        if(!is_dir($templatePath)){
            throw new Exception("模板目录不存在");
        }
        $this->engine = new $driver($templatePath,$cachePath);
    }

    public function assign($name,$val){
        $this->engine->assign($name,$val);
    }

    public function display($viewName=''){
        if(strlen($viewName)==0){
            $viewName = ACTION_NAME.'.'.$this->config['prefix'];
        }
        return $this->engine->display($viewName);
    }
}