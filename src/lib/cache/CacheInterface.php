<?php
namespace pwp\lib\cache;

interface CacheInterface {

    public function get($key);

    public function set($key,$value,$expire=3600);

    public function inc($key,$value=1);

    public function des($key,$value=1);

    public function del($key);

    public function clear();
}