<?php
function dump($arr){
    echo '<pre>';
    var_dump($arr);
    echo '</pre>';
}

function instance($className,$layout='model'){
    return \pwp\Pwp::instance($className,$layout);
}

function load_js($file_name){
    echo "<script src={$file_name}></script>";
}

function load_css($file_name){
    echo "<link rel='stylesheet' type='text/css' href={$file_name}></link>";
}

function is_debug(){
    $config = \pwp\Config::getConfig();
    return $config['debug'];
}