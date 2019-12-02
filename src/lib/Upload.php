<?php

namespace pwp\lib;
use pwp\Config;

class Upload
{
    protected $config;
    protected $driver;
    protected $errorMsg;
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $config = Config::getConfig();
        $type = $config['upload'];
        $this->config = $config['uploadDriver'][$type];
        $object = ucfirst($type).'Upload';
        $str = __NAMESPACE__.'\upload\\'.$object;
        $this->driver = new $str($this->config);
    }

    public function upload(){
        $file = $_FILES['file'];
        return $this->driver->save($file);
    }

    /**
     * 判断文件是否为指定类型
     *
     * @return bool
     */
    public function checkFile(){
        $tmp = explode('.',$_FILES['file']['name']);
        //扩展名获取
        $extension = end($tmp);
        //判断是不是指定类型的文件
        $arr = explode('/',$this->config['file_prefix']);
        if(!in_array($_FILES['file']['type'],$arr)){
            $this->errorMsg = '文件类型错误';
            return false;
        }
        //判断是不是图片,图片默认均可上传
        if((!(($_FILES['file']['type']=='image/gif')
            ||($_FILES['file']['type']=='image/jpeg')
            ||($_FILES['file']['type']=='image/jpg')
            ||($_FILES['file']['type']=='image/png')
            ||($_FILES['file']['type']=='image/pjpeg')
            ||($_FILES['file']['type']=='image/x-png'))
        )&&in_array($_FILES['file']['type'],$arr)){
            $this->errorMsg = '非法图像格式';
            return false;
        }
        
        //判断文件大小
        if($_FILES['file']['size']>$this->config['max_size']){
            $this->errorMsg = '文件超过大小';
        }
        return true;
    }

    public function getErrorMsg(){
        return $this->errorMsg;
    }
}