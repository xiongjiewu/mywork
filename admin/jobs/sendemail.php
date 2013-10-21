<?php
/**
 * 发送邮件队列job
 * added by xiongjiewu at 2013-3-31
 */
include(dirname(__FILE__) . "/../phpmailer/class.phpmailer.php");
$send = new sendEmail();
$send->run();
class sendEmail extends myPdo {
    private $_pdo;
    private $_HostName = "smtp.ym.163.com";
    private $_sendName;
    private $_sendEmail = "admin@dianying8.tv";
    private $_sendPassword = "admin1w1990";

    //当前日期
    private $_currentDay;
    //当前时间点
    private $_G;

    public function __construct() {
        $this->_pdo = $this->getPdo();
        $this->_sendName = $this->get_config_value("web_name") . "管理员";
        $this->_currentDay = date("Ymd");
    }
    function run() {
        while(true) {
            $this->_G = date("G");//当前时间点

            //需要处理的邮件
            $emailInfos = $this->_getEmailInfos();
            if (!empty($emailInfos)) {
              foreach($emailInfos as $emailVal) {
                 $title = $this->ubb2html($emailVal['title']);
                 $content = $this->ubb2html($emailVal['content']);
                 $sendRes = $this->_sendEmail($title,$content,$emailVal['email'],$emailVal['userName']);
                 if ($sendRes) {
                    $this->_updateEmailInfoById($emailVal['id']);
                    printf("--ID为：%s的记录---发送成功---%s\n",$emailVal['id'],date("Y-m-d"));
                 } else {
                    printf("--ID为：%s的记录---发送失败---%s\n",$emailVal['id'],date("Y-m-d"));
                 }
              }
            }

            //每隔7天的23点重启job
            if ((date("Ymd") - $this->_currentDay) > 7 && ($this->_G == 23)) {
                exit;
            } else {
                sleep(1);
            }
        }
    }

    private $_limit = 100;

    /**
     * 获取需要处理的邮件
     * @return array
     */
    private function _getEmailInfos() {
        $sql = "select id,title,content,email,time,status,userName from `tbl_emailQueue` where status = 0 order by id asc limit " . $this->_limit . ";";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * 更新邮件为已处理
     * @param $id
     * @return bool|int
     */
    private function _updateEmailInfoById($id) {
        $id = intval($id);
        if (empty($id)) {
            return false;
        }
        $sql = "update `tbl_emailQueue` set status = 1 where id = ? limit 1;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->execute(array($id));
        return $stmt->rowCount();
    }

    /**
     * 发送邮件
     * @param $title
     * @param $content
     * @param $email
     * @param $userName
     * @return bool
     */
    private function _sendEmail($title,$content,$email,$userName) {
        if (!isset($title) || !isset($content) || !isset($email)) {
            return true;
        }
        $mail = new PHPMailer();
        $mail->IsSMTP();                  // send via SMTP
        $mail->Host = gethostbyname($this->_HostName);   // SMTP servers
        $mail->SMTPAuth = true;           // turn on SMTP authentication
        $mail->Username = $this->_sendEmail;     // SMTP username  注意：普通邮件认证不需要加 @域名
        $mail->Password = $this->_sendPassword; // SMTP password
        $mail->From = $this->_sendEmail;      // 发件人邮箱
        $mail->FromName =  $this->_sendName;  // 发件人

        $mail->CharSet = "UTF8";   // 这里指定字符集！
        $mail->AddAddress($email,$userName);  // 收件人邮箱和姓名
        $mail->IsHTML(true);  // send as HTML
        // 邮件主题
        $mail->Subject = $title;
        $mail->Body = $content;
        return $mail->Send();
    }
}
