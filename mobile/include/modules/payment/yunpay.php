<?php

/**
 * ECSHOP 云在线插件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: chinabank.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECTOUCH'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'lang/' .$GLOBALS['_CFG']['lang']. '/payment/yunpay.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'yunpay_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 支付费用 */
    $modules[$i]['pay_fee'] = '0';

    /* 作者 */
    $modules[$i]['author']  = '创优汇';

    /* 网址 */
    $modules[$i]['website'] = 'http://i2e.cn/yunpay.php';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.2';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'yunpay_partner', 'type' => 'text', 'value' => ''),
        array('name' => 'yunpay_key',     'type' => 'text', 'value' => ''),
		array('name' => 'seller_email',     'type' => 'text', 'value' => ''),
		
    );

    return;
}

/**
 * 类
 */
class yunpay
{
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function yunpay()
    {
    }

    function __construct()
    {
        $this->yunpay();
    }
	
    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
		include(ROOT_PATH."/include/modules/payment/yunpay/yun_md5.function.php");
		
		 //商户订单号
        $out_trade_no = $order['order_sn'].'@'.$order['log_id'];//商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $order['order_sn'];//必填

        //付款金额
        $total_fee = intval($order['order_amount']);//必填 需为整数

        //订单描述

        $body = $order['order_sn'];
		
		
		//服务器异步通知页面路径
        $nourl = return_url(basename(__FILE__, '.php'));
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $reurl = return_url(basename(__FILE__, '.php'));
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
       
		//商品展示地址
        $orurl = "";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，如原网站带有 参数请彩用伪静态或短网址解决

        //商品形象图片地址
        $orimg = "";
        //需http://格式的完整路径，必须为图片完整地址
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"partner" => trim($payment['yunpay_partner']),
				"seller_email"	=> $payment['seller_email'],
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $body,
				"nourl"	=> $nourl,
				"reurl"	=> $reurl,
				"orurl"	=> $orurl,
				"orimg"	=> $orimg
		);

		//建立请求
		$html_text = i2e($parameter, $GLOBALS['_LANG']['pay_button']);
		return $html_text;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        $payment        = get_payment(basename(__FILE__, '.php'));
        include(ROOT_PATH."/include/modules/payment/yunpay/yun_md5.function.php");
		
		//计算得出通知验证结果
        $yunNotify = md5Verify($_REQUEST['i1'],$_REQUEST['i2'],$_REQUEST['i3'],$payment['yunpay_key'],$payment['yunpay_partner']);
		
		//验证成功
		if($yunNotify) {
			//商户订单号
			$out_trade_no = $_REQUEST['i2'];
			//云支付交易号
			$trade_no = $_REQUEST['i4'];
			//价格
			$yunprice=$_REQUEST['i1'];
			
			$log_arr = explode('@', $out_trade_no);
			$log_id       = $log_arr[1];
			
			/* 检查支付的金额是否相符 */
			if (!check_money($log_id, $yunprice))
			{
				return false;
			}
			
			//改变订单状态
            order_paid($log_id);
            return true;
			
		}
		else
		{
			 return false;
		}
		
    }
}

?>