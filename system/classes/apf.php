<?php
class APF {
    private static $instance;
    public static function &get_instance() {
        if (!self::$instance) {
            self::$instance = new APF();
        }
        return self::$instance;
    }

    public function &get_config_value($name,$file = "common")
    {
        if (!isset($name)) {
            return null;
        }
        $value = null;
        if (defined('GACONFIGPATH')) {
            $file_path = GACONFIGPATH . "/" . $file . '.php';
            if (file_exists($file_path)) {
                require($file_path);
                $value = isset($config[$name]) ? $config[$name] : null;
            }
        }
        if ($value == null) {
            $file_path = APPPATH.'config/' . $file . '.php';
            if ( file_exists($file_path)) {
                require($file_path);
                $value = isset($config[$name]) ? $config[$name] : null;
            }
        }
        return $value;
    }

    public function set_header($name, $value, $http_reponse_code=NULL) {
        header("$name: $value", TRUE, $http_reponse_code);
    }

    public function add_header($name, $value, $http_reponse_code=NULL) {
        header("$name: $value", FALSE, $http_reponse_code);
    }

    static private $key = "abcdefghijklmnopqrstuvwxyz0123456789"; //可以多位 保证每位的字符在URL里面正常显示即可
    static private $_md5Len  = 10;//附加md5值长度
    /** 将id转换成字符串
     * @param $value
     * @return string
     */
    public function encodeId($value) {
        $base = strlen(self::$key );
        $arr = array();
        while( $value != 0 ) {
            $arr[] = $value % $base;
            $value = floor( $value / $base );
        }
        $result = "";
        while( isset($arr[0]) ) $result .= substr(self::$key, array_pop($arr), 1 );
        $md5Time = md5(microtime());
        $result = substr($md5Time,0,self::$_md5Len) . $result . substr($md5Time,self::$_md5Len,self::$_md5Len);
        return $result;
    }
    /** 将字符串转换成id
     * @param $value
     * @return string
     */
    public function decodeId($value) {
        $valueLen = strlen($value);
        $value = substr($value,self::$_md5Len,$valueLen - self::$_md5Len * 2);
        $base = strlen(self::$key);
        $num = 0;
        $key = array_flip( str_split(self::$key) );
        $arr = str_split($value);
        for($len = count($arr) - 1, $i = 0; $i <= $len; $i++) {
            $num += pow($base, $i) * $key[$arr[$len-$i]];
        }
        return $num;
    }

    /**
     * 拼接url统一函数
     * @param $page
     * @param $id
     * @param array $params
     * @return bool|string
     */
    public function get_real_url($page,$id,$params = array()) {
        $id = intval($id);
        if (empty($page) || empty($id)) {
            return false;
        }

        switch(strtolower($page)) {
            case "detail" :
                $pri_url = "/detail/index/" . $this->encodeId($id);
                break;
            case "play" :
                $pri_url = "/play/index/" . $this->encodeId($id);
                break;
            default ://待新增
                $pri_url = "/";
                break;
        }
        if (!empty($params) && is_array($params)) {
            $pri_url .= "?" . http_build_query($params);
        }
        return $pri_url;
    }

    /**
     * 拼接图片地址
     * @param $image
     * @return string
     */
    public function get_image_url($image) {
        return trim($this->get_config_value("img_base_url"), "/") . $image;
    }

}
