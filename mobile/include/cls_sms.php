<?php

/**
 * ECSHOP 短信模块 之 模型（类库）
 * ============================================================================
 * 版权所有 2014 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: Jack $
 * $Id: cls_sms.php 17155 2014-02-06 06:29:05Z $
 */
if (!defined('IN_ECTOUCH')) {
    die('Hacking attempt');
}

/* 短信模块主类 */

class sms {

    var $sms_name = NULL; //用户名
    var $sms_password = NULL; //密码
    
    function __construct() {
        $this->sms();
    }

    function sms() {
        /* 由于要包含init.php，所以这两个对象一定是存在的，因此直接赋值 */
        $this->sms_name = $GLOBALS['_CFG']['sms_ecmoban_user'];
        $this->sms_password = $GLOBALS['_CFG']['sms_ecmoban_password'];
    }

    // 发送短消息
    function send($phones, $msg, &$sms_error = array()) {
        /* 检查发送信息的合法性 */
        $contents = $this->get_contents($phones, $msg);
        if (!$contents) {
            return false;
        }

        /* 获取API URL */
        $$url = "http://dxhttp.c123.cn/tx/";

        if (count($contents) > 1) {
            foreach ($contents as $key => $val) {
               // $post_data = "uid=" . $this->sms_name . "&pwd=" . md5($this->sms_password) . "&mobile=" . $val['phones'] . "&content=" . rawurlencode($val['content']); //密码可以使用明文密码或使用32位MD5加密
				$data = array
					(
					'uid'=>$this->sms_name,					//用户账号
					'pwd'=>strtolower(md5($this->sms_password)),	//MD5位32密码
					'mobile'=>$val['phones'],				//号码
					'content'=>iconv('gbk','utf-8',$val['content']),		    //如果页面是gbk编码，则转成utf-8编码，如果是页面是utf-8编码，则不需要转码
					//'time'=>$time,		//定时发送
					//'mid'=>$mid						//子扩展号
					);
                $gets = $this->Post($data, $url);

                sleep(1);
            }
        } else {
           // $post_data = "uid=" . $this->sms_name . "&pwd=" . md5($this->sms_password) . "&mobile=" . $contents[0]['phones'] . "&content=" . rawurlencode($contents[0]['content']); //密码可以使用明文密码或使用32位MD5加密
			$data = array
			(
					'uid'=>$this->sms_name,					//用户账号
					'pwd'=>strtolower(md5($this->sms_password)),	//MD5位32密码
					'mobile'=>$val['phones'],				//号码
					'content'=>iconv('gbk','utf-8',$val['content']),		    //如果页面是gbk编码，则转成utf-8编码，如果是页面是utf-8编码，则不需要转码
					//'time'=>$time,		//定时发送
					//'mid'=>$mid						//子扩展号
			);
            $gets = $this->Post($data, $url);

        }

        //print_r($gets);exit; //开启调试模式
        if if(trim(gets) == '100' ) {
            return true;
        } else {
            $sms_error = $gets['SubmitResult']['msg'];
            return false;
        }
    }

function postSMS($url,$data='')
{
	$row = parse_url($url);
	$host = $row['host'];
	$port = $row['port'] ? $row['port']:80;
	$file = $row['path'];
	while (list($k,$v) = each($data)) 
	{
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$post = substr( $post , 0 , -1 );
	$len = strlen($post);
	$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
	if (!$fp) {
		return "$errstr ($errno)\n";
	} else {
		$receive = '';
		$out = "POST $file HTTP/1.0\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-type: application/x-www-form-urlencoded\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Content-Length: $len\r\n\r\n";
		$out .= $post;		
		fwrite($fp, $out);
		while (!feof($fp)) {
			$receive .= fgets($fp, 128);
		}
		fclose($fp);
		$receive = explode("\r\n\r\n",$receive);
		unset($receive[0]);
		return implode("",$receive);
	}
}

    //检查手机号和发送的内容并生成生成短信队列
    function get_contents($phones, $msg) {
        if (empty($phones) || empty($msg)) {
            return false;
        }
        //$msg.='【'. $GLOBALS['_CFG']['shop_name'].'】'; //by wanganlin delete
        $phone_key = 0;
        $i = 0;
        $phones = explode(',', $phones);
        foreach ($phones as $key => $value) {
            if ($i < 200) {
                $i++;
            } else {
                $i = 0;
                $phone_key++;
            }
            if ($this->is_moblie($value)) {
                $phone[$phone_key][] = $value;
            } else {
                $i--;
            }
        }
        if (!empty($phone)) {
            foreach ($phone as $phone_key => $val) {
                if (EC_CHARSET != 'utf-8') {
                    $phone_array[$phone_key]['phones'] = implode(',', $val);
                    $phone_array[$phone_key]['content'] = $this->auto_charset($msg);
                } else {
                    $phone_array[$phone_key]['phones'] = implode(',', $val);
                    $phone_array[$phone_key]['content'] = $msg;
                }
            }
            return $phone_array;
        } else {
            return false;
        }
    }

    // 自动转换字符集 支持数组转换
    function auto_charset($fContents, $from = 'gbk', $to = 'utf-8') {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
            //如果编码相同或者非字符串标量则不转换
            return $fContents;
        }
        if (is_string($fContents)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($fContents, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $fContents);
            } else {
                return $fContents;
            }
        } elseif (is_array($fContents)) {
            foreach ($fContents as $key => $val) {
                $_key = auto_charset($key, $from, $to);
                $fContents[$_key] = auto_charset($val, $from, $to);
                if ($key != $_key)
                    unset($fContents[$key]);
            }
            return $fContents;
        }
        else {
            return $fContents;
        }
    }

    // 检测手机号码是否正确
    function is_moblie($moblie) {
        return preg_match("/^0?1((3|8)[0-9]|5[0-35-9]|4[57])\d{8}$/", $moblie);
    }

}

?>