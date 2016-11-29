<?php
$time = time();
if(empty($wxch_order_name)) 
{
	$wxch_order_name = 'reorder';
}
$wxch_user_id = $_SESSION['user_id'];
if(empty($wxch_user_id)){

$wxch_user_id=$order['user_id'];

}
if($wxch_user_id > 0) 
{
	$access_token = access_token($db);
	$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	$query_sql = "SELECT * FROM " . $ecs->table('users') . " WHERE user_id = '$wxch_user_id'";
	$ret_w = $db->getRow($query_sql);
	$wxid = $ret_w['wxid'];
	$nickname=$ret_w['nickname'];
	if(empty($order['order_id'])) 
	{
		$order['order_id'] = $order_id;
	}
	if(empty($order_id)) 
	{
		$order_id = $order['order_id'];
	}
	if($wxch_order_name == 'pay') 
	{
		$orders_sql = "SELECT * FROM " . $ecs->table('order_info') . " WHERE `order_id` = '$order_id'";
		$orders = $db->getRow($orders_sql);
		$order_goods = $db->getAll("SELECT * FROM " . $ecs->table('order_goods') . "  WHERE `order_id` = '$order_id'");
	}
	else 
	{
		$orders = $db->getRow("SELECT * FROM " . $ecs->table('order_info') . " WHERE `order_id` = '$order_id' ");
		$order_goods = $db->getAll("SELECT * FROM " . $ecs->table('order_goods') . "  WHERE `order_id` = '$order_id'");
	}
	$shopinfo = '';
	if(!empty($order_goods)) 
	{
		foreach($order_goods as $v) 
		{
			if(empty($v['goods_attr']))
			{
				$shopinfo .= $v['goods_name'].'('.$v['goods_number'].'),';
			}
			else
			{
				$shopinfo .= $v['goods_name'].'（'.$v['goods_attr'].'）'.'('.$v['goods_number'].'),';
			}
		}
		$shopinfo = substr($shopinfo, 0, strlen($shopinfo)-1);
	}
	$wxch_order_name="remind";
	$sql = "SELECT * FROM wxch_order WHERE order_name = '$wxch_order_name'";
	$cfg_order = $db->getRow($sql);
	$cfg_baseurl = $db->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'baseurl'");
	$cfg_murl = $db->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'murl'");
	if($orders['pay_status'] == 0) 
	{
		$pay_status = '支付状态：未付款';
	}
	elseif($orders['pay_status'] == 1) 
	{
		$pay_status = '支付状态：付款中';
	}
	elseif($orders['pay_status'] == 2) 
	{
		$pay_status = '支付状态：已付款';
	}
	$wxch_address = "\r\n收件地址：".$orders['address'];
	$wxch_consignee = "\r\n收件人：".$orders['consignee'];
	$w_title = $cfg_order['title'];
	if($orders['order_amount'] == '0.00') 
	{
		$orders['order_amount'] = $orders['surplus'];
	}
	$w_description = '订单号：'.$orders['order_sn']."\r\n".'商品信息：'.$shopinfo."\r\n总金额：".$orders['order_amount']."\r\n".$pay_status.$wxch_consignee.$wxch_address;
	$w_url = $cfg_baseurl.$cfg_murl.'user.php?act=order_detail&order_id='.$order['order_id'].'&wxid='.$wxid;
	$http_ret1 = stristr($cfg_order['image'],'http://');
	$http_ret2 = stristr($cfg_order['image'], 'http:\\');
	if($http_ret1 or $http_ret2) 
	{
		$w_picurl = $cfg_order['image'];
	}
	else 
	{
		$w_picurl = $cfg_baseurl."mobile/".$cfg_order['image'];

	}
		/*甜心100  新增 查询 管理员微信资料 */
		$sql ="select content from `wxch_order` where order_name='remind'";			
		$content=$db->getOne($sql);
		$wxid = $db->getOne("SELECT wxid FROM " . $ecs->table('users') . " WHERE user_name='".$content."'");
		/*甜心100  新增 查询 管理员微信资料 */
	$post_msg = '{
       "touser":"'.$wxid.'",
       "msgtype":"news",
       "news":{
           "articles": [
            {
                "title":"'.$w_title.'",
                "description":"'.$w_description.'",
                "url":"",
                "picurl":"'.$w_picurl.'"
            }
            ]
       }
   }';
	$ret_json = curl_grab_page($url, $post_msg);
	$ret = json_decode($ret_json);
	if($ret->errmsg != 'ok') 
	{
		$access_token = new_access_token($db);
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
		$ret_json = curl_grab_page($url, $post_msg);
		$ret = json_decode($ret_json);
	}
}
?>