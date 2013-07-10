<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站跳转中间页面页面
 * added by xiongjiewu at 2013-07-04
 */
class Jump extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Character');
        $this->load->model('Backgroundadmin');
        $this->load->set_top_index(-1);
    }

    //类型对应信息
    private $_typeArr = array(
        1 => "people",
        2 => "movie",
    );

    /**
     * 处理主函数，如果没有找到对应信息，默认跳转致搜索页面
     */
    public function index()
    {
        $key = $this->input->get("key");
        $key = trim($key);
        $type = $this->input->get("type");
        $type = intval($type);
        if (empty($key) || empty($type) || empty($this->_typeArr[$type])) {
            $this->jump_to("/");
            exit;
        }
        $key = mysql_real_escape_string($key);
        if ($type == 1) {//人物
            $condiStr = "name ='{$key}' and del = 0 limit 1;";
            $characterInfo = $this->Character->getCharacterInfoByCon($condiStr);
            if (empty($characterInfo[0])) {//没有信息，跳转致搜索页面
                $this->jump_to(APF::get_instance()->get_real_url("/search","",array("key" => $key)));
                exit;
            } else {
                $enCodeId = APF::get_instance()->encodeId($characterInfo[0]['id']);
                $this->jump_to(APF::get_instance()->get_real_url("/people/index/{$enCodeId}"));
                exit;
            }
        } elseif ($type == 2) {//电影
            $condiStr = "name ='{$key}' and del = 0 limit 1;";
            $dyInfo = $this->Backgroundadmin->getMovieInfoByCon($condiStr);
            if (empty($dyInfo[0])) {//没有信息，跳转致搜索页面
                $this->jump_to(APF::get_instance()->get_real_url("/search","",array("key" => $key)));
                exit;
            } else {
                $enCodeId = APF::get_instance()->encodeId($dyInfo[0]['id']);
                $this->jump_to(APF::get_instance()->get_real_url("/detail/index/{$enCodeId}"));
                exit;
            }
        }
        $this->jump_to("/");
        exit;
    }
}