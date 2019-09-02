<?php

namespace pwp;

class Kernel
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->autoload();
        return true;
    }
    //自动加载
    protected function autoload(){
        spl_autoload_register(__CLASS__,'loadClass');
    }


}