<?php

namespace pwp\lib;

use EasyWeChat\Kernel\Exceptions\Exception;

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
    public $include=[];
    public $layout=null;

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

    /**
     * Undocumented function
     *
     * @return void
     */
    public function show($viewName,$isInclude=true,$uri=null){
        //获取
    }

    public function display($viewName,$isInclude=true,$uri=null){
        //拼接文件全路径
        $viewPath = rtrim($this->viewDir,'/').'/'.$viewName;
        //echo $viewPath;
        if(!file_exists($viewPath)){
            throw new Exception("{$viewPath}模板文件不存在");
        }
        //调试模式无缓存
        if(!is_debug()){
            //拼接缓存文件全路径
            $cacheName = md5($viewName.$uri).'.php';
            $cachePath = rtrim($this->cacheDir,'/').'/'.$cacheName;
            //判断缓存文件是否存在
            if(!file_exists($cachePath)){
                $php = $this->compile($viewPath,$viewName);
                file_put_contents($cachePath,$php);
            }else{
                $isTimeOut = (filectime($cachePath)+$this->lifeTime)>time()?true:false;
                $isChange = filemtime($viewPath)>filemtime($cachePath)?true:false;
                if($isTimeOut||$isChange){
                    $php = $this->compile($viewPath,$viewName);
                    file_put_contents($cachePath,$php);
                }

                if($isInclude){
                    extract($this->var);
                    include $cachePath;
                }
            }
        }else{
            $php = $this->compile($viewPath,$viewName);
            if(!is_null($this->layout)){
                foreach($this->include as $key=>$value){
                    $html=preg_replace($key,$value,$this->layout);
                    echo $html;
                }
            }
            echo $html;
            if($isInclude){
                extract($this->var);
            }
        }
        
        
    }

    protected function compile($filePath,$viewName){
        $html = file_get_contents($filePath);
        if($viewName=='layout.html'){
            $this->layout = $html;
        }
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
/*         if(!is_debug()){
            $fileName = trim($data[1],'\'"');
            $html = $this->compile($fileName,false);

//             $cacheName = md5($fileName).'.php';
//             $cachePath = rtrim($this->cacheDir,'/').'/'.$cacheName;
           return '<?php include "'.$cachePath.'"?>';

        }else{*/
            $fileName = trim($data[1],"\'||\"");
            $viewPath = rtrim($this->viewDir,'/').'/'.$fileName;
            $html=$this->compile($viewPath,$data[0]);
            $this->include[$data[0]] = $html;
//        }
    }
}