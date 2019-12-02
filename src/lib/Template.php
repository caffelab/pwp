<?php
namespace pwp\lib;
use pwp;
use pwp\Config;

class Template {
    public $config;
    public $parser;
    public $viewPath;
    public $cachePath;
    public $var = [];
    public $debug = [];
    
    public function __construct($viewPath=null,$cachePath=null){
        $config = Config::getConfig();
        $this->config = $config['template'];
        $cachePath = $this->config['cache_path'];
        $this->debug['begin'] = microtime(true);
        if(!empty($viewPath)){
            if($this->checkDir($viewPath)){
                $this->viewPath = $viewPath;
            }
        }
        if(!empty($cachePath)){
            
            $cachePath = $this->config['cache_path'].'/'.$this->cachePath;
            if($this->checkDir($cachePath)){
                $this->cachePath = $cachePath;
            }
        }
        
        $this->getParser();
    }

    protected function getParser(){
        $this->parser = [
            $this->config['right_delimit']."$%%".$this->config['left_delimit'] => '<?=$\1; ?>',
            $this->config['right_delimit']."foreach %%".$this->config['left_delimit'] => '<?php foreach(\1): ?>',
            $this->config['right_delimit'].'/foreach'.$this->config['left_delimit'] => '<?php endforeach ?>',
            $this->config['right_delimit'].'include %%'.$this->config['left_delimit'] => '',
            $this->config['right_delimit'].'if %%'.$this->config['left_delimit'] => '<?php if(\1): ?>',
            $this->config['right_delimit'].'/if'.$this->config['left_delimit'] => '<?php endif ?>',
            $this->config['right_delimit'].'volist %%'.$this->config['left_delimit'] => '<?php foreach(\1): ?>',
            $this->config['right_delimit'].'/volist'.$this->config['left_delimit'] => '<?php endforeach?>',
            $this->config['right_delimit'].':%%'.$this->config['left_delimit'] => '<?php \1; ?>',
            $this->config['right_delimit'].'else'.$this->config['left_delimit'] => '<?php else: ?>',
            $this->config['right_delimit'].'elseif %%'.$this->config['left_delimit'] => '<?php elseif(\1): ?>'
        ];
    }

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
        if(is_array($value)){
            foreach($value as $k => $v){
                $this->var[$k] = $v;
            }
        }
    }

    public function display($viewName,$isInclude=true,$uri=null){
        $viewPath = rtrim($this->viewPath,'/').'/'.$viewName.'.'.$this->config['prefix'];
        $cachePath = rtrim(ROOT_PATH,'/').'/'.$this->cachePath;
        if(!file_exists($viewPath)){
            throw new \Exception("{$viewPath}模板文件不存在");
        }
        //如果在调试模式下,不需要缓存文件
            if($this->config['view_layout']==0){
                $cacheName = md5($viewName.$uri).'.php';
                $cachePath = rtrim($cachePath,'/').'/'.$cacheName;
                //echo $cachePath;exit;
                if(!file_exists($cachePath)){
                    $php = $this->compile($viewPath);
                    file_put_contents($cachePath,$php);
                }
                $isTimeOut = (filectime($cachePath)+$this->config['life_time'])>time()?true:false;
                $isChange = filemtime($viewPath)>filemtime($cachePath)?true:false;
                if($isTimeOut||$isChange){
                    $this->var['run_time'] = sprintf('%.6f',microtime(true)-START_TIME);
                    $php = $this->compile($viewPath);
                    file_put_contents($cachePath,$php);
                }
                if($isInclude){
                    $this->var['run_time'] = sprintf("%.6f",microtime(true)-START_TIME);
                    extract($this->var);
                    include $cachePath;
                }
            }
    }

    protected function compile($filePath){
        $html = file_get_contents($filePath);

        foreach($this->parser as $key => $value){
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
        $fileName = trim($data[1], '\'"');
        $html = $this->compile($fileName);
        $cacheName = md5($fileName).'.php';
        $cachePath = rtrim($this->cachePath, '/').'/'.$cacheName;
        return '<?php include "'.$cachePath.'"?>';
    }
}