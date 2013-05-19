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

}
