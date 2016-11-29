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
	if($wxid){
		$query_sql = "SELECT nickname FROM wxch_user WHERE wxid = '$wxid'";
		$nickname = $db->getOne($query_sql);
	}
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
	/*店   铺   地  址：         http://           we10.taobao.     com*/
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



	$post_msg = '{
       "touser":"'.$wxid.'",
       "msgtype":"news",
       "news":{
           "articles": [
            {
                "title":"'.$w_title.'",
                "description":"'.$w_description.'",
                "url":"'.$w_url.'",
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
	
	/*ti an  x in  100    新  增   功能*/
	$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
	$num = count($affiliate['item']);
	$money=$orders['fencheng'];
	$row['user_id']=$wxch_user_id;
    for ($i=0; $i < $num; $i++)
    {	
		$wxid=0;
		$row = $db->getRow("SELECT o.parent_id as user_id,u.user_name FROM " . $GLOBALS['ecs']->table('users') . " o" .
                        " LEFT JOIN" . $GLOBALS['ecs']->table('users') . " u ON o.parent_id = u.user_id".
                        " WHERE o.user_id = '$row[user_id]'"
                    );
		$up_uid = $row['user_id'];
	   	$query_sql = "SELECT wxid FROM " . $ecs->table('users') . " WHERE user_id = '$up_uid'";
		$ret_w = $db->getRow($query_sql);
		$wxid = $ret_w['wxid'];
		$num_tianxin100=$i+1;
		if($wxch_order_name == 'pay') 
		{
			$w_title="您的".$num_tianxin100."级会员".$nickname."付款了";
		}else{
			$w_title="您的".$num_tianxin100."级会员".$nickname."下单了";
		}
		$affiliate['item'][$i]['level_money'] = (float)$affiliate['item'][$i]['level_money'];
		if ($affiliate['item'][$i]['level_money'])
        {
           $affiliate['item'][$i]['level_money'] /= 100;
        }
		$yongjin_tianxin100= round($money*$affiliate['item'][$i]['level_money'],2);
		$w_description= "订单号：".$orders['order_sn']."\r\n总金额：".$orders['order_amount']."您将获得佣金".$yongjin_tianxin100."\r\n".$pay_status;
        $wp_url = $cfg_baseurl.$cfg_murl."distribute.php?act=myorder&user_id=".$wxch_user_id."&level=".$num_tianxin100;
		$post_msg = '{
       "touser":"'.$wxid.'",
       "msgtype":"news",
       "news":{
           "articles": [
            {
                "title":"'.$w_title.'",
                "description":"'.$w_description.'",
                "url":"'.$wp_url.'",
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
	
	/*ti an  x in  100    新  增   功能*/
	
	//PRINCE新增拼团提醒 Start
	if($wxch_order_name == 'pay' && $orders['extension_code']=='pintuan'){
			$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pintuan_orders') . " WHERE order_id = '$order_id' ";
			$pintuan = $GLOBALS['db']->getRow($sql);
			$pt_id=$pintuan['pt_id'];
			$act_user=$pintuan['act_user'];
			$follow_user=$pintuan['follow_user'];
			
			$query_sql_1 = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$act_user'";
			$ret_w_1 = $GLOBALS['db']->getRow($query_sql_1);
			$act_wxid = $ret_w_1['wxid'];
		
			$query_sql_2 = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$follow_user'";
			$ret_w_2 = $GLOBALS['db']->getRow($query_sql_2);
			$follow_wxid = $ret_w_2['wxid'];

			$cfg_baseurl = $GLOBALS['db']->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'baseurl'");
			$cfg_murl = $GLOBALS['db']->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'murl'");

			$w1_url = $cfg_baseurl.$cfg_murl.'pintuan.php?act=pt_view&pt_id='.$pt_id.'&u='.$act_user;
			$w2_url = $cfg_baseurl.$cfg_murl.'pintuan.php?act=pt_view&pt_id='.$pt_id.'&u='.$follow_user;
			$w_picurl = $cfg_baseurl."mobile/images/weixin/wxch_pt.jpg";
			$w_description="快快点击进入分享给朋友们参团吧";

		    if($act_user==$follow_user){
				$w_title="成功发起拼团";
			}else{
				$w_title="成功参与拼团";
			}

			$post_msg = '{
			   "touser":"'.$follow_wxid.'",
			   "msgtype":"news",
			   "news":{
				   "articles": [
					{
						"title":"'.$w_title.'",
						"description":"'.$w_description.'",
						"url":"'.$w2_url.'",
						"picurl":"'.$w_picurl.'"
					}
					]
			   }
		    }';
			$ret_json = curl_grab_page($url, $post_msg);
			$ret = json_decode($ret_json);
			
		    if($act_user!=$follow_user){
				$w_title="有新朋友参加您的拼团啦";
				$w_description="快快点击进入分享更多朋友参团吧";
				$post_msg = '{
				   "touser":"'.$act_wxid.'",
				   "msgtype":"news",
				   "news":{
					   "articles": [
						{
							"title":"'.$w_title.'",
							"description":"'.$w_description.'",
							"url":"'.$w1_url.'",
							"picurl":"'.$w_picurl.'"
						}
						]
				   }
				}';
				$ret_json = curl_grab_page($url, $post_msg);
				$ret = json_decode($ret_json);
			}

	}
	//PRINCE新增拼团提醒  End

	
}



function get_award_scale($user_id)
{
	$sql = "select rank_points from ".$GLOBALS['ecs']->table('users')." where user_id = ".$user_id;
	$rank_points = $GLOBALS['db']->getOne($sql);
	$sql = "select award_scale,award_on from ".$GLOBALS['ecs']->table('user_rank')." where min_points <= " . intval($rank_points) . ' AND max_points > ' . intval($rank_points);
	$data = $GLOBALS['db']->getRow($sql);
	if ($data['award_on'] > 0){
		$award_scale = $data['award_scale'];
	}else {
		$award_scale = 0;
	}
	
	return $award_scale;
}
?>