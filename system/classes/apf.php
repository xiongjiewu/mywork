<?php
class APF
{
    private static $instance;

    public static function &get_instance()
    {
        if (!self::$instance) {
            self::$instance = new APF();
        }
        return self::$instance;
    }

    public function &get_config_value($name, $file = "common")
    {
        if (!isset($name)) {
            return null;
        }
        global $COF_FILE_PATH;
        $value = null;
        foreach($COF_FILE_PATH as $cof_val) {
            $file_path = $cof_val . '/' . $file . '.php';
            if (file_exists($file_path)) {
                require($file_path);
                if (isset($config[$name])) {
                    $value = $config[$name];
                    break;
                }
            }
        }
        return $value;
    }

    public function set_header($name, $value, $http_reponse_code = NULL)
    {
        header("$name: $value", TRUE, $http_reponse_code);
    }

    public function add_header($name, $value, $http_reponse_code = NULL)
    {
        header("$name: $value", FALSE, $http_reponse_code);
    }

    static private $key = "abcdefghijklmnopqrstuvwxyz0123456789"; //可以多位 保证每位的字符在URL里面正常显示即可
    static private $_md5Len = 10; //附加md5值长度
    /** 将id转换成字符串
     * @param $value
     * @return string
     */
    public function encodeId($value)
    {
        $base = strlen(self::$key);
        $arr = array();
        while ($value != 0) {
            $arr[] = $value % $base;
            $value = floor($value / $base);
        }
        $result = "";
        while (isset($arr[0])) $result .= substr(self::$key, array_pop($arr), 1);
        $md5Time = md5(microtime());
        $result = substr($md5Time, 0, self::$_md5Len) . $result . substr($md5Time, self::$_md5Len, self::$_md5Len);
        return $result;
    }

    /** 将字符串转换成id
     * @param $value
     * @return string
     */
    public function decodeId($value)
    {
        $valueLen = strlen($value);
        $value = substr($value, self::$_md5Len, $valueLen - self::$_md5Len * 2);
        $base = strlen(self::$key);
        $num = 0;
        $key = array_flip(str_split(self::$key));
        $arr = str_split($value);
        for ($len = count($arr) - 1, $i = 0; $i <= $len; $i++) {
            $num += pow($base, $i) * $key[$arr[$len - $i]];
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
    public function get_real_url($page, $id = '', $params = array(),$encodeId = false)
    {
        if (empty($page)) {
            return false;
        }

        $id = intval($id);
        $page = trim($page,"/");
        switch (strtolower($page)) {
            case "detail" :
                $pri_url = "/detail/index/" . $this->encodeId($id);
                break;
            case "play" :
                $pri_url = "/play/index/" . $this->encodeId($id);
                break;
            case "people" :
                $pri_url = "/people/index/" . $this->encodeId($id);
                break;
            default : //待新增
                $pri_url = "/" . $page . "/";
                if ($encodeId) {
                    $pri_url .= $this->encodeId($id);
                }
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
    public function get_image_url($image,$type = "",$size = "")
    {
        if (empty($image)) {
            $image = $this->get_config_value("user_photo");
        }
        if (strpos($image,"http") !== false) {
            return $image;
        }
        $imageArr = explode("!",$image);
        $image = $imageArr[0];
        if (!empty($type) && !empty($size)) {
            $imgInfo = APF::get_instance()->get_config_value($type,"imgcollocation");
            if (!empty($imgInfo["size"][$size])) {
                $image .= $imgInfo["size"][$size];
            }
        }
        return trim($this->get_config_value("img_base_url"), "/") . $image;
    }

    public function splitStr($str, $len, $replace = "...")
    {
        if (mb_strlen($str, "UTF-8") > $len) {
            $str = mb_substr($str, 0, $len, "UTF-8") . $replace;
        }
        return $str;
    }

    /** curl 获取信息
     * @param $url
     * @param bool $json
     * @return mixed
     */
    public function myCurl($url, $params = array(),$json = false,$get = true)
    {
        if (empty($params) || !is_array($params)) {
            return false;
        }
        if ($get) {//get请求
            $url .= "?" . http_build_query($params);
            $ch = curl_init (); //初始化curl
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //设置是否返回信息
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml")); //设置HTTP头
            curl_setopt ($ch, CURLOPT_URL, $url); //设置链接
            $response = curl_exec ($ch); //接收返回信息
            curl_close ($ch); //关闭curl链接
            return $json ? json_decode ($response, true) : $response;
        } else {
            $curlPost = http_build_query($params);
            $ch = curl_init();//初始化curl
            curl_setopt($ch,CURLOPT_URL,$url);//抓取指定网页
            curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            $response = curl_exec($ch);//运行curl
            curl_close($ch);
            return $json ? json_decode ($response, true) : $response;
        }
    }

}
