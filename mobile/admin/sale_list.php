<?php
/**
 * ECSHOP 销售明细列表程序
 * ============================================================================
 * 版权所有 2005-2009 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: sale_list.php 16881 2009-12-14 09:19:16Z liubo $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'include/lib_order.php');
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' ||  $_REQUEST['act'] == 'download'))
{
    /* 检查权限 */
    check_authz_json('sale_order_stats');
    if (strstr($_REQUEST['start_date'], '-') === false)
    {
        $_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
        $_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
    }
    /*------------------------------------------------------ */
    //--Excel文件下载
    /*------------------------------------------------------ */
    if ($_REQUEST['act'] == 'download')
    {
        $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['goods_name']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['order_sn']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '商品货号') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '商品库存') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '折后价格') . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['amount']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['sell_price']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['cost_price']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['gross_profit']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '配送费'). "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['sell_date']) . "\t\n";
		
        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
           
		   echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'utf-8', '[ ' . $value['order_sn'] . ' ]') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_sn']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_number']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_amount']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_num']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_price']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['cost_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'utf-8', $value['gross_profit']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['shipping_fee']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_time']) . "\t";            
			
			echo "\n";
        }
		
		/***
    foreach ($goods_sales_list AS $key => $value)
    {
        echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_name']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', '[ ' . $value['order_sn'] . ' ]') . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_num']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_price']) . "\t";
  echo ecs_iconv(EC_CHARSET, 'utf-8', $value['cost_price']) . "\t";
  echo ecs_iconv(EC_CHARSET, 'utf-8', $value['gross_profit']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_time']) . "\t";
        echo "\n";
    }
	***/
if($total_isdisplay)
{
      echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['sale_total']) . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $sale_total) . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['cost_total']) . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $cost_total) . "\t\n";
   echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['gross_profit_total']) . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $gross_profit_total) . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['gross_profit_rate']) . "\t";
   echo ecs_iconv(EC_CHARSET, 'utf-8', $gross_profit_rate) . "\t\n";

}
        exit;
    }
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);	
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
	
	
    make_json_result($smarty->fetch('sale_list.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明细列表
/*------------------------------------------------------ */
else
{
    /* 权限判断 */
    admin_priv('sale_order_stats');
    /* 时间参数 */
    if (!isset($_REQUEST['start_date']))
    {
        $start_date = local_strtotime('-7 days');
    }
    if (!isset($_REQUEST['end_date']))
    {
        $end_date = local_strtotime('today');
    }
    
    $sale_list_data = get_sale_list();
	//var_dump( $sale_list_data["abc"]);
    /* 赋值到模板 */
	$smarty->assign('abc',      $sale_list_data["abc"][0]);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          $_LANG['sell_stats']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      $_LANG['sale_list']);
    $smarty->assign('cfg_lang',     $_CFG['lang']);

 $smarty->assign('sale_total',       $sale_list_data['sale_total']);
$smarty->assign('cost_total',     $sale_list_data['cost_total']);
$smarty->assign('gross_profit_total',$sale_list_data['gross_profit_total']);
$smarty->assign('gross_profit_rate',$sale_list_data['gross_profit_rate']);

    $smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('sale_list.htm');
}
 
/*------------------------------------------------------ */
//--获取销售明细需要的函数
/*------------------------------------------------------ */
/**
 * 取得销售明细数据信息
 * @param   bool  $is_pagination  是否分页
 * @return  array   销售明细数据
 */

function get_sale_list($is_pagination = true){

    /* 时间参数 */
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-7 days') : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : local_strtotime($_REQUEST['end_date']);
  
    /* 查询数据的条件 */
    $where = " WHERE og.order_id = oi.order_id". order_query_sql('finished', 'oi.') .
             " AND oi.add_time >= '".$filter['start_date']."' AND oi.add_time < '" . ($filter['end_date'] + 86400) . "'";
    
    $sql = "SELECT COUNT(og.goods_id) FROM " .
           $GLOBALS['ecs']->table('order_info') . ' AS oi,'.
           $GLOBALS['ecs']->table('order_goods') . ' AS og '.
           $where;
     $filter['record_count'] = $GLOBALS['db']->getOne($sql);
		  // echo $sql;
	 $sql = "select  sum(shipping_fee) as free  from ecs_order_info as oi,ecs_order_goods as og  $where" ;
	 $free_sum = $GLOBALS['db']->query($sql);// var_dump($free_sum); 	
	 $abc = array();
	 while ($items = $GLOBALS['db']->fetchRow($free_sum)){
		 $items["free"] =  price_format($items["free"]);
		$abc[] = $items;
	 }
	 
   

    /* 分页大小 */
    $filter = page_and_size($filter);

   /*** $sql = 'SELECT og.goods_id, og.goods_sn, og.goods_name, og.goods_number AS goods_num, og.goods_price '.
           'AS sales_price, oi.add_time AS sales_time, oi.order_id, oi.order_sn '.
           "FROM " . $GLOBALS['ecs']->table('order_goods')." AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi ".
           $where. " ORDER BY sales_time DESC, goods_num DESC";
***/		   
		       $sql = 'SELECT og.goods_id, og.goods_sn, og.goods_name,og.goods_number as goods_number, og.orders AS goods_num, og.goods_price '.
           'AS sales_price,og.cost_price, (og.goods_price-og.cost_price)*og.orders as gross_profit,oi.add_time AS sales_time,oi.order_id,oi.shipping_fee AS shipping_fee,oi.goods_amount AS goods_amount, oi.order_sn '.
           "FROM (SELECT og.goods_id, g.goods_number,og.goods_sn, og.goods_name, og.goods_number AS orders, g.cost_price, g.shop_price AS goods_price,og.order_id AS order_id,og.goods_price AS sales_price
from ecs_goods as g, ecs_order_goods as og where g.goods_id=og.goods_id) AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi ".
           $where. " ORDER BY sales_time DESC, goods_num DESC";
		  //echo $sql;sum(value_name)  sum(oi.shipping_fee) as sumfee, 
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }
$sale_list_data = $GLOBALS['db']->query($sql);

   // $sale_list_data = $GLOBALS['db']->getAll($sql);

 // $sale_total = $cost_total = $gross_profit_total =$gross_profit_rate = 0;  // 添加的
