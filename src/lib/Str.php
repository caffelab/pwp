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
        if(preg_match('/[\x7f-\xff]/',$str)){
            return true;
        }
        return false;
     }
    
     /**
      * cutArticle函数
      * 截取文章摘要
      *@return String $data
      */
    public static function cutArticle($data=NULL,$cut=0,$str='....'){
        $data = \strip_tags($data);
        $pattern = "/&[a-zA-Z]+;/";
        $cut = preg_match("/[\x7f-\xff]/", $data)?$cut*2:$cut;
        $data = \preg_replace($pattern,'',$data);
        if(!\is_numeric($cut)){
            return $data;
        }
        if($cut>0){
            $data = \mb_strimwidth($data,0,$cut,$str);
        }
        return $data;
    }

    /**
     * makeOrderId函数
     * 订单号生成
     * @param mixed $name
     * @return String 
     */
    public static function makeOrderId($prefix='default',$type='date'){
        //订单号随机生成
        if($prefix=='default'&&!$type=='date'){
            $arr = ['A','B','C','D','E','F','G'];
            $osn = $arr[intval(date('Y')-2019)].strtoupper(dechex('m')).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
            return $osn;
        }
        //订单号首字母被定义,后面的数字随机
        if(!($prefix=='default')&&!($type=='date')){
            $osn = $prefix.strtoupper(dechex('m')).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
            return $osn;
        }
        //订单首字母被定义，后面的数字是日期和随机数
        if(!($prefix=='default')&&$type=='date'){
            $osn = $prefix.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            return $osn;
        }
    }

    /**
     * isTel函数
     * 判断电话号码
     * @return bool
     */
    public static function isTel($str=NULL){
        $isTel = "/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!\preg_match($isTel,$str)){
            return false;
        }
        return true;
    }

    /**
     * isEmail函数
     * 判断电子邮件规则
     */
    public static function isEmail($str=NULL){
        $isEmail = '/^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/';
        if(!\preg_match($isEmail,$str)){
            return false;
        }
        return true;
    }

    /**
     * isIp函数
     * 判断是否是IP 支持IPv4和IPv6
     */
    public static function isIp($str=NULL){
        if(!filter_var($str,FILTER_VALIDATE_IP)){
            return false;
        }
        return true;
    }

    /**
     * str2py函数
     * 转换为拼音
     */
    public static function str2py($str=NULL){
        //判断是否是中文
        $pinyin = new Pinyin();
        if(!self::includeChinese($str)){
            return $str;
        }
        return $pinyin->str2pys($str);
    }
}