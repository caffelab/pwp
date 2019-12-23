<?php
/**
 * 日志类封装
 * 日志标准化写入，提取
 */
namespace pwp\lib;
use pwp\Config;

class Log{
    public $logPath;
    public $logLevel;
    public $fileName;
    public $config;
    protected $format = 'Y/m/d H:i:s';

    public function __construct(){
        $config = Config::getConfig();
        $this->config = $config['log'];
        $this->logPath = ROOT_PATH.$this->config['log_path'];
        if(!is_dir($this->logPath)){
            $status = mkdir($this->logPath,0777);
            if(!$status){
                throw new Exception("创建缓存目录失败:".$this->logPath."请检查配置文件");
            }
        }
        //初始化创建一个文件，文件名为当天日期
        $this->fileName = date('Y-m-d').".".$this->config['log_prefix'];
        $status = touch($this->logPath.DIRECTORY_SEPARATOR.$this->fileName);
        if(!$status){
            throw new Exception("log文件创建失败,请检查文件夹：".$this->logPath."的权限");
        }
    }

    /**
     * 写入文件操作
     * @param [string] $logLevel  日志等级
     * @param [string] $logMsg    日志内容
     * @return void
     */
    public function write($logLevel,$logMsg){
        $file = $this->logPath.'/'.$this->fileName;
        $fp = fopen($file,'w');
        $msg = "[".date($this->format)."]"."    ".$logLevel."      ".$logMsg."\r\n";
        fwrite($fp,$msg);
        fclose($fp);
    }

    public function read(){
        $file = $this->logPath.'/'.$this->fileName;
        $fp = fopen($file,'r');
        while(\feof($fp)){
            echo fgets($fp);
        }
    }

    public static function info($msg){
        $log = new Log();
        $log->write('INFO',$msg);
    }

    public static function err($msg){
        $log = new Log();
        $log->write("ERR",$msg);
    }

    public static function warn($msg){
        $log = new Log();
        $log->write("WARN",$msg);
    }

    
}