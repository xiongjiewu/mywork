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

    static private $key = "ABCDEFGHIJKLMNOPQRSTUVWXYZ-abcdefghijklmnopqrstuvwxyz:0123456789"; //可以多位 保证每位的字符在URL里面正常显示即可
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
        return $result;
    }
    /** 将字符串转换成id
     * @param $value
     * @return string
     */
    public function decodeId($value) {
        $base = strlen( self::$key );
        $num = 0;
        $key = array_flip( str_split(self::$key) );
        $arr = str_split($value);
        for($len = count($arr) - 1, $i = 0; $i <= $len; $i++) {
            $num += pow($base, $i) * $key[$arr[$len-$i]];
        }
        return $num;
    }

}
