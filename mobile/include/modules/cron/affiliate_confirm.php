<?php
if (!defined('IN_ECTOUCH')){  
    die('Hacking attempt');  
}  
require_once(ROOT_PATH . 'include/lib_common.php');
require_once(ROOT_PATH . 'include/lib_order.php');
require_once(ROOT_PATH . 'lang/zh_cn/admin/affiliate_ck.php');
$cron_lang = ROOT_PATH . 'lang/' .$GLOBALS['_CFG']['lang']. '/cron/affiliate_confirm.php';
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
$out_day = empty($cron['out_day']) ? 10 : $cron['out_day'];
$out_time = $out_day*24*3600;

$sql="select order_id from ".$ecs->table('order_info')." where shouhuo_time < ($time-$out_time) and shipping_status=2 and is_separate=0";
$order=$db->getAll($sql);

foreach($order as $o){

  affiliate($o['order_id']);
  
}

//确认收货后，自动分成
function affiliate($oid){	
    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    empty($affiliate) && $affiliate = array();

    $separate_by = $affiliate['config']['separate_by'];

    $row = $GLOBALS['db']->getRow("SELECT o.order_sn, o.is_separate, (o.goods_amount - o.discount) AS goods_amount, o.user_id,o.fencheng FROM " . $GLOBALS['ecs']->table('order_info') . " o".
                    " LEFT JOIN " . $GLOBALS['ecs']->table('users') . " u ON o.user_id = u.user_id".
            " WHERE order_id = '$oid'");

    $order_sn = $row['order_sn'];

    if (empty($row['is_separate']))
    {
        $affiliate['config']['level_point_all'] = (float)$affiliate['config']['level_point_all'];
		
        if ($affiliate['config']['level_point_all'])
        {
            $affiliate['config']['level_point_all'] /= 100;
        }
        $money = round($row['fencheng'],2);
		
	   $tianxin100q=$row['fencheng']+1;
		if(empty($tianxin100q)){
		
			 $money=0;
		}
		if(empty($row['fencheng'])){
			
			 $money=round($row['goods_amount'],2);
		}
		 
		 
        $integral = integral_to_give(array('order_id' => $oid, 'extension_code' => ''));
	
		
        $point = round(intval($integral['rank_points']));
        $separate_personal = $affiliate['config']['ex_fenxiao_personal'];
        $level_register_up = (float)$affiliate['config']['level_register_up'];
        
        if(empty($separate_by))
        {
            //推荐注册分成
            $num = count($affiliate['item']);
            for ($i=0; $i < $num; $i++)
            {
                $affiliate['item'][$i]['level_point'] = (float)$affiliate['item'][$i]['level_point'];
                $affiliate['item'][$i]['level_money'] = (float)$affiliate['item'][$i]['level_money'];
                if ($affiliate['item'][$i]['level_point'])
                {
                    $affiliate['item'][$i]['level_point'] /= 100;
                }
                if ($affiliate['item'][$i]['level_money'])
                {
                    $affiliate['item'][$i]['level_money'] /= 100;
                }
                $setmoney = round($money * $affiliate['item'][$i]['level_money'], 2);
                $setpoint = round($point * $affiliate['item'][$i]['level_point'], 0);
                $row = $GLOBALS['db']->getRow("SELECT o.parent_id as user_id,u.user_name,o.user_name as personal_name,o.user_id as personal_id FROM " . $GLOBALS['ecs']->table('users') . " o" .
                        " LEFT JOIN" . $GLOBALS['ecs']->table('users') . " u ON o.parent_id = u.user_id".
                        " WHERE o.user_id = '$row[user_id]'"
                    );
                $up_uid = $row['user_id'];
                if (empty($up_uid) || empty($row['user_name']))
                {
                    break;
                }
                else
                {
                    /*t甜心100 新增 Mr.lu  如果上级已经不是分销商，就不分成*/
					//echo $setpoint;
					$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
					$level_register_up = (float)$affiliate['config']['level_register_up'];
                    $rank_points =  $GLOBALS['db']->getOne("SELECT rank_points FROM " . $GLOBALS['ecs']->table('users')."where user_id=".$up_uid);
                    if($rank_points>$level_register_up||$rank_points==$level_register_up){
                        $info = sprintf($_LANG['separate_info'], $order_sn, $setmoney, $setpoint);
                        log_account_change($up_uid, $setmoney, 0, $setpoint, 0, $info);
                        write_affiliate_log($oid, $up_uid, $row['user_name'], $setmoney, $setpoint, $separate_by,$separate_personal);
                    }
                    
                }
            }
            
            //个人购买增加分成
            if ($separate_personal > 0){
            	$personal_data = $GLOBALS['db']->getRow("SELECT o.user_id,u.user_name,u.rank_points FROM " . $GLOBALS['ecs']->table('order_info') . " o".
            			" LEFT JOIN " . $GLOBALS['ecs']->table('users') . " u ON o.user_id = u.user_id".
            			" WHERE order_id = '$oid'");
            	if($personal_data['rank_points'] >= $level_register_up){
            		$personalMoney = round($money * $affiliate['config']['level_money_personal']*0.01, 2);
            		$personalPoint = round($point * $affiliate['config']['level_point_personal']*0.01, 0);
            		$info = sprintf($_LANG['separate_info'], $order_sn, $personalMoney, $personalPoint);
            		log_account_change($personal_data['user_id'], $personalMoney, 0, $personalPoint, 0, $info);
            		write_affiliate_log($oid, $personal_data['user_id'] , $personal_data['user_name'], $personalMoney, $personalPoint, $separate_by,$separate_personal);
            	}
            }
        }
        else
        {
            //推荐订单分成
            $row = $db->getRow("SELECT o.parent_id, u.user_name FROM " . $GLOBALS['ecs']->table('order_info') . " o" .
                    " LEFT JOIN" . $GLOBALS['ecs']->table('users') . " u ON o.parent_id = u.user_id".
                    " WHERE o.order_id = '$oid'"
                );
            $up_uid = $row['parent_id'];
            if(!empty($up_uid) && $up_uid > 0)
            {
                $info = sprintf($_LANG['separate_info'], $order_sn, $money, $point);
                log_account_change($up_uid, $money, 0, $point, 0, $info);
                write_affiliate_log($oid, $up_uid, $row['user_name'], $money, $point, $separate_by);
              
            }
            else
            {
                $links[] = array('text' => $_LANG['affiliate_ck'], 'href' => 'affiliate_ck.php?act=list');
                sys_msg($_LANG['edit_fail'], 1 ,$links);
            }
        }
        $sql = "UPDATE " . $GLOBALS['ecs']->table('order_info') .
               " SET is_separate = 1" .
               " WHERE order_id = '$oid'";
        $GLOBALS['db']->query($sql);
    }
}
//记录分成
function write_affiliate_log($oid, $uid, $username, $money, $point, $separate_by){
    $time = gmtime();
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('affiliate_log') . "( order_id, user_id, user_name, time, money, point, separate_type)".
                                                              " VALUES ( '$oid', '$uid', '$username', '$time', '$money', '$point', $separate_by)";
    if ($oid){
      $GLOBALS['db']->query($sql);
    }
}
?>