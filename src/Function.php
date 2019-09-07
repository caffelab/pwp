<?php
function dump($arr){
    echo '<pre>';
    var_dump($arr);
    echo '</pre>';
}

function instance($className,$layout='model'){
    return \pwp\Pwp::instance($className,$layout);
}