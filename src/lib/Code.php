<?php
/**
 * 验证码类
 * @author ZCX (695417298@qq.com)
 */
namespace pwp\lib;

class Code {
    protected $number;
    protected $width;
    protected $height;
    protected $codeType;
    protected $code;
    protected $image;

    public function __construct($number=4,$codeType=2,$width=100,$height=50){
        $this->number = $number;
        $this->width = $width;
        $this->codeType = $codeType;
        $this->height = $height;
        $this->code = $this->createCode();
    }

    public function __destruct(){
        imagedestroy($this->image);
    }
    protected function createCode(){
        switch($this->codeType){
            case 0://纯数字
                $code = $this->getNumberCode();
                break;
            case 1: //纯字母
                $code = $this->getCharCode();
            break;
            case 2: //字母和数字
                $code = $this->getCharNumberCode();
                break;
            default:
                $code = $this->getCharNumberCode();
                break;
        }
        return $code;
    }

    public function __get($name){
        if($name == 'code'){
            return $this->code;
        }
        return false;
    }

    protected function getNumberCode(){
        $str = join('',range(0,9));
        return substr(str_shuffle($str),0,$this->number);
    }

    protected function getCharCode(){
        $str = join('',range('a','z'));
        $str = $str.strtoupper($str);
        return substr(str_shuffle($str),0,$this->number);
    }

    protected function getCharNumberCode(){
        $str = '';
        $str.= join('',range('a','z'));
        $str.=strtoupper($str);
        $str.= join('',range(2,9));
        //排除i,l,o,s 
        $str = str_replace(['i','5','s','o','l'],'',$str);
        return substr(str_shuffle($str),0,$this->number);
    }

    protected function createImage(){
        $this->image = imagecreatetruecolor($this->width,$this->height);
    }

    protected function fillBack(){
        imagefill($this->image,0,0,$this->lightColor());
    }

    protected function lightColor(){
        return imagecolorallocate($this->image,mt_rand(130,255),mt_rand(130,255),mt_rand(130,255));
    }
    protected function darkColor(){
        return imagecolorallocate($this->image,mt_rand(0,120),mt_rand(0,120),mt_rand(0,120));
    }

    protected function drawChar(){
        $width = ceil($this->width/$this->number);
        for($i=0;$i<$this->number;$i++){
            $x = mt_rand($i * $width + 5,($i + 1) * $width - 15);
            $y = mt_rand(0,$this->height-15);
            imagechar($this->image,5,$x,$y,$this->code[$i],$this->darkColor());
        }
    }

    protected function drawDisturb(){
        for($i=0;$i<200;$i++){
            $x = mt_rand(0,$this->width);
            $y = mt_rand(0,$this->height);
            imagesetpixel($this->image,$x,$y,$this->lightColor());
        }
    }

    protected function show(){
        header('Content-Type:image/png');
        imagepng($this->image);
    }

    public function outImage(){
        //创建画布
        $this->createImage();
        $this->fillBack();
        $this->drawChar();
        $this->drawDisturb();
        $this->show();
    }
}