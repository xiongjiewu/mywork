<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 生成验证码
 * added by xiongjiewu at 2013-3-10
 */
class Codeimg extends CI_Controller {

    private $height; //@定义验证码图片高度
    private $width; //@定义验证码图片宽度
    private $textNum; //@定义验证码字符个数
    private $textContent; //@定义验证码字符内容
    private $fontColor; //@定义字符颜色
    private $randFontColor; //@定义随机出的文字颜色
    private $fontSize; //@定义字符大小
    private $fontFamily; //@定义字体
    private $bgColor; //@定义背景颜色
    private $randBgColor; //@定义随机出的背景颜色
    private $textLang; //@定义字符语言
    private $noisePoint; //@定义干扰点数量
    private $noiseLine; //@定义干扰线数量
    private $distortion; //@定义是否扭曲
    private $distortionImage; //@定义扭曲图片源
    private $showBorder; //@定义是否有边框
    private $image; //@定义验证码图片源
    const REGISTER_CODE_IMG_WIDTH = 80;//注册验证码图片宽度
    const REGISTER_CODE_IMG_HEIGHT = 38;//注册验证码图片高度
    const REGISTER_CODE_IMG_NUM = 4;//注册验证码图片数字个数
    const REGISTER_CODE_IMG_FONT_COLOR = "#444444";//注册验证码图片字体颜色
    const REGISTER_CODE_IMG_FONT_SIZE = 15;//注册验证码图片字体大小
    const REGISTER_CODE_IMG_FONT_ROOT = "classes";//注册验证码字体库所在文件夹
    const REGISTER_CODE_IMG_FONT_PATH = "./images/Tahoma.ttf";//注册验证码字体库文件路径
    const REGISTER_CODE_IMG_FONT_STYLE = "en";//注册验证码字体类型（中文：cn，英文：en）
    const REGISTER_CODE_IMG_BACKGROUND_COLOR = "#ffffff";//注册验证码图片背景颜色
    const REGISTER_CODE_IMG_CODE_NUM = 300;//注册验证码图片干扰点个数
    const REGISTER_CODE_IMG_LINE_NUM = 5;//注册验证码图片干扰线个数
    const REGISTER_CODE_FONT_W = false;//注册验证码字符是否扭曲
    const REGISTER_CODE_IMG_BORDER = false;//注册验证码图片是否生成边框

