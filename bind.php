<?php
/**
 * Created by PhpStorm.
 * User: ln_dj
 * Date: 2015/1/28
 * Time: 3:38
 */
include "lanewechat.php";


$xuehao = $_POST['xuehao'];
if($xuehao){

}


class Bangding {

    public function bind(){
        $jwid = $_POST['jwid'];
        $person_id = $_POST['person_id'];
        $openid = $_POST['openid'];

        $this->getSql();
        $sql = "SELECT * FROM nwnu_stid WHERE openid ='{$openid}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);

        if(!empty($row)){
            $arr=array('data'=>'您已经绑定了学号呀，无需再次绑定了。如果您想更换学号查询，请使用其他微信账号或者首先取消绑定。');
            echo  json_encode($arr);
        }else{
            //如果是未绑定用户，查询学号，并比对身份证号。如果没有学号，返回无法查询。
            $sql = "SELECT * FROM nwnu WHERE jwid = '{$jwid}'";
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            if(empty($row)){
                $arr = array('data' => '由于一些特殊原因，您暂时无法查询，请联系红领巾小分队协助您解决问题。');
                echo json_encode($arr);
            }else{
                $person_id_sql = $row['person_id'];
                if ($person_id == $person_id_sql) {
                    $sql = "INSERT INTO `nwnu_stid`(`id`, `stid`, `jwid`, `person_id`, `openid`) VALUES (null, null, null, null, '{$openid}')";
                    if(!mysql_query($sql)){
                        $arr=array('data'=>'未知原因，绑定失败，请重试');
                        echo  json_encode($arr);
                    }else{
                        $arr=array('msg'=>'success');
                        echo  json_encode($arr);
                    }
                }else{
                    $arr=array('data'=>'您填写的身份证号码有误，请再试一次。');
                    echo  json_encode($arr);
                }
            }
        }
    }

    private function getSql(){
        $dbname = "nwnu";
        $host = '127.0.0.1';
        $port = 3306;
        $user = 'root';//用户名(api key)
        $pwd = 'XhuXT7g8';//密码(secret key)
        /*接着调用mysql_connect()连接服务器*/
        $link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
        if(!$link) {
                    die("Connect Server Failed: " . mysql_error($link));
                   }
        /*连接成功后立即调用mysql_select_db()选中需要连接的数据库*/
        if(!mysql_select_db($dbname,$link)) {
                    die("Select Database Failed: " . mysql_error($link));
                   }
        mysql_query("set character set 'utf8'");
    }

}