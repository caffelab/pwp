<?php

namespace pwp\lib;

class Image
{
    public $gd_info = [];
    public function __construct(){
        $this->gd_info = gd_info();
    }
}