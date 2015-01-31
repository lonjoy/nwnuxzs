<?php
namespace LaneWeChat\Core;
/**
 * 处理请求
 * Created by Lane.
 * User: lane
 * Date: 13-12-19
 * Time: 下午11:04
 * Mail: lixuan868686@163.com
 * Website: http://www.lanecn.com
 */

class WechatRequest{
    /**
     * @descrpition 分发请求
     * @param $request
     * @return array|string
     */
    public static function switchType(&$request){
        $data = array();
        switch ($request['msgtype']) {
            //事件
            case 'event':
                $request['event'] = strtolower($request['event']);
                switch ($request['event']) {
                    //关注
                    case 'subscribe':
                        //二维码关注
                        if(isset($request['eventkey']) && isset($request['ticket'])){
                            $data = self::eventQrsceneSubscribe($request);
                        //普通关注
                        }else{
                            $data = self::eventSubscribe($request);
                        }
                        break;
                    //扫描二维码
                    case 'scan':
                        $data = self::eventScan($request);
                        break;
                    //地理位置
                    case 'location':
                        $data = self::eventLocation($request);
                        break;
                    //自定义菜单 - 点击菜单拉取消息时的事件推送
                    case 'click':
                        $data = self::eventClick($request);
                        break;
                    //自定义菜单 - 点击菜单跳转链接时的事件推送
                    case 'view':
                        $data = self::eventView($request);
                        break;
                    //自定义菜单 - 扫码推事件的事件推送
                    case 'scancode_push':
                        $data = self::eventScancodePush($request);
                        break;
                    //自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
                    case 'scancode_waitmsg':
                        $data = self::eventScancodeWaitMsg($request);
                        break;
                    //自定义菜单 - 弹出系统拍照发图的事件推送
                    case 'pic_sysphoto':
                        $data = self::eventPicSysPhoto($request);
                        break;
                    //自定义菜单 - 弹出拍照或者相册发图的事件推送
                    case 'pic_photo_or_album':
                        $data = self::eventPicPhotoOrAlbum($request);
                        break;
                    //自定义菜单 - 弹出微信相册发图器的事件推送
                    case 'pic_weixin':
                        $data = self::eventPicWeixin($request);
                        break;
                    //自定义菜单 - 弹出地理位置选择器的事件推送
                    case 'location_select':
                        $data = self::eventLocationSelect($request);
                        break;
                    //取消关注
                    case 'unsubscribe':
                        $data = self::eventUnsubscribe($request);
                        break;
                    //群发接口完成后推送的结果
                    case 'masssendjobfinish':
                        $data = self::eventMassSendJobFinish($request);
                        break;
                    //模板消息完成后推送的结果
                    case 'templatesendjobfinish':
                        $data = self::eventTemplateSendJobFinish($request);
                        break;
                    default:
                        return Msg::returnErrMsg(MsgConstant::ERROR_UNKNOW_TYPE, '收到了未知类型的消息', $request);
                        break;
                }
                break;
            //文本
            case 'text':
                $data = self::text($request);
                break;
            //图像
            case 'image':
                $data = self::image($request);
                break;
            //语音
            case 'voice':
                $data = self::voice($request);
                break;
            //视频
            case 'video':
                $data = self::video($request);
                break;
            //位置
            case 'location':
                $data = self::location($request);
                break;
            //链接
            case 'link':
                $data = self::link($request);
                break;
            default:
                return ResponsePassive::text($request['fromusername'], $request['tousername'], '收到未知的消息，我不知道怎么处理');
                break;
        }
        return $data;
    }