/**
    foreach ($sale_list_data as $key => $item)
    {
	 
        $sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
        $sale_list_data[$key]['sales_time']  = local_date($GLOBALS['_CFG']['time_format'], $sale_list_data[$key]['sales_time']);	
		 
    }
	**/
	
$sale_total = $cost_total = $gross_profit_total =$gross_profit_rate = 0;

while ($items = $GLOBALS['db']->fetchRow($sale_list_data))
{
		$sale_total += $items['sales_price'] * $items['goods_num'];
			$cost_total += $items['cost_price'] * $items['goods_num'];
			$gross_profit_total += $items['gross_profit'];
		$items['cost_price']   = price_format($items['cost_price']);
		$items['gross_profit']   = price_format($items['gross_profit']);
		$items['sales_price']   = price_format($items['sales_price']);		
		$items['sales_time']    = local_date($GLOBALS['_CFG']['time_format'], $items['sales_time']);
			$goods_sales_list[]     = $items;
}

		$total_isdisplay = false;
		if($sale_total>0)
		{
		$total_isdisplay = true;
		$gross_profit_rate = round($gross_profit_total*100/$sale_total,2).'%';   //毛利/销售收入
		$sale_total   = price_format($sale_total);
		$cost_total   = price_format($cost_total);
		$gross_profit_total   = price_format($gross_profit_total);
	
}
	//var_dump($cost_total);
    $arr = array('sale_list_data' => $goods_sales_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sale_total'=>$sale_total,'cost_total'=>$cost_total,'gross_profit_total'=>$gross_profit_total,'gross_profit_rate'=>$gross_profit_rate,'abc'=>$abc);
    //var_dump($arr);
	return $arr;

}



?>