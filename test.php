<?php
namespace LaneWeChat\Core;
include 'lanewechat.php';

/*$stid = '93065';
$url_cj = 'http://jw3.nwnu.edu.cn:7001/WebEducation/studentresultservlet?action=A&ontop=N&stid='.$stid;
$request = \LaneWeChat\Core\Curl::callWebServer($url_cj,'','get');*/

/*print_r($request);*/
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

$stid = '93065';
$sql = "SELECT * FROM nwnu_stid WHERE stid = '{$stid}'";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$jwid = $row['jwid'];
$person_id = $row['person_id'];
print_r($person_id);
print_r($jwid);

$openid = $request['fromusername'];
print_r($openid);