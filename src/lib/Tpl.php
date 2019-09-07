<?php

namespace pwp\lib;

class Tpl
{
    public $viewDir;
    public $cacheDir;
    public $lifeTime =100;
    public $var = [];
    public $tag = [
        '{$%%}' => '<?=$\1; ?>',
        '{foreach %%}' => '<?php foreach(\1): ?>',
        '{/foreach}' => '<?php endforeach ?>',
        '{include %%}' => '',
        '{if %%}' => '<?php if(\1): ?>',
        '{/if}' => '<?php endif ?>',
        '{volist %%}' => '<?php foreach(\1): ?>',
        '{/volist}' => '<?php endforeach?>',
        '{:%%}' => '<?php \1; ?>',
        '{else}' => '<?php else: ?>',
        '{elseif %%}' => '<?php elseif(\1): ?>'
    ];

    public function __construct($viewDir=null,$cacheDir=null,$lifeTime=null){
        if(!empty($viewDir)){
            if($this->checkDir($viewDir)){
                $this->viewDir = $viewDir;
            }
        }
        if(!empty($cacheDir)){
            if($this->checkDir($cacheDir)){
                $this->cacheDir = $cacheDir;
            }
        }
        if(!empty($lifeTime)){
            $this->lifeTime = $lifeTime;
        }
    }

    //判断路径是否存在
    protected function checkDir($dirPath){
        //如果目录不存在
        if(!file_exists($dirPath)||!is_dir($dirPath)){
            return mkdir($dirPath,0755,true);
        }
        if(!is_readable($dirPath)||!is_writable($dirPath)){
            return chmod($dirPath,0755);
        }
        return true;
    }

    public function assign($name,$value){
        $this->var[$name] = $value;
    }

    public function display($viewName,$isInclude=true,$uri=null){
        //拼接文件全路径
        $viewPath = rtrim($this->viewDir,'/').'/'.$viewName;
        //echo $viewPath;
        if(!file_exists($viewPath)){
            die('模板文件不存在');
        }
        //拼接缓存文件全路径
        $cacheName = md5($viewName.$uri).'.php';
        $cachePath = rtrim($this->cacheDir,'/').'/'.$cacheName;
        //判断缓存文件是否存在
        if(!file_exists($cachePath)){
            $php = $this->compile($viewPath);
            file_put_contents($cachePath,$php);
        }else{
            $isTimeOut = (filectime($cachePath)+$this->lifeTime)>time()?true:false;
            $isChange = filemtime($viewPath)>filemtime($cachePath)?true:false;
            if($isTimeOut||$isChange){
                $php = $this->compile($viewPath);
                file_put_contents($cachePath,$php);
            }

            if($isInclude){
                extract($this->var);
                include $cachePath;
            }
        }
        
    }

    protected function compile($filePath){
        $html = file_get_contents($filePath);
        foreach($this->tag as $key => $value){
            $pattern = '#'.str_replace('%%','(.+?)',preg_quote($key,'#')).'#';
            if(strstr($pattern,'include')){
                $html = preg_replace_callback($pattern,[$this,'parseInclude'],$html);
            }else{
                $html = preg_replace($pattern,$value,$html);

            }
        }
        return $html;
    }

    protected function parseInclude($data){
        $fileName = trim($data[1],'\'"');
        $this->display($fileName,false);
        $cacheName = md5($fileName).'.php';
        $cachePath = rtrim($this->cacheDir,'/').'/'.$cacheName;
        return '<?php include "'.$cachePath.'"?>';
    }
}