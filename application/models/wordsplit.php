<?php
/**
 * 分词类，added by xiongjiewu 2013-05-19
 */
define("SPLIT_APP",dirname(__FILE__) . "/../split");
class Wordsplit extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }
    public function get_tags_arr($title)
    {
        require(SPLIT_APP.'/pscws4.class.php');
        $pscws = new PSCWS4();
        $pscws->set_dict(SPLIT_APP.'/scws/dict.utf8.xdb');
        $pscws->set_rule(SPLIT_APP.'/scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops(5);
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        $pscws->close();
        return $tags;
    }

    public function get_keywords_str($content){
        require(SPLIT_APP.'/phpanalysis.class.php');
        PhpAnalysis::$loadInit = false;
        $pa = new PhpAnalysis('utf-8', 'utf-8', false);
        $pa->LoadDict();
        $pa->SetSource($content);
        $pa->StartAnalysis( false );
        $tags = $pa->GetFinallyResult();
        return $tags;
    }
}