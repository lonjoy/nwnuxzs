<!doctype html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>成绩查询网页版</title>

  <!-- Set render engine for 360 browser -->
  <meta name="renderer" content="webkit">

  <!-- No Baidu Siteapp-->
  <meta http-equiv="Cache-Control" content="no-siteapp"/>

  <link rel="icon" type="image/png" href="assets/i/favicon.png">

  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="icon" sizes="192x192" href="assets/i/app-icon72x72@2x.png">

  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
  <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">

  <!-- Tile icon for Win8 (144x144 + tile color) -->
  <meta name="msapplication-TileImage" content="assets/i/app-icon72x72@2x.png">
  <meta name="msapplication-TileColor" content="#0e90d2">

  <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
  <!--
  <link rel="canonical" href="http://www.example.com/">
  -->
  <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.2.1/css/amazeui.min.css
">
<!-- <link rel="stylesheet" href="assets/css/amazeui.min.css"> -->
<link rel="stylesheet" href="assets/css/app.css">


  <?php 
   
    $openid = "";
    function getSql(){
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

    function get_td_array($table){

      $table = preg_replace("/<table[^>]*?>/is","",$table);
      $table = preg_replace("/<tr[^>]*?>/si","",$table);
      $table = preg_replace("/<td[^>]*?>/si","",$table);
      $table = str_replace("</tr>","{tr}",$table);
      $table = str_replace("</td>","{td}",$table);
      //去掉 HTML 标记
      $table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);
      //去掉空白字符
      $table = preg_replace("'([rn])[s]+'","",$table);
      $table = str_replace(" ","",$table);
      $table = str_replace(" ","",$table);
      $table = str_replace("&nbsp;","",$table);
    
      $table = explode('{tr}', $table);
      array_pop($table);
      foreach ($table as $key=>$tr) {
        $td = explode('{td}', $tr);
        $td = explode('{td}', $tr);
        array_pop($td);
        $td_array[] = $td;
      }
      return $td_array;
    }

    function curl($url){    
  
	    $ch = curl_init();   
	    curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址   
	    //curl_setopt($ch,CURLOPT_HEADER,1);            //是否显示头部信息   
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);           //设置超时   
	    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);   //用户访问代理 User-Agent   
	    curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer   
	    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301   
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果   
	    $str = curl_exec($ch);   
	    curl_close($ch);   
	    $str=mb_convert_encoding($str, "utf-8", "gb2312");
	    return $str;   
	}

	function getStid(){

		$openid =  $_GET['openid'];
        getSql();
        $sql = "SELECT * FROM nwnu_stid WHERE openid ='{$openid}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);
        $stid = $row['stid'];
        return $stid;

	}

    function grade(){
     
      $stid = getStid();
      //$stid = '93065';
      $url_cj = 'http://jw3.nwnu.edu.cn:7001/WebEducation/studentresultservlet?action=A&ontop=N&stid='.$stid;      
      $result_cj = curl($url_cj);
      $str_cj = get_td_array($result_cj);
      return $str_cj;

    }

    function userInfo(){

    	$stid = getStid();
    	$url_xj = 'http://jw3.nwnu.edu.cn:7001/WebEducation/studentidservlet?action=stview&ontop=N&stid='.$stid.'&grade=1111';
    	$result_xj = curl($url_xj);
    	$str_xj = get_td_array($result_xj);

    	return $str_xj;

    } 



  ?>

<style type="text/css">
	
	.userinfo>span{
		display: block;
	}	
	.title_cj>span{
		display: block;

	}

</style>

</head>
<body>
<!--[if lte IE 9]>
<p class="browsehappy">你正在使用<strong>过时</strong>的浏览器，Amaze UI 暂不支持。 请 <a
  href="http://browsehappy.com/" target="_blank">升级浏览器</a>
  以获得更好的体验！</p>
<![endif]-->

<!-- 页面内容 开发时删除 -->

<header data-am-widget="header" class="am-header am-header-default">  
  <h1 class="am-header-title">
    <a href="#title-link" class="">成绩查询</a>
  </h1>  
</header>    

 <section class="am-panel am-panel-default">
 <header class="am-panel-hd">
    <h3 class="am-panel-title">个人信息</h3>
  </header>
  <div class="am-panel-bd">

  <?php
  	$result_xj = userInfo();

  ?>
    <div class = "userinfo">
    	<span>姓名：<?php echo $result_xj[1][3];  ?></span>
    	<span>学号：<?php echo $result_xj[1][1];  ?></span>    
    	<span>学院：<?php echo $result_xj[0][1];  ?></span>
    	<span>专业：<?php echo $result_xj[0][3];  ?></span>
    </div>

  </div>
 
 <header class="am-panel-hd">
    <h3 class="am-panel-title">成绩信息</h3>
 </header>
 </section> 

<!--am-accordion-gapped default-->

<section data-am-widget="accordion" class="am-accordion am-accordion-gapped"
data-am-accordion='{  }'>

<?php
	$str_cj = grade();
    foreach ($str_cj as $value) {
        if($value[4] AND $value[1] != "课程编码") { 
          
?>

  <dl class="am-accordion-item am-active">
    <dt class="am-accordion-title">
	<div class= "title_cj">
		<span id = "title1">课程：<?php echo $value[2]; ?></span>
		<span id = "title2">成绩：<?php echo $value[9]; ?></span>
	</div>

      <!-- <table class="am-table">
       <thead>
         <tr>
          <td>课程：<?php echo $value[2]; ?></td>
          <td>成绩：<?php echo $value[9]; ?></td>      
        </tr>
       </thead>   
      </table> -->
    </dt>
    <dd class="am-accordion-bd am-collapse am-in">
      <!-- 规避 Collapase 处理有 padding 的折叠内容计算计算有误问题， 加一个容器 -->      
      <div class="am-accordion-content">课程序号：<?php echo $value[0] ?>
      	<br/>课程编码：<?php echo $value[1] ?>
        <br/>课程类型：<?php echo $value[3] ?>
        <br/>修读时间：<?php echo $value[4] ?>
        <br/>平时成绩：<?php echo $value[6] ?>
        <br/>期中成绩：<?php echo $value[7] ?>
        <br/>期末成绩：<?php echo $value[8] ?>
        <br/>总评成绩：<?php echo $value[9] ?>
        <br/>补考成绩：<?php echo $value[10] ?>
        <br/>学分：    <?php echo $value[11] ?>
        </div>
    </dd>
  </dl>  

<?php
	
		}
	}
?>
 
</section>

<div data-am-widget="gotop" class="am-gotop am-gotop-fixed">
  <a href="#top" title="">
    <i class="am-gotop-icon am-icon-hand-o-up"></i>
  </a>
</div>

<footer class="am-margin-top">
  <hr/>
  <p class="am-text-center">
    <small>Designed By 红领巾小分队</small>
  </p>
</footer>
<!-- 以上页面内容 开发时删除 -->

<!--[if lt IE 9]>
<script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="assets/js/polyfill/rem.min.js"></script>
<script src="assets/js/polyfill/respond.min.js"></script>
<script src="assets/js/amazeui.legacy.js"></script>
<![endif]-->

<!--[if (gte IE 9)|!(IE)]><!-->


<!-- Latest compiled and minified CSS and JS -->
<script src="http://libs.useso.com/js/jquery/2.1.1/jquery.min.js"></script>
<script src="http://cdn.amazeui.org/amazeui/2.1.0/js/amazeui.min.js"></script>    
<!-- 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/amazeui.min.js"></script> -->
<!--<![endif]-->

</body>
</html>
