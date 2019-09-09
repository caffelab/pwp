<?php

namespace pwp;
use pwp\lib\Tpl;
use EasyWeChat\Kernel\Exceptions\Exception;
class Controller
{
    public $assign;
    public $tpl;
    public function __construct(){
        $this->tpl = new Tpl();
        $this->tpl->cacheDir = ROOT_PATH.'cache/';
    }

    public function assign($name,$value){
        $this->tpl->assign($name,$value);
    }

    public function display($pageName=''){
        $tmpDir = $this->getViewPath();
        $this->tpl->viewDir = $tmpDir;
        //echo $tmpDir;
        //echo __DIR__;
        if(!empty($pageName)){
            $page_name = $pageName;
        }else{
            $page_name = ACTION_NAME.'.html';
        }
        $this->tpl->display($page_name);
    }

    //目录为：app\system\view\api\index.html
    protected function getViewPath(){
        $tmpDir = ROOT_PATH.APP_NAME.'/'.MODULE_NAME.'/view/'.LAYOUT_NAME;
        if(!file_exists($tmpDir)){
            throw new Exception('模板文件不存在于'.$tmpDir);
        }
        return $tmpDir;
    }
}