    function index(){
        $this->getCodeImg();
    }
    function random($len){
        $srCStr="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $strs="";
        for($i=0;$i<$len;$i++)
        {
            $strs.=$srCStr[mt_rand(0,35)];
        }
        return $strs;
    }
    function get_yanzhengma_img(){
        $str=$this->random(4); //随机生成的字符串
        $width = 50; //验证码图片的宽度
        $height = 25; //验证码图片的高度
        $this->set_content_type("image/PNG");
        $_SESSION["code"] = $str;
        $im = imagecreate($width,$height);
        //背景色
        $back = imagecolorallocate($im,0xFF,0xFF,0xFF);
        //模糊点颜色
        $pix = imagecolorallocate($im,100,163,238);
        //字体色
        $font = imagecolorallocate($im,80,163,238);
        //绘模糊作用的点
        for($i=0;$i<250;$i++){
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pix);
        }
        imagestring($im, 5, 7, 5,$str, $font);
        imagerectangle($im,0,0,$width-1,$height-1,$font);
        imagepng($im);
        imagedestroy($im);
        $_SESSION["code"] = $str;
    }

    function get_code_img(){
        ob_start();
        //session_start();
        //session_register("login_check_number");
        //如果浏览器显示“图像XXX因其本身有错无法显示”，可尽量去掉文中空格
        //先成生背景，再把生成的验证码放上去
        $img_height=70;//先定义图片的长、宽
        $img_width=50;
        $authnum='';
        //生产验证码字符
        $ychar="0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
        $list=explode(",",$ychar);
        for($i=0;$i<4;$i++){
            $randnum=rand(0,35);
            $authnum.=$list[$randnum];
        }
        //把验证码字符保存到session
        $_SESSION["login_check_number"] = $authnum;

        $aimg = imagecreate($img_height,$img_width);    //生成图片
        imagecolorallocate($aimg, 255,255,255);            //图片底色，ImageColorAllocate第1次定义颜色PHP就认为是底色了
        $black = imagecolorallocate($aimg, 0,0,0);        //定义需要的黑色

        for ($i=1; $i<=100; $i++) {
            imagestring($aimg,1,mt_rand(1,$img_height),mt_rand(1,$img_width),"@",imagecolorallocate($aimg,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255)));
        }

        //为了区别于背景，这里的颜色不超过200，上面的不小于200
        for ($i=0;$i<strlen($authnum);$i++){
            imagestring($aimg, mt_rand(3,5),$i*$img_height/4+mt_rand(2,7),mt_rand(1,$img_width/2-2), $authnum[$i],imagecolorallocate($aimg,mt_rand(0,50),mt_rand(0,100),mt_rand(0,150)));
        }
        imagerectangle($aimg,0,0,$img_height-1,$img_width-1,$black);//画一个矩形
        $this->set_content_type("image/PNG");
        ImagePNG($aimg);//生成png格式
        ImageDestroy($aimg);
    }

    function getCodeImg(){
        ob_start();
        /** 用于调整显示样式,注释则使用默认样式
         *    set_show_mode($w,  $h,  $num,  $fc,  $fz,  $ff_url,  $lang,  $bc,  $m,  $n,  $b,  $border);
         *    $w验证码宽度;    $验证码高度;    $num验证码位数;    $fc字符颜色;    $fz字符大小;
         *    $ff_url字体存放路径;    $lang定义字符语言'en'或'cn';    $bc背景颜色;    $m干扰点个数;    $n干扰线条数;
         * $b是否扭曲字符,TRUE或FALSE;    $border是否有边框,TRUE或FALSE;
         */
        $width = self::REGISTER_CODE_IMG_WIDTH;
        $height = self::REGISTER_CODE_IMG_HEIGHT;
        $font_num = self::REGISTER_CODE_IMG_NUM;
        $font_color = self::REGISTER_CODE_IMG_FONT_COLOR;
        $font_size = self::REGISTER_CODE_IMG_FONT_SIZE;
        $path = self::REGISTER_CODE_IMG_FONT_PATH;//获取字体文件绝对路径
        $font_style = self::REGISTER_CODE_IMG_FONT_STYLE;
        $b_color = self::REGISTER_CODE_IMG_BACKGROUND_COLOR;
        $code_num = self::REGISTER_CODE_IMG_CODE_NUM;
        $line_num = self::REGISTER_CODE_IMG_LINE_NUM;
        $f_s = self::REGISTER_CODE_FONT_W;
        $border = self::REGISTER_CODE_IMG_BORDER;
        $this->set_show_mode($width,  $height,  $font_num,  $font_color,  $font_size,  $path,  $font_style,$b_color,$code_num,$line_num,$f_s,$border);
        $code = $this->createImage();
        $this->set_cookie(APF::get_instance()->get_config_value('resgiter_code_cookie_name'),self::get_id($code));//获取验证码的值并转加密存到scookie中;
        $this->set_content_type("image/PNG");
    }

    public function set_header($name, $value, $http_reponse_code=NULL) {
        header("$name: $value", TRUE, $http_reponse_code);
    }

    public function Imagecaptcha(){ //@Constructor 构造函数
        //设置一些默认值
        $this->textNum = 4;
        $this->fontSize = 15;
        $path = self::REGISTER_CODE_IMG_FONT_PATH;//获取字体文件绝对路径
        $this->fontFamily = $path;//设置字体，可以改成linux的目录
        $this->textLang = 'en';
        $this->noisePoint = 100;
        $this->noiseLine = 0;
        $this->distortion = false;
        $this->showBorder = false;
    }

    public function set_show_mode($w,$h,$num,$fc,$fz,$ff_url,$lang,$bc,$m,$n,$b,$border){
        $this->width=$w; //@设置图片宽度
        $this->height=$h; //@设置图片高度
        $this->textNum=$num; //@设置字符个数
        $this->fontColor=sscanf($fc,'#%2x%2x%2x'); //@设置字符颜色
        $this->fontSize=$fz; //@设置字号
        $this->fontFamily=$ff_url; //@设置字体url
        $this->textLang=$lang; //@设置字符语言
        $this->bgColor=sscanf($bc,'#%2x%2x%2x'); //@设置图片背景
        $this->noisePoint=$m; //@设置干扰点数量
        $this->noiseLine=$n; //@设置干扰线数量
        $this->distortion=$b; //@设置是否扭曲字符
        $this->showBorder=$border; //@设置是否显示边框
    }

    public function initImage(){    //@初始化验证码图片
        if(empty($this->width)){$this->width=floor($this->fontSize*1.3)*$this->textNum+10;}
        if(empty($this->height)){$this->height=floor($this->fontSize*2.5);}
        $this->image=imagecreatetruecolor($this->width,$this->height);
        if(empty($this->bgColor)){
            $this->randBgColor=imagecolorallocate($this->image,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255));
        }else{
            $this->randBgColor=imagecolorallocate($this->image,$this->bgColor[0],$this->bgColor[1],$this->bgColor[2]);
        }
        imagefill($this->image,0,0,$this->randBgColor);
    }

    public function randText($type){    //@产生随机字符
        $string='';
        switch($type){
            case 'en':
                $str='ABCDEFGHJKLMNOPQRSTUVWXYabcdehkmnprsuvwxy3456789';//要随机的字符内容
                for($i=0;$i<$this->textNum;$i++){
                    $string=$string.','.$str[mt_rand(0,strlen($str)-1)];
                }
                break;
            case 'cn':
                for($i=0;$i<$this->textNum;$i++) {
                    $string=$string.','.chr(mt_rand(0xB0,0xCC)).chr(mt_rand(0xA1,0xBB));
                }
                $string=iconv('GB2312','UTF-8',$string); //转换编码到utf8
                break;
        }
        return substr($string,1);
        //return $this->textNum;
    }

    public function createText(){    //@输出文字到验证码
        $text_array=explode(',',$this->randText($this->textLang));
        $this->textContent=join('',$text_array);
        if(empty($this->fontColor)){
            $this->randFontColor=imagecolorallocate($this->image,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        }else{
            $this->randFontColor=imagecolorallocate($this->image,$this->fontColor[0],$this->fontColor[1],$this->fontColor[2]);
        }
        for($i=0;$i<$this->textNum;$i++){
            $angle=mt_rand(-1,1)*mt_rand(1,20);
            imagettftext($this->image,$this->fontSize,$angle,5+$i*floor($this->fontSize*1.3),floor($this->height*0.75),$this->randFontColor,$this->fontFamily,$text_array[$i]);
        }
    }

    public function createNoisePoint(){    //@生成干扰点
        for($i=0;$i<$this->noisePoint;$i++){
            $pointColor=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),$pointColor);
        }
    }

    public function createNoiseLine(){    //@产生干扰线
        for($i=0;$i<$this->noiseLine;$i++) {
            $lineColor=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),20);
            imageline($this->image,0,mt_rand(0,$this->width),$this->width,mt_rand(0,$this->height),$lineColor);
        }
    }

    public function distortionText(){    //@扭曲文字
        $this->distortionImage=imagecreatetruecolor($this->width,$this->height);
        imagefill($this->distortionImage,0,0,$this->randBgColor);
        for($x=0;$x<$this->width;$x++){
            for($y=0;$y<$this->height;$y++){
                $rgbColor=imagecolorat($this->image,$x,$y);
                imagesetpixel($this->distortionImage,(int)($x+sin($y/$this->height*2*M_PI-M_PI*0.5)*3),$y,$rgbColor);
            }
        }
        $this->image=$this->distortionImage;
    }

    public function createImage(){    //@生成验证码图片
        $this->initImage(); //创建基本图片
        $this->createText(); //输出验证码字符
        $this->createNoisePoint(); //产生干扰点
        $this->createNoiseLine(); //产生干扰线
        if($this->distortion !=false){$this->distortionText();}//扭曲文字
        if($this->showBorder){imagerectangle($this->image,0,0,$this->width-1,$this->height-1,$this->randFontColor);} //添加边框
        imagepng($this->image);
        imagedestroy($this->image);
        if($this->distortion !=false){imagedestroy($this->distortionImage);}
        return $this->textContent;
    }

}
