<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Controller
{

    private static $instance;

    protected $base_title;
    protected $_movieType;

    protected $_moviePlace;

    protected $_bofangqiType;

    protected $_qingxiType;

    protected $_shoufeiType;

    protected $_downLoadType;

    protected $_movieSortType;

    const CONFIG_N_COOKIE_DOMAIN = "cookie_domain";
    const CONFIG_N_COOKIE_PATH = "cookie_path";

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$instance =& $this;
        $this->base_title = APF::get_instance()->get_config_value("base_title");
        $this->_shoufeiType = APF::get_instance()->get_config_value("shoufeiType");
        $this->_bofangqiType = APF::get_instance()->get_config_value("bofangqiType");
        $this->_downLoadType = APF::get_instance()->get_config_value("downLoadType");
        $this->_moviePlace = APF::get_instance()->get_config_value("moviePlace");
        $this->_qingxiType = APF::get_instance()->get_config_value("qingxiType");
        $this->_movieSortType = APF::get_instance()->get_config_value("movieSortType");
        $this->_movieType = APF::get_instance()->get_config_value("movieType");

        foreach (is_loaded() as $var => $class) {
            $this->$var =& load_class($class);
        }
        $this->decryptCookie();

        $this->load =& load_class('Loader', 'core');

        $this->load->initialize();

        if (!empty($this->userId)) {
            $this->_attr['userId'] = $this->userId;
            $this->load->model("Message");
            $userNoReadMessageCount = $this->Message->getMessageCountByFiled(array("userId"=>$this->userId,"del"=>0,"is_read" =>0));
            $this->_attr['userNoReadMessageCount'] = $userNoReadMessageCount;
            $this->load->set_login_pan(false);
        }
        if (!empty($this->userName)) {
            $this->_attr['userName'] = $this->userName;
        }
        log_message('debug', "Controller Class Initialized");
    }

    public static function &get_instance()
    {
        return self::$instance;
    }

    public function set_view($view, $main = "base")
    {
        $this->load->set_view($view);
        $this->load->view($main, $this->_attr);
    }

    public function set_background_view($view, $main = "background/backgroundmain")
    {
        $this->load->set_view($view);
        $this->load->view($main, $this->_attr);
    }

    private $_attr;

    public function set_attr($name, $value)
    {
        $this->_attr['data'][$name] = $value;
    }

    public function set_page_info($page, $size, $totalCount, $base_url, $bNum = 3)
    {
        $page = intval($page);
        $size = intval($size);
        $totalCount = intval($totalCount);
        if (empty($base_url) || $page <= 0 || $size <= 0 || $totalCount <= 0) {
            return false;
        }
        $result = array();
        $totalPage = ceil($totalCount / $size);
        $min = $page - $bNum;
        if ($bNum >= ($totalPage - $page)) {
            $min -= ($bNum -$totalPage + $page);
        }
        $min = ($min <= 0) ? 1 : $min;
        $max = $page + $bNum;
        if ($page <=  $bNum) {
            $max +=  ($bNum + 1 - $page);
        }
        $max = ($max > $totalPage) ? $totalPage : $max;
        if ($page == 1) {
            $result[] = array(
                "link" => "javascript:void(0)",
                "page" => "«",
                "able" => false,
                "current" => false,
            );
        } else {
            $result[] = array(
                "link" => $base_url . ($page - 1) . "/",
                "page" => "«",
                "able" => true,
                "current" => false,
            );
        }
        for ($i = $min; $i <= $max; $i++) {
            $result[] = array(
                "link" => $base_url . $i . "/",
                "page" => $i,
                "able" => true,
                "current" => ($i == $page) ? true : false,
            );
        }
        if ($page == $totalPage) {
            $result[] = array(
                "link" => "javascript:void(0)",
                "page" => "»",
                "able" => false,
                "current" => false,
            );
        } else {
            $result[] = array(
                "link" => $base_url . ($page + 1) . "/",
                "page" => "»",
                "able" => true,
                "current" => false,
            );
        }
        return $result;
    }

    public function jump_to($url, $location = true)
    {
        $url = APF::get_instance()->get_config_value("base_uri") . $url;
        $this->load->helper('url');
        redirect("{$url}", $location);
    }

    public function ubb2Html($content)
    {
        $this->load->model('Globalfunction');
        return $this->Globalfunction->ubb2html($content);
    }

    public function splitStr($str, $len, $replace = "...")
    {
        if (mb_strlen($str, "UTF-8") > $len) {
            $str = mb_substr($str, 0, $len, "UTF-8") . $replace;
        }
        return $str;
    }

    public function initArrById($arr = array(), $type = "infoId")
    {
        if (empty($arr)) {
            return $arr;
        }
        $result = array();
        foreach ($arr as $arrVal) {
            $result[$arrVal[$type]] = $arrVal;
        }
        return $result;
    }

    public function set_cookie($name, $value, $expire = 0, $path = NULL, $domain = NULL, $secure = FALSE, $httponly = FALSE)
    {
        if (!$path) {
            $path = APF::get_instance()->get_config_value(self::CONFIG_N_COOKIE_PATH);
        }
        if (!$domain) {
            $domain = APF::get_instance()->get_config_value(self::CONFIG_N_COOKIE_DOMAIN);
        }
        return setcookie($name, $value,
            $expire ? time() + intval($expire) : 0,
            $path, $domain,
            $secure, $httponly);
    }

    public function get_cookie($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
    }

    // host
    private static $host = '';
    // 基数，控制微缩网址的长度
    private static $base_val = 0; //自己定义的
    // 进制
    private static $base_hex = 62;
    // 字符列表
    private static $charlist = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * 把数据里的ID转换为62进制
     *
     * @static
     * @param int $id   mysql insert_id
     * @return string
     */
    public static function get_idx($id)
    {
        $base = self::$base_hex;
        $out = '';
        // check if we have a zero
        $t = ($id == 0) ? 0 : floor(log10($id) / log10($base)); //计算10进制数转换为62进制的位数
        for ($t; $t >= 0; $t--) {
            $a = floor($id / pow($base, $t));
            $out = $out . substr(self::$charlist, $a, 1);
            $id = $id - ($a * pow($base, $t));
        }
        return $out;
    }

    /**
     * 把str转换为ID
     *
     * @static
     * @param string $str
     * @return int
     */
    public static function get_id($str)
    {
        $base = self::$base_hex;
        $out = 0;
        $len = strlen($str) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $out = $out + strpos(self::$charlist, substr($str, $t, 1)) * pow($base, $len - $t);
        }
        return $out;
    }

    function getUserIP()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = "";
        }
        return $cip;
    }

    public function setLoginCookie($username, $userid, $time = 0, $remember = 0)
    {
        if (empty($username) || empty($userid)) {
            return false;
        }
        $cookiename = APF::get_instance()->get_config_value("AuthCookieName");
        $jiaSecques = APF::get_instance()->get_config_value("dianying8Secques");
        $enStr = $this->encrypt("$userid\t$username\t$jiaSecques\t$time", null);
        if ($time > 0 && $remember > 0) {
            $this->set_cookie($cookiename, $enStr, $time);
        } else {
            $this->set_cookie($cookiename, $enStr);
        }
    }

    public static function encrypt($string, $key)
    {
        return self::authorCode($string, "ENCODE", $key);
    }

    public static function decrypt($string, $key)
    {
        return self::authorCode($string, "DECODE", $key);
    }

    private static function authorCode($string, $operation, $key)
    {
        $key = md5($key ? $key : md5($_SERVER['HTTP_USER_AGENT']));
        $key_length = strlen($key);
        $string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);

        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }

    protected $userId;
    protected $userName;

    protected function decryptCookie()
    {
        $cookiename = APF::get_instance()->get_config_value("AuthCookieName");
        $cookie = $this->get_cookie($cookiename);
        if ($cookie) {
            $cookiestr = self::decrypt($cookie, md5($_SERVER['HTTP_USER_AGENT']));
            $cookieArr = explode("\t", $cookiestr);

            @list($userid, $username, $secques, $cookietime) = $cookieArr;
            $this->userId = $userid;
            $this->userName = $username;
        }
    }

    public function get_userId()
    {
        return $this->userId;
    }

    public function get_userName()
    {
        return $this->userName;
    }

    public function remove_cookie($name, $path=NULL, $domain=NULL, $secure=FALSE, $httponly=FALSE) {
        return $this->set_cookie($name, NULL, -3600, $path, $domain, $secure, $httponly);
    }

    protected function remove_login_cookie()
    {
        $cookie_name = APF::get_instance()->get_config_value('AuthCookieName');
        $cookie_path = APF::get_instance()->get_config_value('cookie_path');
        $cookie_domain = APF::get_instance()->get_config_value('cookie_domain');
        $this->remove_cookie($cookie_name,$cookie_path,$cookie_domain);
        return true;
    }

    public function set_content_type($content_type, $charset=NULL) {
        if (!$charset && preg_match('/^text/i', $content_type)) {
            $charset = APF::get_instance()->get_config_value('charset');
            if (!$charset) {
                $charset = 'utf-8';
            }
        }
        if ($charset) {
            APF::get_instance()->set_header("content-type", "$content_type; charset=$charset");
        } else {
            APF::get_instance()->set_header("content-type", $content_type);
        }
    }
}