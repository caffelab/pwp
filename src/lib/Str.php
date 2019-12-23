<?php
namespace pwp\lib;
class Str {
    /**
     * priceStr函数
     *格式化为价格格式
     * @return string
     */
    public static function priceStr($str=NULL){
        if(strlen($str)==0||is_null($str)){
            $str = 0;
        }
        return number_format($str,2,".","");
    } 

    /**
     * includeChinese函数
     * 判断是否是中文
     * @return bool
     */

     public static function includeChinese($str=NULL){
        if(preg_match('/[\x7f-xff]+$/',$str)){
            return true;
        }
        return false;
     }
    
}