    /**
     * @descrpition 文本
     * @param $request
     * @return array
     */
    public static function text(&$request){
        $keyword = $request['content'];
        //分别截取用户发送内容的前两个字符以及之后的字符

        $openid = $request['fromusername'];
        $str_trans = mb_substr($keyword,0,2,"UTF-8");
        $str_valid = mb_substr($keyword,0,-2,"UTF-8");
        if($keyword == '每日一句'){

            $item = self::dayilyEnglish();           
            return  ResponsePassive::news($request['fromusername'], $request['tousername'], $item);

        }elseif ($str_trans == '翻译' && !empty($str_valid)) {
            
            $description = self::youdaoDic($keyword);
            $tuwenList = array();
            $tuwenList[] = array('title'=>'有道词典翻译结果', 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>'', 'url'=>'');     
            $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return  ResponsePassive::news($request['fromusername'], $request['tousername'], $item);           
        }elseif($keyword == "哈哈"){
            $tuwenList = array();
            $tuwenList[] = array('title'=>'据说这种排版效果好哦~', 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>$openid, 'description'=>'', 'pic_url'=>'http://cdn-img.easyicon.net/png/11832/1183257.gif', 'url'=>'');
            $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                 }
          return  ResponsePassive::news($request['fromusername'], $request['tousername'], $item);
        }elseif($keyword == "成绩"){

            $item = self::grade($openid);
            return  ResponsePassive::news($request['fromusername'], $request['tousername'], $item);

        }elseif($str_trans == '绑定' && !empty($str_valid)){      

            $arr = explode("+", $keyword);
            $jwid = $arr[1];
            $person_id = $arr[2];

            $item = self::bind($jwid,$person_id,$openid);
            return  ResponsePassive::news($request['fromusername'], $request['tousername'], $item);

        }elseif($keyword == "取消绑定" || $keyword == "解除绑定" ){  

            $item = self::bindOff($openid);
            return  ResponsePassive::news($request['fromusername'], $request['tousername'], $item);

        }else{                                           //未设置关键词内容交由图灵机器人处理

            $answer = self::tulingRobot($keyword); 
            if(is_array($answer)){
                return  ResponsePassive::news($request['fromusername'], $request['tousername'], $answer);
            }else{
                return  ResponsePassive::text($request['fromusername'], $request['tousername'], $answer);
            }          
             
        }
      
    }

    /**
     * @descrpition 图像
     * @param $request
     * @return array
     */
    public static function image(&$request){
        $content = '收到图片';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 语音
     * @param $request
     * @return array
     */
    public static function voice(&$request){
        if(!isset($request['recognition'])){
            $content = '收到语音';
            return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
        }else{
            $content = '收到语音识别消息，语音识别结果为：'.$request['recognition'];
            return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
        }
    }

    /**
     * @descrpition 视频
     * @param $request
     * @return array
     */
    public static function video(&$request){
        $content = '收到视频';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 地理
     * @param $request
     * @return array
     */
    public static function location(&$request){
        $content = '收到上报的地理位置';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 链接
     * @param $request
     * @return array
     */
    public static function link(&$request){
        $content = '收到链接';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 关注
     * @param $request
     * @return array
     */
    public static function eventSubscribe(&$request){
        $content = '终于等到你~还好我没放弃……查成绩、查课表、四六级……带你玩转教务网！回复【成绩】、【每日一句】试试吧！';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 取消关注
     * @param $request
     * @return array
     */
    public static function eventUnsubscribe(&$request){
        $content = '为什么不理我了？';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 扫描二维码关注（未关注时）
     * @param $request
     * @return array
     */
    public static function eventQrsceneSubscribe(&$request){
        $content = '欢迎您关注我们的微信，将为您竭诚服务';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 扫描二维码（已关注时）
     * @param $request
     * @return array
     */
    public static function eventScan(&$request){
        $content = '您已经关注了哦～';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 上报地理位置
     * @param $request
     * @return array
     */
    public static function eventLocation(&$request){
        $content = '收到上报的地理位置';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 点击菜单拉取消息时的事件推送
     * @param $request
     * @return array
     */
    public static function eventClick(&$request){
		//获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到点击菜单事件，您设置的key是' . $eventKey;
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 点击菜单跳转链接时的事件推送
     * @param $request
     * @return array
     */
    public static function eventView(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到跳转链接事件，您设置的key是' . $eventKey;
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 扫码推事件的事件推送
     * @param $request
     * @return array
     */
    public static function eventScancodePush(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到扫码推事件的事件，您设置的key是' . $eventKey;
        $content .= '。扫描信息：'.$request['scancodeinfo'];
        $content .= '。扫描类型(一般是qrcode)：'.$request['scantype'];
        $content .= '。扫描结果(二维码对应的字符串信息)：'.$request['scanresult'];
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
     * @param $request
     * @return array
     */
    public static function eventScancodeWaitMsg(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到扫码推事件且弹出“消息接收中”提示框的事件，您设置的key是' . $eventKey;
        $content .= '。扫描信息：'.$request['scancodeinfo'];
        $content .= '。扫描类型(一般是qrcode)：'.$request['scantype'];
        $content .= '。扫描结果(二维码对应的字符串信息)：'.$request['scanresult'];
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出系统拍照发图的事件推送
     * @param $request
     * @return array
     */
    public static function eventPicSysPhoto(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到弹出系统拍照发图的事件，您设置的key是' . $eventKey;
        $content .= '。发送的图片信息：'.$request['sendpicsinfo'];
        $content .= '。发送的图片数量：'.$request['count'];
        $content .= '。图片列表：'.$request['piclist'];
        $content .= '。图片的MD5值，开发者若需要，可用于验证接收到图片：'.$request['picmd5sum'];
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出拍照或者相册发图的事件推送
     * @param $request
     * @return array
     */
    public static function eventPicPhotoOrAlbum(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到弹出拍照或者相册发图的事件，您设置的key是' . $eventKey;
        $content .= '。发送的图片信息：'.$request['sendpicsinfo'];
        $content .= '。发送的图片数量：'.$request['count'];
        $content .= '。图片列表：'.$request['piclist'];
        $content .= '。图片的MD5值，开发者若需要，可用于验证接收到图片：'.$request['picmd5sum'];
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出微信相册发图器的事件推送
     * @param $request
     * @return array
     */
    public static function eventPicWeixin(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到弹出微信相册发图器的事件，您设置的key是' . $eventKey;
        $content .= '。发送的图片信息：'.$request['sendpicsinfo'];
        $content .= '。发送的图片数量：'.$request['count'];
        $content .= '。图片列表：'.$request['piclist'];
        $content .= '。图片的MD5值，开发者若需要，可用于验证接收到图片：'.$request['picmd5sum'];
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出地理位置选择器的事件推送
     * @param $request
     * @return array
     */
    public static function eventLocationSelect(&$request){
        //获取该分类的信息
        $eventKey = $request['eventkey'];
        $content = '收到点击跳转事件，您设置的key是' . $eventKey;
        $content .= '。发送的位置信息：'.$request['sendlocationinfo'];
        $content .= '。X坐标信息：'.$request['location_x'];
        $content .= '。Y坐标信息：'.$request['location_y'];
        $content .= '。精度(可理解为精度或者比例尺、越精细的话 scale越高)：'.$request['scale'];
        $content .= '。地理位置的字符串信息：'.$request['label'];
        $content .= '。朋友圈POI的名字，可能为空：'.$request['poiname'];
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * 群发接口完成后推送的结果
     *
     * 本消息有公众号群发助手的微信号“mphelper”推送的消息
     * @param $request
     */
    public static function eventMassSendJobFinish(&$request){
        //发送状态，为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
        $status = $request['status'];
        //计划发送的总粉丝数。group_id下粉丝数；或者openid_list中的粉丝数
        $totalCount = $request['totalcount'];
        //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount
        $filterCount = $request['filtercount'];
        //发送成功的粉丝数
        $sentCount = $request['sentcount'];
        //发送失败的粉丝数
        $errorCount = $request['errorcount'];
        $content = '发送完成，状态是'.$status.'。计划发送总粉丝数为'.$totalCount.'。发送成功'.$sentCount.'人，发送失败'.$errorCount.'人。';
        return ResponsePassive::text($request['fromusername'], $request['tousername'], $content);
    }

    /**
     * 群发接口完成后推送的结果
     *
     * 本消息有公众号群发助手的微信号“mphelper”推送的消息
     * @param $request
     */
    public static function eventTemplateSendJobFinish(&$request){
        //发送状态，成功success，用户拒收failed:user block，其他原因发送失败failed: system failed
        $status = $request['status'];
        if($status == 'success'){
            //发送成功
        }else if($status == 'failed:user block'){
            //因为用户拒收而发送失败
        }else if($status == 'failed: system failed'){
            //其他原因发送失败
        }
        return ;
    }


    public static function test(){
        // 第三方发送消息给公众平台
        $encodingAesKey = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG";
        $token = "weixin";
        $timeStamp = "1409304348";
        $nonce = "xxxxxx";
        $appId = "wxb11529c136998cb6";
        $text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";


        $pc = new Aes\WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $encryptMsg = '';
        $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
        if ($errCode == 0) {
            print("加密后: " . $encryptMsg . "\n");
        } else {
            print($errCode . "\n");
        }

        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $array_s = $xml_tree->getElementsByTagName('MsgSignature');
        $encrypt = $array_e->item(0)->nodeValue;
        $msg_sign = $array_s->item(0)->nodeValue;

        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);

// 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            print("解密后: " . $msg . "\n");
        } else {
            print($errCode . "\n");
        }
    }

    /*
    *   有道翻译
    *   @param $keyword   
    */

    private static function youdaoDic($keyword){
        $word = mb_substr($keyword,2,202,"UTF-8");     
        // $keyfrom = "lzjtuxzs";    //申请APIKEY 时所填表的网站名称的内容
        //$apikey = "1003322252";  //从有道申请的APIKEY      
        //有道翻译-xml格式
        $url_youdao = 'http://fanyi.youdao.com/fanyiapi.do?keyfrom=lzjtujwzs&key=1038638365&type=data&doctype=xml&version=1.1&q='.$word;
        //$con = file_get_contents($url_youdao);
        //print_r($con);
        $xmlStyle = simplexml_load_file($url_youdao);        
        $errorCode = $xmlStyle->errorCode;
        $paras = $xmlStyle->translation->paragraph;
        $yinbiao = $xmlStyle->basic->phonetic;
        $jiben = $xmlStyle->basic->explains->ex;        
        if($errorCode == 0){
           return "基本释义：".$paras."\n"."音标：".'/'.$yinbiao.'/'."\n"."网络释义：".$jiben;                
        }else{
            return "无法进行有效的翻译";
        }  
    }
    /*
    *$param 
    * 每日一句
    */
    private static function dayilyEnglish(){

         $result = Curl::callWebServer('http://open.iciba.com/dsapi/','','get');
         $english = array(); 
         $english[] = array("Title" =>"每日一句 ".$result["dateline"], "Description"=>$result["content"]."\n".$result["note"]."\n\n".str_replace("词霸小编：", "", $result["translation"]), "PicUrl" =>$result["picture"], "Url" =>"","VoiceUrl" => $result["tts"]);     
        
         $title = $english[0]['Title'];
         $description = $english[0]['Description'];
         $pic_url = $english[0]['PicUrl'];

         $tuwenList = array();
         $tuwenList[] = array('title'=>$title, 'description'=>$description, 'pic_url'=>$pic_url, 'url'=>'');   
         $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
         return  $item;
    } 

    /*
    * @param 
    * 图灵机器人
    *
    */
    private static function tulingRobot($keyword){
        $openid= $request['fromusername'];
        $apiKey = "8523eb72f0ac3e3af3fe0df600d61106"; 
        $apiUrl = 'http://www.tuling123.com/openapi/api?key='.$apiKey.'&info='.$keyword.'&userid='.$openid;
        $result = \LaneWeChat\Core\Curl::callWebServer($apiUrl,'','get');

        if($result['code'] == 100000){               //回复文本消息

            return $result['text'];

        }elseif ($result['code'] == 302000) {        //回复新闻消息          

            $title1 = $result['list'][1]['article'];
            $pic_url1 = $result['list'][1]['icon'];
            $url1 = $result['list'][1]['detailurl'];

            $title2 = $result['list'][2]['article'];
            $pic_url2 = $result['list'][2]['icon'];
            $url2 = $result['list'][2]['detailurl'];

            $title3 = $result['list'][3]['article'];
            $pic_url3 = $result['list'][3]['icon'];
            $url3 = $result['list'][3]['detailurl'];

            $title4 = $result['list'][4]['article'];
            $pic_url4 = $result['list'][4]['icon'];
            $url4 = $result['list'][4]['detailurl'];

            $title5 = $result['list'][5]['article'];
            $pic_url5 = $result['list'][5]['icon'];
            $url5 = $result['list'][5]['detailurl']; 

            $title6 = $result['list'][6]['article'];
            $pic_url6 = $result['list'][6]['icon'];
            $url6 = $result['list'][6]['detailurl'];

            $tuwenList[] = array('title'=>'据说了解时事的孩子才是好孩子', 'description'=>'', 'pic_url'=>'http://mmbiz.qpic.cn/mmbiz/FDk590mmNmqawqbey0rpJU8oTSbhfPgD2AGURoZDe9qAZPhclkxknCPTT9tuROGoPb4pvEQNZnhqrwEqy2h6QQ/640?tp=webp', 'url'=>'');           
            $tuwenList[] = array('title'=>$title1, 'description'=>'', 'pic_url'=>$pic_url1, 'url'=>$url1);
            $tuwenList[] = array('title'=>$title2, 'description'=>'', 'pic_url'=>$pic_url2, 'url'=>$url2);
            $tuwenList[] = array('title'=>$title3, 'description'=>'', 'pic_url'=>$pic_url3, 'url'=>$url3);
            $tuwenList[] = array('title'=>$title4, 'description'=>'', 'pic_url'=>$pic_url4, 'url'=>$url4);
            $tuwenList[] = array('title'=>$title5, 'description'=>'', 'pic_url'=>$pic_url5, 'url'=>$url5);
            $tuwenList[] = array('title'=>$title6, 'description'=>'', 'pic_url'=>$pic_url6, 'url'=>$url6);

             $itemNews = array();
            foreach($tuwenList as $tuwen){
                    $itemNews[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemNews;
        }elseif ($result['code'] == 304000) {        //APP下载类消息，可是！！！！！微信浏览器不支持下载！！！！！！！！！！！

            $title0 = $result['list'][0]['name'];
            $pic_url0 = $result['list'][0]['icon'];
            $url0 = $result['list'][0]['detailurl'];

            $title1 = $result['list'][1]['name'];
            $pic_url1 = $result['list'][1]['icon'];
            $url1 = $result['list'][1]['detailurl'];

            $title2 = $result['list'][2]['name'];
            $pic_url2 = $result['list'][2]['icon'];
            $url2 = $result['list'][2]['detailurl'];

            $title3 = $result['list'][3]['name'];
            $pic_url3 = $result['list'][3]['icon'];
            $url3 = $result['list'][3]['detailurl'];

            $title4 = $result['list'][4]['name'];
            $pic_url4 = $result['list'][4]['icon'];
            $url4 = $result['list'][4]['detailurl'];

            $title5 = $result['list'][5]['name'];
            $pic_url5 = $result['list'][5]['icon'];
            $url5 = $result['list'][5]['detailurl'];

            $tuwenList[] = array('title'=>'小助手推荐您几个相关APP，微信不可以下载，安卓随意，iphone去APPSTORE搜索下吧。', 'description'=>'', 'pic_url'=>'http://mmbiz.qpic.cn/mmbiz/FDk590mmNmqTnicCeN4NyoiaQlLapt7PdZMqQmrWia4uicP9zYxH04Euib8PcPttzZHMeMFSHPN9GPNz6c51t3ACCyg/640?tp=webp', 'url'=>'');
            $tuwenList[] = array('title'=>$title0, 'description'=>'', 'pic_url'=>$pic_url0, 'url'=>$url0);
            $tuwenList[] = array('title'=>$title1, 'description'=>'', 'pic_url'=>$pic_url1, 'url'=>$url1);
            $tuwenList[] = array('title'=>$title2, 'description'=>'', 'pic_url'=>$pic_url2, 'url'=>$url2);
            $tuwenList[] = array('title'=>$title3, 'description'=>'', 'pic_url'=>$pic_url3, 'url'=>$url3);
            $tuwenList[] = array('title'=>$title4, 'description'=>'', 'pic_url'=>$pic_url4, 'url'=>$url4);
            $tuwenList[] = array('title'=>$title5, 'description'=>'', 'pic_url'=>$pic_url5, 'url'=>$url5);

            $itemAPP = array();
            foreach($tuwenList as $tuwen){
                    $itemAPP[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemAPP;
        }elseif ($result['code'] == 305000) {             //火车消息
            $trainnum0 = $result['list'][0]['trainnum'];
            $start0 = $result['list'][0]['start'];
            $terminal0 = $result['list'][0]['terminal'];
            $starttime0 = $result['list'][0]['starttime'];
            $endtime0 = $result['list'][0]['endtime'];

            $trainnum1 = $result['list'][1]['trainnum'];
            $start1 = $result['list'][1]['start'];
            $terminal1 = $result['list'][1]['terminal'];
            $starttime1 = $result['list'][1]['starttime'];
            $endtime1 = $result['list'][1]['endtime'];

            $trainnum2 = $result['list'][2]['trainnum'];
            $start2 = $result['list'][2]['start'];
            $terminal2 = $result['list'][2]['terminal'];
            $starttime2 = $result['list'][2]['starttime'];
            $endtime2 = $result['list'][2]['endtime'];

            $trainnum3 = $result['list'][3]['trainnum'];
            $start3 = $result['list'][3]['start'];
            $terminal3 = $result['list'][3]['terminal'];
            $starttime3 = $result['list'][3]['starttime'];
            $endtime3 = $result['list'][3]['endtime'];

            $trainnum4 = $result['list'][4]['trainnum'];
            $start4 = $result['list'][4]['start'];
            $terminal4 = $result['list'][4]['terminal'];
            $starttime4 = $result['list'][4]['starttime'];
            $endtime4 = $result['list'][4]['endtime'];

            $trainInfo = "1.".$trainnum0."\n".$start0."——".$terminal0."\n"."运行时间：".$starttime0."——".$endtime0."\n"."---------------------"."\n"."2.".$trainnum1."\n".$start1."——".$terminal1."\n"."运行时间：".$starttime1."——".$endtime1."\n"."---------------------"."\n"."3.".$trainnum2."\n".$start2."——".$terminal2."\n"."运行时间：".$starttime2."——".$endtime2."\n"."---------------------"."\n"."4.".$trainnum3."\n".$start3."——".$terminal3."\n"."运行时间：".$starttime3."——".$endtime3."\n"."---------------------"."\n"."5.".$trainnum4."\n".$start4."——".$terminal4."\n"."运行时间：".$starttime4."——".$endtime4."\n"."---------------------"."\n"."【ps】(+1)表示次日到达";


            $tuwenList[] = array('title'=>"小助手帮您找到了以下的车次信息", 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>$trainInfo, 'description'=>'', 'pic_url'=>'', 'url'=>'http://touch.qunar.com/h5/train/');

            $itemTrain = array();
            foreach($tuwenList as $tuwen){
                    $itemTrain[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemTrain;
        }elseif ($result['code'] == 306000) {          //航班消息
            $flight0 = $result['list'][0]['flight'];   
            $starttime0 = $result['list'][0]['starttime'];
            $endtime0 = $result['list'][0]['endtime'];

            $flight1 = $result['list'][1]['flight'];   
            $starttime1 = $result['list'][1]['starttime'];
            $endtime1 = $result['list'][1]['endtime'];

            $flight2 = $result['list'][2]['flight'];   
            $starttime2 = $result['list'][2]['starttime'];
            $endtime2 = $result['list'][2]['endtime'];

            $flight3 = $result['list'][3]['flight'];   
            $starttime3 = $result['list'][3]['starttime'];
            $endtime3 = $result['list'][3]['endtime'];

            $flight4 = $result['list'][4]['flight'];   
            $starttime4 = $result['list'][4]['starttime'];
            $endtime4 = $result['list'][4]['endtime'];    

            $trainInfo = "1.".$flight0."\n"."航行时间：".$starttime0."——".$endtime0."\n"."----------------------"."2.".$flight1."\n"."航行时间：".$starttime1."——".$endtime1."\n"."----------------------"."3.".$flight2."\n"."航行时间：".$starttime2."——".$endtime2."\n"."----------------------"."4.".$flight3."\n"."航行时间：".$starttime3."——".$endtime3."\n"."----------------------"."5.".$flight4."\n"."航行时间：".$starttime4."——".$endtime4."\n"."----------------------"."\n"."点击查询更多信息";

            $tuwenList[] = array('title'=>"小助手帮您找到了以下航班信息", 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>$trainInfo, 'description'=>'', 'pic_url'=>'', 'url'=>'http://touch.qunar.com/h5/flight/');

            $itemFlight = array();
            foreach($tuwenList as $tuwen){
                    $itemFlight[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemFlight;
        }elseif ($result['code'] == 200000 ) {    //电影消息

            $title = $result['text'];
            $url = $result['url'];

            $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>"点击我进入查看吧", 'description'=>'', 'pic_url'=>'http://cdn-img.easyicon.net/png/11832/1183288.gif', 'url'=>$url);

            $itemFilm = array();
            foreach($tuwenList as $tuwen){
                    $itemFilm[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemFilm;
        }elseif ($result['code'] == 308000) {            //视频消息

            $name0 = $result['list'][0]['name'];   
            $info0 = $result['list'][0]['info'];
            $icon0 = $result['list'][0]['icon'];
            $detailurl0 = $result['list'][0]['detailurl'];

            $name1 = $result['list'][1]['name'];   
            $info1 = $result['list'][1]['info'];
            $icon1 = $result['list'][1]['icon'];
            $detailurl1 = $result['list'][1]['detailurl'];

            $name2 = $result['list'][2]['name'];   
            $info2 = $result['list'][2]['info'];
            $icon2 = $result['list'][2]['icon'];
            $detailurl2 = $result['list'][2]['detailurl'];

            $name3 = $result['list'][3]['name'];   
            $info3 = $result['list'][3]['info'];
            $icon3 = $result['list'][3]['icon'];
            $detailurl3 = $result['list'][3]['detailurl'];

            $name4 = $result['list'][4]['name'];   
            $info4 = $result['list'][4]['info'];
            $icon4 = $result['list'][4]['icon'];
            $detailurl4 = $result['list'][4]['detailurl'];           

            $tuwenList[] = array('title'=>"小助手帮您找到了以下视频", 'description'=>'', 'pic_url'=>'http://mmbiz.qpic.cn/mmbiz/FDk590mmNmqawqbey0rpJU8oTSbhfPgD2AGURoZDe9qAZPhclkxknCPTT9tuROGoPb4pvEQNZnhqrwEqy2h6QQ/640?tp=webp', 'url'=>'');
            $tuwenList[] = array('title'=>$name0."\n".$info0, 'description'=>'', 'pic_url'=>$icon0, 'url'=>$detailurl0);
            $tuwenList[] = array('title'=>$name1."\n".$info1, 'description'=>'', 'pic_url'=>$icon1, 'url'=>$detailurl1);
            $tuwenList[] = array('title'=>$name2."\n".$info2, 'description'=>'', 'pic_url'=>$icon2, 'url'=>$detailurl2);
            $tuwenList[] = array('title'=>$name3."\n".$info3, 'description'=>'', 'pic_url'=>$icon3, 'url'=>$detailurl3);
            $tuwenList[] = array('title'=>$name4."\n".$info4, 'description'=>'', 'pic_url'=>$icon4, 'url'=>$detailurl4);

            $itemVideo = array();
            foreach($tuwenList as $tuwen){
                    $itemVideo[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemVideo;
        }elseif ($result['code'] == 309000) {           //酒店消息

            $name0 = $result['list'][0]['name'];  
            $price0 = $result['list'][0]['price'];
            $icon0 = $result['list'][0]['icon']; 

            $name1 = $result['list'][1]['name'];  
            $price1 = $result['list'][1]['price'];
            $icon1 = $result['list'][1]['icon']; 

            $name2 = $result['list'][2]['name'];  
            $price2 = $result['list'][2]['price'];
            $icon2 = $result['list'][2]['icon']; 

            $name3 = $result['list'][3]['name'];  
            $price3 = $result['list'][3]['price'];
            $icon3 = $result['list'][3]['icon']; 

            $name4 = $result['list'][4]['name'];  
            $price4 = $result['list'][4]['price'];
            $icon4 = $result['list'][4]['icon'];

            $tuwenList[] = array('title'=>"小助手帮您找到了以下相关酒店", 'description'=>'', 'pic_url'=>'http://mmbiz.qpic.cn/mmbiz/FDk590mmNmqawqbey0rpJU8oTSbhfPgD2AGURoZDe9qAZPhclkxknCPTT9tuROGoPb4pvEQNZnhqrwEqy2h6QQ/640?tp=webp', 'url'=>'');
            $tuwenList[] = array('title'=>$name0."\n".$price0, 'description'=>'', 'pic_url'=>$icon0, 'url'=>'');
            $tuwenList[] = array('title'=>$name1."\n".$price1, 'description'=>'', 'pic_url'=>$icon1, 'url'=>'');
            $tuwenList[] = array('title'=>$name2."\n".$price2, 'description'=>'', 'pic_url'=>$icon2, 'url'=>'');
            $tuwenList[] = array('title'=>$name3."\n".$price3, 'description'=>'', 'pic_url'=>$icon3, 'url'=>'');
            $tuwenList[] = array('title'=>$name4."\n".$price4, 'description'=>'', 'pic_url'=>$icon4, 'url'=>'');

            $itemHotel = array();
            foreach($tuwenList as $tuwen){
                    $itemHotel[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
            return $itemHotel;
        }else{
            $answer = "你说什么？小助手都不知道该怎么回答你啦……";
            return $answer;
        }

    }
    
    /*
    *   判断是否新用户
    *   @param
    */
    private static function isUser($openid){

        //$url_bd = 'http://www.tsdesign.hk/wechat/.......';
        self::getSql();       
        $sql = "SELECT * FROM nwnu_stid WHERE openid ='{$openid}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);
        if(empty($row)){
            $tuwenList = array();
            $tuwenList[] = array('title'=>'呀，小助手检测到您是第一次使用哦，乖乖绑定吧~', 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>"回复 绑定+学号+身份证号码 即可绑定，例如：\n绑定+201451010117+62020119900725xxxx", 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                 }
            return $item;   
            exit;
        }else{
            $stid = $row['stid'];
            return $stid;
        }
    } 
    /*
    *   @param $jwid,$person_id,$openid
    *   新用户回复绑定
    *   @return  array
    */

    private static function bind($jwid,$person_id,$openid){

        self::getSql();
        $sql = "SELECT * FROM nwnu_stid WHERE openid ='{$openid}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);

         if(!empty($row)){

            $title = "您已经绑定过了哦~";
            $description = "点我去看操作帮助吧";
            $pic_url = "http://cdn-img.easyicon.net/png/11834/1183400.gif";
            $url = "";

            $tuwenList = array();
            $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>$url);
            $item = array();
            foreach($tuwenList as $tuwen){
                $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
            }
            return  $item;
            exit;
         }else{

            $sql = "SELECT * FROM nwnu_stid WHERE jwid = '{$jwid}'";
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);

            if(empty($row)){

                $title = "抱歉，小助手遇到一点问题……";
                $description = "请联系小分队解决这个问题吧";
                $pic_url = "http://cdn-img.easyicon.net/png/11818/1181827.gif";
                $tuwenList = array();
                $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
                $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>'');
                $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
                return  $item;
                exit;

            }else{

                $person_id_sql = $row['person_id'];
                if ($person_id == $person_id_sql){

                    $sql = "UPDATE nwnu_stid SET openid = '{$openid}' WHERE jwid = '{$jwid}'";

                    if(!mysql_query($sql)){

                        $title = "sorry!小助手遇到一点问题……";
                        $description = "请联系小分队解决这个问题吧";
                        $pic_url = "http://cdn-img.easyicon.net/png/11818/1181827.gif";

                        $tuwenList = array();
                        $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
                        $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>'');
                        $item = array();
                        foreach($tuwenList as $tuwen){
                            $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                        }
                        return  $item;
                        exit;

                    }else{

                        $title = "绑定成功！";
                        $description = "点击下方菜单或者回复相应指令查询吧！";
                        $pic_url = "http://cdn-img.easyicon.net/png/11755/1175557.gif";

                        $tuwenList = array();
                        $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
                        $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>'');
                        $item = array();
                        foreach($tuwenList as $tuwen){
                            $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                        }
                        return  $item;
                        exit;

                    }
                }else{

                    $title = "您回复的学号或身份证号码有误！";
                    $description = "请检查后重新回复。";
                    $pic_url = "http://cdn-img.easyicon.net/png/11818/1181827.gif";

                    $tuwenList = array();
                    $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
                    $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>'');
                    $item = array();
                    foreach($tuwenList as $tuwen){
                        $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'],$tuwen['url']);
                    }
                    return  $item;                 

                }
            }
         }
    }
    /*
    *   $param
    *   取消绑定
    */
    private static function bindOff($openid){

        self::getSql();
        $sql = "SELECT * FROM nwnu_stid WHERE openid ='{$openid}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);

        if(empty($row)){

            $title = "悄悄告诉你哦，你都没有绑定过哎~";
            $description = "点我去看操作帮助吧";
            $pic_url = "http://cdn-img.easyicon.net/png/11834/1183400.gif";
            $url = "";

            $tuwenList = array();
            $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
            $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>$url);
            $item = array();
            foreach($tuwenList as $tuwen){
                $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
            }
            return  $item;
            exit;
        }else{

            $jwid = $row['jwid'];
            $sql = "UPDATE nwnu_stid SET openid = null WHERE jwid = '{$jwid}'";

            if(!mysql_query($sql)){

                $title = "sorry!取消绑定失败！";
                $description = "请联系小分队解决这个问题吧";
                $pic_url = "http://cdn-img.easyicon.net/png/11818/1181827.gif";

                $tuwenList = array();
                $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
                $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>'');
                $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
                return  $item;
                exit;

            }else{

                $title = "取消绑定成功！";
                $description = "下次查询记得回来哦~！";
                $pic_url = "http://cdn-img.easyicon.net/png/11755/1175557.gif";

                $tuwenList = array();
                $tuwenList[] = array('title'=>$title, 'description'=>'', 'pic_url'=>'', 'url'=>'');
                $tuwenList[] = array('title'=>$description, 'description'=>'', 'pic_url'=>$pic_url, 'url'=>'');
                $item = array();
                foreach($tuwenList as $tuwen){
                    $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
                }
                return  $item;               

            }
        }   
    }       

    /*
    *   查询成绩
    *   @param $jwid
    *
    */
    private static function grade($openid){
        
        self::getSql();
        
        $grade = "";
        $stid = self::isUser($openid);
        if(is_array($stid)){
            return  $stid;
            exit;
        }else{            
            $url_cj = 'http://jw3.nwnu.edu.cn:7001/WebEducation/studentresultservlet?action=A&ontop=N&stid='.$stid;
            $request_cj = \LaneWeChat\Core\Curl::callWebServer($url_cj,'','get',false);
            //$request_cj = file_get_contents($url_cj);
            $request_cj = mb_convert_encoding($request_cj, "utf-8", "gb2312");
            $str_cj = self::get_td_array($request_cj);
           
            foreach ($str_cj as $value) {
                if($value[4]) {
                  
                    $grade .= "{$value[2]}----{$value[9]}\n";
                }
            }
        }

        if(empty($grade)){
            $grade = "抱歉，由于网络或教务系统原因，小助手没有查询成功，请稍后再试。";
        }

        $title = "成绩查询";
        $description = $grade."-------------------------\n"."点击我就能查看详细信息哦";
        //网页版链接
        $url = "http://tsdesign.hk/wechat/index.php?openid=".$openid;
        $tuwenList = array();
        $tuwenList[] = array('title'=>$title, 'description'=>$description, 'pic_url'=>$pic_url, 'url'=>$url);
        $item = array();
        foreach($tuwenList as $tuwen){
            $item[] = ResponsePassive::newsItem($tuwen['title'], $tuwen['description'], $tuwen['pic_url'], $tuwen['url']);
        }
        return  $item;

    }
    /*
    *   @param $table
    *   匹配成绩等表格
    */
    private static function get_td_array($table){

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
    /*
    *   @param $table
    *   匹配课表，由于教师姓名加了<>,所以注释过来html标签行
    */

    private static function get_kb_array($table){
        $table = preg_replace("/<table[^>]*?>/is","",$table);
        $table = preg_replace("/<tr[^>]*?>/si","",$table);
        $table = preg_replace("/<td[^>]*?>/si","",$table);
        $table = str_replace("</tr>","{tr}",$table);
        $table = str_replace("</td>","{td}",$table);
        $table = str_replace("</*/*br*/*/><br>","\n\n",$table);
        $table = str_replace("<br>","\n",$table);
        //去掉 HTML 标记
        /*$table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);*/
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

    private static function getSql(){
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
