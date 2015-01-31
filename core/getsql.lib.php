<?php
namespace LaneWeChat\Core;
/*
*	MySQL连接
*	class Getsql
*	@Author: Luo Ning   
*   @Date: 15-1-26
*   @Time: 凌晨0:42
*   @Mail: 947703573@qq.com
*/


class Getsql{

	public static function getsql(){
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



  