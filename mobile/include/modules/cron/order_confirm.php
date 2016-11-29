<?php
if (!defined('IN_ECTOUCH')){  
    die('Hacking attempt');  
}  
require_once(ROOT_PATH . 'include/lib_common.php');
require_once(ROOT_PATH . 'include/lib_order.php');
require_once(ROOT_PATH . 'lang/zh_cn/admin/affiliate_ck.php');
$cron_lang = ROOT_PATH . 'lang/' .$GLOBALS['_CFG']['lang']. '/cron/order_confirm.php';
if (file_exists($cron_lang)) {
    global $_LANG;
    include_once($cron_lang);
}
/* 模块的基本信息 安装的时候用*/
if (isset($set_modules) && $set_modules == TRUE) {
    $i = isset($modules) ? count($modules) : 0;
    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');
    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'my_cron_desc';
    /* 作者 */
    $modules[$i]['author']  = '甜心100';
    /* 网址 */
    $modules[$i]['website'] = 'http://we10.cn';
    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';
    /* 配置信息 一般这一项通过serialize函数保存在cron表的中cron_config这个字段中*/
    $modules[$i]['config']  = array(
        array('name' => 'out_day', 'type' => 'text', 'value' => '30')
    );
    //name：计划任务的名称，type：类型(text,textarea,select…)，value：默认值
    return;
}

//下面是这个计划任务要执行的程序了
$time  = gmtime();
$out_day = empty($cron['out_day']) ? 30 : $cron['out_day'];
$out_time = $out_day*24*3600;

$sql="select * from ".$ecs->table('order_info')." where shipping_time < ($time-$out_time) and shipping_status=1";
$order=$db->getAll($sql);

foreach($order as $o){
  //$sql="update ".$ecs->table('order_info')." set shipping_status=2 where shipping_time < ($time-$out_time) and shipping_status=1 and order_id=$o[order_id]";
  //$db->query($sql);
  
  /* 标记订单为已收货 */  
  $update_status = update_order($o['order_id'], array('shipping_status' => SS_RECEIVED));
  
  /* 记录log */  
  $action_note = "计划任务：定期自动确定收货，订单号：".$o['order_sn']."，执行状态：".($update_status ? '成功' : '失败');  
  order_action($o['order_sn'], OS_CONFIRMED, SS_RECEIVED, PS_PAYED, $action_note, '系统');
  
  
  //affiliate($o['order_id']);
}
?>