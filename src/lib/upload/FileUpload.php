<?php

namespace pwp\lib\upload;

class FileUpload implements UploadInterface
{
    public $config = [];
    public $path;
    public $errorMsg;

    public function __construct($config){
        $this->config = $config;
        $this->path = '/'.$this->config['upload_dir'].'/'.date('Y-m-d');
        //dump($_FILES);exit;
        if(!$this->checkdir()){
            throw new \Exception($this->errorMsg);
        }
        
        if(!is_writable($this->path)){
            $this->errorMsg = '文件夹不存在';
        }
    }

    public function save($file){
        $file_name = md5(time()).'_'.$file['name'];
        $path = $this->path.'/'.$file_name;
        move_uploaded_file($_FILES['file']['tmp_name'],ROOT_PATH.$path);
        $ret = [];
        $ret['code'] = 0;
        $ret['msg'] = '';
        $ret['data']['src'] = $path;
        return json_encode($ret);
    }

    //创建文件目录
    public function checkdir(){
        $path = ROOT_PATH.$this->path;
        if(is_dir($path)){
            return true;
        }
        try{
            mkdir($path,0777,true);
        }catch(\Exception $e){
            $this->errorMsg = "上传文件目录创建失败{$path},请检查权限";
            return false;
        }
        return true;
    }

    public function getError(){
        return $this->errorMsg;
    }

}