<?php
namespace pwp\lib\template;
class TwigDriver {
    public $twig;
    public $env;
    public $templatePath;
    public $cachePath;
    public $val=[];
    /**
     * Class constructor.
     */
    public function __construct($templatePath='',$cachePath='')
    {
        $this->templatePath = $templatePath;
        $this->cachePath = $cachePath;
        $this->env = new \Twig\Loader\FilesystemLoader($this->templatePath);
        $this->twig = new \Twig\Environment($this->env,[
            'cache' => $this->cachePath,
        ]) ;
    }

    public function assign($name,$val){
        if(is_array($name)){
            $this->val = array_merge($this->val,$name);
        }
        $this->val[$name] = $val;
        //$this->val = array_merge($this->val,$arr);
        //dump($this->val);exit;
    }

    public function display($templateView=NULL){
        $tmp = $this->twig->load($templateView);
        echo $tmp->render($this->val);
    }
}