<?php
class Admin extends CI_Model {

    private $_filedArr = array("id","userId","time","type");

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _getFiledStr()
    {
        return implode(",",$this->_filedArr);
    }

}