<?php
/**
 * job基类
 * added by xiongjiewu at 2013-06-07
 * Class jobBase
 */
class jobBase extends myPdo
{
    protected $_pdo;

    public function __construct()
    {
        $this->_pdo = $this->getPdo ();
    }

    /**
     * 获取当前处理的电影信息
     * @return mixed
     */
    protected function _getMoviceInfo($id, $limit = 1, $row = "one", $del = 0)
    {
        $sql = "select * from `tbl_detailInfo` where del = {$del} and id > " . $id . " order by id asc limit " . $limit;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        if ($row == "one") {
            return $stmt->fetch ();
        } else {
            return $stmt->fetchAll ();
        }
    }

    /**
     * 获取当前处理的人物信息
     * @return mixed
     */
    protected function _getCharacterInfo($id, $limit = 1, $row = "one", $del = 0)
    {
        $sql = "select * from `tbl_character` where del = {$del} and id > " . $id . " order by id asc limit " . $limit;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        if ($row == "one") {
            return $stmt->fetch ();
        } else {
            return $stmt->fetchAll ();
        }
    }

    /** 更新电影信息
     * @param array $dataArr 新数据数组
     * @return bool|int
     */
    protected function _updateDetailInfo($id, $dataArr = array())
    {
        $id = intval ($id);
        if (empty($id) || empty($dataArr)) {
            return false;
        }
        $dataArr = array_filter ($dataArr);
        $setArr = $valArr = array();
        foreach ($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $setStr = implode (",", $setArr);
        $sql = "update `tbl_detailInfo` set {$setStr} where id = {$id} limit 1;";
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->execute ($valArr);
        return $stmt->rowCount ();
    }

    /**
     * 获取演员组成的一维数组信息
     * @param $acTorInfo
     * @return array
     */
    protected function _getActorArr($acTorInfo)
    {
        if (empty($acTorInfo)) {
            return array();
        }
        $resArr = array();
        foreach ($acTorInfo as $infoVal) {
            $resArr[$infoVal['id']] = $infoVal['name'];
        }
        return $resArr;
    }

    /**
     * 获取电影演员信息
     * @param $infoId
     * @return array|bool
     */
    protected function _getMovieActorInfo($infoId, $tableName = "tbl_actInfo")
    {
        $infoId = intval ($infoId);
        if (empty($infoId)) {
            return false;
        }
        $sql = "select * from `" . $tableName . "` where del = 0 and infoId = " . $infoId;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * 获取电影导演信息
     * @param $infoId
     * @return array|bool
     */
    protected function _getDirectorInfo($infoId, $tableName = "tbl_directorInfo")
    {
        return $this->_getMovieActorInfo ($infoId, $tableName);
    }

    /**
     * 删除演员或者导演
     * @param $id
     * @param $newName
     * @return bool|int
     */
    protected function _updateUserInfoById($id, $tableName = "tbl_actInfo")
    {
        $id = intval ($id);
        if (empty($id)) {
            return false;
        }
        $sql = "update `" . $tableName . "` set `del` = 1 where infoId = " . $id;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->execute ();
        return $stmt->rowCount ();
    }

    /**
     * 更新演员或者导演信息
     * @param $id
     * @param $newName
     * @return bool|int
     */
    protected function _updateUserInfoByinfoIdAndName($id, $name, $tableName = "tbl_actInfo")
    {
        $id = intval ($id);
        $name = trim ($name);
        if (empty($id) || empty($name)) {
            return false;
        }
        $sql = "update `" . $tableName . "` set `del` = 0 where infoId = " . $id . " and name = '" . $name . "' limit 1;";
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->execute ();
        return $stmt->rowCount ();
    }

    /**
     * 删除演员或者导演
     * @param $id
     * @param $newName
     * @return bool|int
     */
    protected function _delUserInfoById($id, $tableName = "tbl_actInfo")
    {
        $id = intval ($id);
        if (empty($id)) {
            return false;
        }
        $sql = "update `" . $tableName . "` set `del` = 1 where id = " . $id;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->execute ();
        return $stmt->rowCount ();
    }

    /**
     * 根据电影和id获取其他名称一致的电影信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    protected function _getMovieInfoByName($name, $id)
    {
        $name = trim ($name);
        $id = intval ($id);
        if (empty($name) || empty($id)) {
            return false;
        }
        $sql = "select * from `tbl_detailInfo` where del = 0 and id > " . $id . " and name = '" . $name . "';";
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetchAll ();
    }

    /**
     * 对主演或者导演信息进行并插入
     * @param array $dataArr
     * @param string $tableName
     * @return bool|string
     */
    protected function _insertActOrDirectorInfo($dataArr = array(), $tableName = "tbl_actInfo")
    {
        if (empty($dataArr) || empty($dataArr['infoId']) || empty($dataArr['name'])) {
            return false;
        }
        $info = $this->_getUserInfoByNameAndInfoId ($dataArr['name'], $dataArr['infoId'], $tableName);
        if (!empty($info)) { //存在则更新
            return $this->_updateUserInfoByinfoIdAndName ($dataArr['infoId'], $dataArr['name'], $tableName);
        }
        $dataArr['createTime'] = time (); //创建时间
        $keyArr = array_keys ($dataArr);
        $keyStr = implode (",", $keyArr);
        $valueArr = array_fill (0, count ($dataArr), '?');
        $valueStr = implode (",", $valueArr);
        $sql = "insert into `" . $tableName . "` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare ($sql);
        $dataArr = explode ("[AAAAAAA]", implode ("[AAAAAAA]", $dataArr));
        try {
            $stmt->execute ($dataArr);
            return $this->_pdo->lastInsertId ();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 根据演员或者导演名称和电影id获取索引表信息
     * @param $name
     * @param $id
     * @return array|bool
     */
    protected function _getUserInfoByNameAndInfoId($name, $infoId, $tableName = "tbl_actInfo")
    {
        $name = trim ($name);
        $infoId = intval ($infoId);
        if (empty($name) || empty($infoId)) {
            return false;
        }
        $sql = "select * from `" . $tableName . "` where infoId = " . $infoId . " and name = '" . $name . "' limit 1;";
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ();
        return $stmt->fetch ();
    }

    /**
     * 比较演员或者导演相同个数
     * @param $actor1
     * @param $actor2
     * @return bool
     */
    protected function _judgeActorInfo($actor1, $actor2)
    {
        $xiangtongCount = 0;
        foreach ($actor1 as $acVal) {
            if (in_array ($acVal, $actor2)) {
                $xiangtongCount++;
            } else {
                //过滤演员空格
                $acValNew = str_replace(" ","·",$acVal);
                if (in_array ($acValNew, $actor2)) {
                    $xiangtongCount++;
                } else {
                    //过滤演员·
                    $acValNew2 = str_replace("·"," ",$acVal);
                    if (in_array ($acValNew2, $actor2)) {
                        $xiangtongCount++;
                    }
                }
            }
        }
        return $xiangtongCount;
    }

    /** 插入表信息共用函数
     * @param array $dataArr 数据数组
     * @return bool|int
     */
    protected function _insertInfo($dataArr = array(), $tableName = "tbl_movieScore")
    {
        if (empty($dataArr)) {
            return false;
        }
        $keyArr = array_keys ($dataArr);
        $keyStr = implode (",", $keyArr);
        $valueArr = array_fill (0, count ($dataArr), '?');
        $valueStr = implode (",", $valueArr);
        $sql = "insert into `" . $tableName . "` ({$keyStr}) values ({$valueStr});";
        $stmt = $this->_pdo->prepare ($sql);
        $dataArr = array_values($dataArr);
        $stmt->execute($dataArr);
        return $this->_pdo->lastInsertId();
    }

    /** 获取表信息万能函数
     * @param array $dataArr 条件数据数组
     * @return bool|int
     */
    protected function _getInfo($dataArr = array(), $row = "one", $tableName = "tbl_movieScore")
    {
        if (empty($dataArr) || !is_array ($dataArr)) {
            return array();
        }
        $keyArr = $valArr = array();
        foreach ($dataArr as $dataKey => $dataVal) {
            $keyArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        $keyStr = implode (" and ", $keyArr);
        $sql = "select * from `" . $tableName . "` where " . $keyStr;
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $stmt->execute ($valArr);
        if ($row == "one") {
            return $stmt->fetch();
        } else {
            return $stmt->fetchAll();
        }
    }

    /** curl 获取信息
     * @param $url
     * @param bool $json
     * @return mixed
     */
    protected function _getCurlInfo($url, $json = false,$referer = "http://www.sogou.com",$ip = array())
    {
        //设置ip
        if (empty($ip)) {
            $idInfo = $this->get_config_value("ip_info","ipInfo");
            shuffle($idInfo);
            $headers = $idInfo[0];
        } else {
            $headers = $ip;
        }

        $headerArr = array();
        foreach( $headers as $n => $v ) {
            $headerArr[] = $n .':' . $v;
        }
        $headerArr[] = "Content-type: text/xml";
        $ch = curl_init (); //初始化curl
        $user_agent = "Mozilla/5.0";
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //设置是否返回信息
        curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt ($ch, CURLOPT_URL, $url); //设置链接
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr );  //构造IP+//设置HTTP头
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $response = curl_exec ($ch); //接收返回信息
        curl_close ($ch); //关闭curl链接
        return $json ? json_decode ($response, true) : $response;
    }

    /** 匹配抓取
     * @param $match 正则
     * @param $subject 目标字符串
     * @param $pather 模式
     * @return mixed
     */
    protected function _getPregMatchAll($match, $subject, $pather = null)
    {
        preg_match_all ($match, $subject, $resInfo, $pather);
        return $resInfo;
    }

    /** 匹配抓取
     * @param $match 正则
     * @param $subject 目标字符串
     * @return mixed
     */
    protected function _getPregMatch($match, $subject)
    {
        preg_match ($match, $subject, $resInfo);
        return $resInfo;
    }

    /** 更新表信息万能函数
     * @param array $condition 条件数组
     * @param array $dataArr 新数据数组
     * @param array $tableName 表名
     * @return bool|int
     */
    protected function _updateInfo($condition = array(), $dataArr = array(), $tableName = "tbl_detailInfo")
    {
        if (empty($condition) || empty($dataArr)) {
            return false;
        }
        $whereArr = $setArr = $valArr = array();
        foreach ($dataArr as $dataKey => $dataVal) {
            $setArr[] = "{$dataKey} = ?";
            $valArr[] = $dataVal;
        }
        foreach ($condition as $cKey => $cVal) {
            $whereArr[] = "{$cKey} = ?";
            $valArr[] = $cVal;
        }
        $whereStr = implode (" and ", $whereArr);
        $setStr = implode (",", $setArr);
        $sql = "update `" . $tableName . "` set {$setStr} where " . $whereStr . ";";
        $stmt = $this->_pdo->prepare ($sql);
        $stmt->execute ($valArr);
        return $stmt->rowCount ();
    }

    private $_limit = array( //gb2312 拼音排序
        array(45217, 45252), //A
        array(45253, 45760), //B
        array(45761, 46317), //C
        array(46318, 46825), //D
        array(46826, 47009), //E
        array(47010, 47296), //F
        array(47297, 47613), //G
        array(47614, 48118), //H
        array(0, 0), //I
        array(48119, 49061), //J
        array(49062, 49323), //K
        array(49324, 49895), //L
        array(49896, 50370), //M
        array(50371, 50613), //N
        array(50614, 50621), //O
        array(50622, 50905), //P
        array(50906, 51386), //Q
        array(51387, 51445), //R
        array(51446, 52217), //S
        array(52218, 52697), //T
        array(0, 0), //U
        array(0, 0), //V
        array(52698, 52979), //W
        array(52980, 53688), //X
        array(53689, 54480), //Y
        array(54481, 55289), //Z
    );

    /**
     * 获取字符传首字母
     * @param $str
     * @return string
     */

    function getFirstLetter($str)
    {
        $str = trim ($str);
        $str = iconv ("UTF-8", "GBK", $str);
        $i = 0;
        $tmp = bin2hex (substr ($str, $i, 1));
        if ($tmp >= 'B0') { //汉字的开始
            $t = $this->getLetter (hexdec (bin2hex (substr ($str, $i, 2))));
            return sprintf ("%c", $t == -1 ? '*' : $t);
        } else {
            return substr ($str, $i, 1);
        }
    }

    /**
     * 获取ASCII值
     * @param $num
     * @return int|string
     */
    function getLetter($num)
    {
        $limit = $this->_limit;
        $char_index = 65;
        foreach ($limit as $k => $v) {
            if ($num >= $v[0] && $num <= $v[1]) {
                $char_index += $k;
                return $char_index;
            }
        }
        return -1;
    }

    /**
     * 将汉字转换成拼音
     * @param $_String
     * @param string $_Code
     * @return mixed
     */
    function getPinyin($_String, $_Code = 'gb2312')
    {
        $_String = trim ($_String);
        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
            "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
            "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
            "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
            "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
            "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
            "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
            "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
            "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
            "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
            "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
            "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
            "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
            "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
            "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
            "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
            "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
            "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
            "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
            "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
            "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
            "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
            "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
            "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
            "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
            "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
            "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
            "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
            "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
            "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
            "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
            "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
            "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
            "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
            "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
            "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
            "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
            "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
            "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
            "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
            "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
            "|-10270|-10262|-10260|-10256|-10254";
        $_TDataKey = explode ('|', $_DataKey);
        $_TDataValue = explode ('|', $_DataValue);
        $_Data = array_combine ($_TDataKey, $_TDataValue);
        arsort ($_Data);
        reset ($_Data);
        if ($_Code != 'gb2312') $_String = $this->_U2_Utf8_Gb ($_String);
        $_Res = '';
        for ($i = 0; $i < strlen ($_String); $i++) {
            $_P = ord (substr ($_String, $i, 1));
            if ($_P > 160) {
                $_Q = ord (substr ($_String, ++$i, 1));
                $_P = $_P * 256 + $_Q - 65536;
            }
            $_Res .= $this->_Pinyin ($_P, $_Data);
        }
        return preg_replace ("/[^a-z0-9]*/", '', $_Res); //???????

    }

    function _Pinyin($_Num, $_Data)
    {
        if ($_Num > 0 && $_Num < 160) {
            return chr ($_Num);
        } elseif ($_Num < -20319 || $_Num > -10247) {
            return '';
        } else {
            foreach ($_Data as $k => $v) {
                if ($v <= $_Num) break;
            }
            return $k;
        }
    }

    function _U2_Utf8_Gb($_C)
    {
        $_String = '';
        if ($_C < 0x80) {
            $_String .= $_C;
        } elseif ($_C < 0x800) {
            $_String .= chr (0xC0 | $_C >> 6);
            $_String .= chr (0x80 | $_C & 0x3F);
        } elseif ($_C < 0x10000) {
            $_String .= chr (0xE0 | $_C >> 12);
            $_String .= chr (0x80 | $_C >> 6 & 0x3F);
            $_String .= chr (0x80 | $_C & 0x3F);
        } elseif ($_C < 0x200000) {
            $_String .= chr (0xF0 | $_C >> 18);
            $_String .= chr (0x80 | $_C >> 12 & 0x3F);
            $_String .= chr (0x80 | $_C >> 6 & 0x3F);
            $_String .= chr (0x80 | $_C & 0x3F);
        }
        return iconv ('UTF-8', 'gbk', $_String);
    }

    /**
     * 下载图片
     * @param $imgName 图片名称
     * @param $downLoadUrl 下载地址
     */
    protected function _downLoadImg($imgName, $downLoadUrl)
    {
        if (empty($imgName) || empty($downLoadUrl)) {
            return false;
        }
        if (strpos($imgName,"/images/") === false) {
            $imgPath = "/images/dy/" . $imgName;
        } else {
            $imgPath = $imgName;
        }
        $imgUpInfo = $this->get_config_value("dy","imgcollocation");
        try {
            //开始上传
            $upClass = new UpYun($imgUpInfo['bucket'],$imgUpInfo['user'],$imgUpInfo['password']);
            $upClass->putImg($downLoadUrl,$imgPath,true);
            return $imgPath;
        } catch(Exception $e) {
            return $this->get_config_value("/images/dy_common.jpg");
        }
    }

    /**
     * 拼接地区对应的类型码
     * @return array
     */
    protected function _getDiQuType($diQu)
    {
        $diQu = trim($diQu);
        $diQuInfo = $this->get_config_value("moviePlace");
        $diQuRes = array();
        foreach ($diQuInfo as $diQuKey => $diQuVal) {
            $diQuRes[$diQuVal] = $diQuKey;
        }
        $diQuRes["内地"] = 1;
        $diQuRes["中国内地"] = 1;
        $diQuRes["香港"] = 1;
        $diQuRes["台湾"] = 1;
        $diQuRes["港台"] = 1;
        $diQuRes["大陆"] = 1;
        $diQuRes["华语"] = 1;
        $diQuRes["好莱坞"] = 3;
        $diQuType = 0;
        foreach ($diQuInfo as $diQuKey => $diQuVal) {
            if (strpos($diQu, $diQuVal) !== false) {
                $diQuType = $diQuKey;
                break;
            }
        }
        return empty($diQuType) ? $diQuRes['其他'] : $diQuType;
    }

    /**
     * 拼接地区对应的类型码
     * @return array
     */
    protected function _getMoviceType($moviceT)
    {
        $moviceInfo = $this->get_config_value("movieType");
        $moviceRes = array();
        foreach ($moviceInfo as $moviceKey => $moviceVal) {
            $moviceRes[$moviceVal] = $moviceKey;
        }
        $moviceType = 0;
        foreach ($moviceInfo as $moviceKey => $moviceVal) {
            if (strpos($moviceT, $moviceVal) !== false) {
                $moviceType = $moviceKey;
                break;
            }
        }
        return empty($moviceType) ? $moviceRes['其他'] : $moviceType;
    }

    /**
     * 根据id和type获取信息
     * @param $webMoviceId
     * @param $webType
     * @return bool|mixed
     */
    protected function _getMoviceInfoByIdAndType($webMoviceId, $webType)
    {
        $webMoviceId = intval($webMoviceId);
        $webType = intval($webType);
        if (empty($webMoviceId) || empty($webType)) {
            return true;
        }
        $sql = "select * from `tbl_detailInfo` where webId = {$webMoviceId} and webType = {$webType} limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $info = $stmt->fetch();
        return $info;
    }

    /**
     * 根据infoId获取被合并电影信息
     * @param $webMoviceId
     * @param $webType
     * @return bool|mixed
     */
    protected function _getDelMoviceInfoById($infoId)
    {
        $infoId = intval($infoId);
        if (empty($infoId)) {
            return true;
        }
        $sql = "select * from `tbl_delMovieInfo` where infoId = ". $infoId ." and del = 0 limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $info = $stmt->fetch();
        return $info;
    }

    /**
     * 把字符串转成数组，支持汉字，只能是utf-8格式的
     * @param $str
     * @return array
     */
    protected function StringToArray($str)
    {
        $result = array();
        $len = strlen($str);
        $i = 0;
        while($i < $len){
            $chr = ord($str[$i]);
            if($chr == 9 || $chr == 10 || (32 <= $chr && $chr <= 126)) {
                $result[] = substr($str,$i,1);
                $i +=1;
            }elseif(192 <= $chr && $chr <= 223){
                $result[] = substr($str,$i,2);
                $i +=2;
            }elseif(224 <= $chr && $chr <= 239){
                $result[] = substr($str,$i,3);
                $i +=3;
            }elseif(240 <= $chr && $chr <= 247){
                $result[] = substr($str,$i,4);
                $i +=4;
            }elseif(248 <= $chr && $chr <= 251){
                $result[] = substr($str,$i,5);
                $i +=5;
            }elseif(252 <= $chr && $chr <= 253){
                $result[] = substr($str,$i,6);
                $i +=6;
            }
        }
        return $result;
    }
}