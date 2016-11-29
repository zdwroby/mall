<?php

/**
 * 拼团商品前台文件
 * $Author: RINCE 120029121  $
 * $Id: pintuan.php 17217 2016-01-11 06:29:08Z RINCE 120029121  $
 */

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
require_once(ROOT_PATH . 'include/prince/lib_common.php');
require(ROOT_PATH . 'include/lib_weixintong.php');
$user_id = $wechat->get_userid();
echo update_pintuan_info();
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/*------------------------------------------------------ */
//-- act 操作项的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 拼团商品 --> 拼团活动商品列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{    
    
    /* 取得拼团活动总数 */
    $count = pintuan_count();
    if ($count > 0)
    {
        /* 取得每页记录数 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 计算总页数 */
        $page_count = ceil($count / $size);

        /* 取得当前页 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 缓存id：语言 - 每页记录数 - 当前页 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 缓存id：语言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('pintuan_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的拼团活动 */
            $pt_list = pintuan_list($size, $page);
            $smarty->assign('pt_list',  $pt_list);
            // print_r( $pt_list );
            /* 设置分页链接 */
            $pager = get_pager('pintuan.php', array('act' => 'list'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置


        assign_dynamic('pintuan_list');
    }

    /* 显示模板 */
    $smarty->display('pintuan_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 拼团商品 --> 拼团活动商品列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'asynclist')
{
    /* 取得拼团活动总数 */
    $count = pintuan_count();
    if ($count > 0)
    {
        /* 取得每页记录数 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 计算总页数 */
        $page_count = ceil($count / $size);

        /* 取得当前页 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 缓存id：语言 - 每页记录数 - 当前页 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 缓存id：语言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

     /*
     * 异步显示商品列表 by wang
     */
    if ($_GET['act'] == 'asynclist') {
        $asyn_last = intval($_POST['last']) + 1;
        $size = $_POST['amount'];
        $page = ($asyn_last > 0) ? ceil($asyn_last / $size) : 1;
    }
    $goodslist = pintuan_list($size, $page);
    $sayList = array();
    if (is_array($goodslist)) {
        foreach ($goodslist as $vo) {
			
			//PRINCE 120029121
			if(strpos($vo['goods_thumb'],'ttp')>0){
				$img_url=$vo['goods_thumb'];
			}else{
				$img_url=$config['site_url'] . $vo['goods_thumb'];
			}
			//PRINCE 120029121
			if($vo['ladder_amount']>1){
				$s1="起";
				$s2="最低";
			}else{
				$s1="团";
			}
			
            $sayList[] = array(
                'pro-inner' => '
        <div class="proImg-wrap"> <a href="' . $vo['url'] . '" > <img src="' . $img_url . '" alt="' . $vo['goods_name'] . '"> </a> 

                                <span class="tuan_mark tuan_mark2">
                                	<b>' . $vo['discount'] . '折</b>
                                    <span>' . $vo['lowest_amount'] ."人".$s1.'</span>
                                </span>
		
		</div>
        <div class="proInfo-wrap"> <a href="' . $vo['url'] . '" >
          <div class="proTitle">' . $vo['act_name'] . '</div>
          <div class="ptPrice">
             <div class="ptPrice1">
			 <em >' . $vo['lowest_amount'] ."人".$s1."&nbsp;&nbsp;&nbsp;&nbsp;".$s2. $vo['lowest_price'].'/件</em>
			 <em style="float:right;font-size:15px;" >去开团></em>
			 </div> 
          </div>
          <br /><div  class="mkPrice" >
            <em>市场价：</em> 
            <del >' . "¥".$vo['market_price'] . '</del> 
            <em>销量：'.$vo['sold'].'</em> 
          </div></a> 
        </div>'
            );
        }
    }
   //  print_r( $goodslist  );
    echo json_encode($sayList);
    exit;
    /*
     * 异步显示商品列表 by wang end
     */

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('pintuan_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的拼团活动 */
            $pt_list = pintuan_list($size, $page);
            $smarty->assign('pt_list',  $pt_list);

            /* 设置分页链接 */
            $pager = get_pager('pintuan.php', array('act' => 'list'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        $smarty->assign('categories', get_categories_tree()); // 分类树
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        $smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typepintuan.xml" : 'feed.php?type=pintuan'); // RSS URL

        assign_dynamic('pintuan_list');
    }

    /* 显示模板 */
    $smarty->display('pintuan_list.dwt', $cache_id);
}



/*------------------------------------------------------ */
//-- 用户拼团列表 --> 用户拼团列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'userlist')
{    
    
    /* 取得拼团活动总数 */
    $count = user_pintuan_count();
    if ($count > 0)
    {
        /* 取得每页记录数 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 计算总页数 */
        $page_count = ceil($count / $size);

        /* 取得当前页 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 缓存id：语言 - 每页记录数 - 当前页 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 缓存id：语言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('pintuan_user_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的拼团活动 */
            $pt_user_list = pintuan_user_list($size, $page);
            $smarty->assign('pt_user_list',  $pt_user_list);

            /* 设置分页链接 */
            $pager = get_pager('pintuan.php', array('act' => 'userlist'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置


        assign_dynamic('pintuan_user_list');
    }

    /* 显示模板 */
    $smarty->display('pintuan_user_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 用户拼团列表 --> 用户拼团列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'asyncuserlist')
{
    /* 取得拼团活动总数 */
    $count = user_pintuan_count();
    if ($count > 0)
    {
        /* 取得每页记录数 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 计算总页数 */
        $page_count = ceil($count / $size);

        /* 取得当前页 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 缓存id：语言 - 每页记录数 - 当前页 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 缓存id：语言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

     /*
     * 异步显示商品列表 by wang
     */
    if ($_GET['act'] == 'asyncuserlist') {
        $asyn_last = intval($_POST['last']) + 1;
        $size = $_POST['amount'];
        $page = ($asyn_last > 0) ? ceil($asyn_last / $size) : 1;
    }
    $goodslist = pintuan_user_list($size, $page);
    $sayList = array();
    if (is_array($goodslist)) {
        foreach ($goodslist as $vo) {
			
			//PRINCE 120029121
			if(strpos($vo['goods_thumb'],'ttp')>0){
				$img_url=$vo['goods_thumb'];
			}else{
				$img_url=$config['site_url'] . $vo['goods_thumb'];
			}
			
			if($vo['status']==1){
				$status="拼团成功";
			}elseif($vo['status']==2){
				$status="拼团失败";
			}else{
				$status="拼团进行中";
			}
			$vo['url']='pintuan.php?act=pt_view&pt_id=' . $vo['pt_id'];
            $sayList[] = array(
                'pro-inner' => '<div>
        <div class="proImg-wrap" > <a href="' . $vo['url'] . '" > <img src="' . $img_url . '" alt="' . $vo['goods_name'] . '" > </a> 		
		</div>
        <table><tr><td><div class="ptInfo-wrap"> <a href="' . $vo['url'] . '" >
          <div class="proTitle" style="font-size:12px;" >' . $vo['act_name'] . '</div>
          <div class="ptPrice">
            <em >' . $vo['need_people'] ."人团&nbsp;&nbsp;".$vo['price'].'/件</em> 
          </div></a> 
		</div></td></tr></table>
		</div>',
                'pro-pt_inner' => '
		  <div class="pt_status" >' . $status . '</div>	
		  <div class="pt_actions" ><a href="user.php?act=order_detail&order_id=' . $vo['order_id'] . '">查看订单</a></div>
		  <div class="pt_actions" ><a href="' . $vo['url'] . '">拼团详情</a></div>	'
            );
        }
    }
   //  print_r( $goodslist  );
    echo json_encode($sayList);
    exit;
    /*
     * 异步显示商品列表 by wang end
     */

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('pintuan_user_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的拼团活动 */
            $pt_user_list = pintuan_user_list($size, $page);
            $smarty->assign('pt_user_list',  $pt_user_list);
            // print_r( $pt_user_list );
            /* 设置分页链接 */
            $pager = get_pager('pintuan.php', array('act' => 'userlist'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        $smarty->assign('categories', get_categories_tree()); // 分类树
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        $smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typepintuan.xml" : 'feed.php?type=pintuan'); // RSS URL

        assign_dynamic('pintuan_user_list');
    }

    /* 显示模板 */
    $smarty->display('pintuan_user_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 拼团商品 --> 商品详情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 取得参数：拼团活动id */
    $pintuan_id = isset($_REQUEST['act_id']) ? intval($_REQUEST['act_id']) : 0;
	$userid=$_SESSION['user_id']?$_SESSION['user_id']:0;

	if(empty($_SESSION['user_id']) || $_SESSION['user_id']==0){
		$continue_url="http://".$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		$sql = "UPDATE `wxch_user`  SET continue_url = '". $continue_url . "'".
			   " WHERE wxid = '" . $_SESSION['wechat_id'] . "'";
		$db->query($sql);
	}
	
    if ($pintuan_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得拼团活动信息 */
    $pintuan = pintuan_info($pintuan_id);

    if (empty($pintuan))
    {
        ecs_header("Location: ./\n");
        exit;
    }


    /* 缓存id：语言，拼团活动id，状态，（如果是进行中）当前数量和是否登录 */
    $cache_id = $_CFG['lang'] . '-' . $pintuan_id . '-' . $pintuan['status'];
    if ($pintuan['status'] == GBS_UNDER_WAY)
    {
        $cache_id = $cache_id . '-' . $pintuan['valid_goods'] . '-' . intval($_SESSION['user_id'] > 0);
    }
    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('pintuan_goods.dwt', $cache_id))
    {
        $pintuan['gmt_end_date'] = $pintuan['end_date'];
        $pintuan['need_people'] = get_lowest_amount( $pintuan['price_ladder'])-1;

        /* 取得拼团商品信息 */
        $goods_id = $pintuan['goods_id'];
        $goods = goods_info($goods_id);
        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        $pintuan['virtual_sold'] =$pintuan['virtual_sold']+$goods['sales_count'] ;
        $pintuan['share_url'] ="pintuan.php?act=view&act_id=".$pintuan_id."&u=".$userid;
        $pintuan['share_img'] =$pintuan['share_img']?$pintuan['share_img']:$goods['goods_thumb'];

        $smarty->assign('pintuan', $pintuan);
        $smarty->assign('pictures',            get_goods_gallery($goods_id));                    // 商品相册

        $affiliate_u=isset($_REQUEST['u']) ? intval($_REQUEST['u']) : 0;
	    $qr_path = $GLOBALS['db']->getOne("SELECT qr_path FROM wxch_qr_tianxin100 WHERE scene_id = '$affiliate_u'");
        $smarty->assign('qr_path',    $qr_path);  // 图片二维码

		require_once "wxjs/jssdk.php";
		$ret = $db->getRow("SELECT  *  FROM `wxch_config`");
		$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
		$signPackage = $jssdk->GetSignPackage();
		$smarty->assign('signPackage',  $signPackage);
		
		
        $goods['url'] = build_uri('goods', array('gid' => $goods_id), $goods['goods_name']);
        $smarty->assign('pt_goods', $goods);
        $smarty->assign('userid', $_SESSION['user_id']?$_SESSION['user_id']:0);
		
        $smarty->assign('new_pintuan', get_new_pintuan($pintuan_id));


        /* 取得商品的规格 */
        $properties = get_goods_properties($goods_id);
        $smarty->assign('specification', $properties['spe']); // 商品规格
		
        /* 提示 */	
		if($_SESSION['pt_tips'] ){
            $smarty->assign('tips', $_SESSION['pt_tips']);  
			 unset($_SESSION['pt_tips']);
		}

        //模板赋值
       // print_r( $_CFG['show_goodssn'] );
        $smarty->assign('cfg', $_CFG);
        assign_template();

        $position = assign_ur_here(0, $goods['goods_name']);
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置


        assign_dynamic('pintuan_goods');
    }

    //更新商品点击次数
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $pintuan['goods_id'] . "'";
    $db->query($sql);

    $smarty->assign('now_time',  gmtime());           // 当前系统时间
    $smarty->display('pintuan_goods.dwt', $cache_id);
}


elseif ($_REQUEST['act'] == 'pt_view')
{
    /* 取得参数：拼团活动id */
    $pintuan_id = isset($_REQUEST['pt_id']) ? intval($_REQUEST['pt_id']) : 0;
	$userid=$_SESSION['user_id']?$_SESSION['user_id']:0;

	if(empty($_SESSION['user_id']) || $_SESSION['user_id']==0){
		$continue_url="http://".$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		$sql = "UPDATE `wxch_user`  SET continue_url = '". $continue_url . "'".
			   " WHERE wxid = '" . $_SESSION['wechat_id'] . "'";
		$db->query($sql);
	}
	
    if ($pintuan_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得拼团活动信息 */
    $pintuan = pintuan_detail_info($pintuan_id);

    if (empty($pintuan))
    {
        ecs_header("Location: ./\n");
        exit;
    }


    /* 缓存id：语言，拼团活动id，状态，（如果是进行中）当前数量和是否登录 */
    $cache_id = $_CFG['lang'] . '-' . $pintuan_id . '-' . $pintuan['status'];
    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('pintuan_view.dwt', $cache_id))
    {

        $pintuan['share_url'] ="pintuan.php?act=pt_view&pt_id=".$pintuan_id."&u=".$userid;
        $pintuan['share_img'] =$pintuan['share_img']?$pintuan['share_img']:$pintuan['goods_thumb'];
        $smarty->assign('pintuan', $pintuan);
		
		require_once "wxjs/jssdk.php";
		$ret = $db->getRow("SELECT  *  FROM `wxch_config`");
		$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
		$signPackage = $jssdk->GetSignPackage();
		$smarty->assign('signPackage',  $signPackage);
		
        $smarty->assign('userid', $_SESSION['user_id']?$_SESSION['user_id']:0);
		
       $sql = "SELECT pto.*,o.order_status,o.shipping_status,o.pay_status " .
            "FROM  " . $GLOBALS['ecs']->table('pintuan_orders') . " AS pto  " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON pto.order_id    = o.order_id    " .
            "WHERE pto.pt_id=".$pintuan_id." and pto.follow_user=".$userid." ";
       $my_pintuan = $GLOBALS['db']->getRow($sql);

        /*中间按钮*/
	    if($pintuan['create_succeed'] == 0){
			 $center_action="正在开团";
	    }else{
			 $center_action="分享活动";
			 $center_click=1;
		}
		$smarty->assign('center_action', $center_action);    // 中间按钮
		$smarty->assign('center_click', $center_click);    // 中间按钮
		
		
        /*右边按钮*/
	    if($pintuan['status'] == 1  || $pintuan['status'] == 2){
			 $right_action='发起拼团';
			 $right_url="pintuan.php";
		}else{ 
			 if(empty($my_pintuan)){
				 $right_action="立即参团";
				 $right_click=1;
			 }elseif($my_pintuan['pay_status']==0 && $my_pintuan['order_status']<=1){
				 $right_action="立即付款";
				 $right_url="user.php?act=order_detail&order_id=".$my_pintuan['order_id'];
			 }elseif($my_pintuan['order_id']){
				 $right_action="查看订单";
				 $right_url="user.php?act=order_detail&order_id=".$my_pintuan['order_id'];
			 }else{
				 $right_action="更多拼团";
				 $right_url="pintuan.php";
			 }
		}
        $smarty->assign('right_action', $right_action);  // 右边按钮
        $smarty->assign('right_url', $right_url);   // 右边按钮
		$smarty->assign('right_click', $right_click);    // 中间按钮

        $affiliate_u=isset($_REQUEST['u']) ? intval($_REQUEST['u']) : 0;
	    $qr_path = $GLOBALS['db']->getOne("SELECT qr_path FROM wxch_qr_tianxin100 WHERE scene_id = '$affiliate_u'");
        $smarty->assign('qr_path',    $qr_path);  // 图片二维码
		
        $sql = "SELECT pto.*,o.order_status,o.shipping_status,o.pay_status " .
            "FROM  " . $GLOBALS['ecs']->table('pintuan_orders') . " AS pto  " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON pto.order_id    = o.order_id    " .
            "WHERE pto.pt_id=".$pintuan_id." and pay_status=2 ";
	    $pintuan_orders = $GLOBALS['db']->getAll($sql);
        $smarty->assign('pintuan_orders', $pintuan_orders);

        //模板赋值
        $smarty->assign('cfg', $_CFG);
        assign_template();

        $position = assign_ur_here(0, $pintuan['act_name']);
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置


        assign_dynamic('pintuan_view');
    }

    $smarty->assign('now_time',  gmtime());           // 当前系统时间
    $smarty->display('pintuan_view.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 拼团商品 --> 购买
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'buy')
{
    /* 查询：判断是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        show_message('您还未登陆，不能参团', "马上登陆", 'user.php', 'error');
    }

    /* 查询：取得参数：拼团活动id */
    $pintuan_id = isset($_POST['act_id']) ? intval($_POST['act_id']) : 0;
    $pintuan_level = isset($_POST['level']) ? intval($_POST['level']) : 0;
    $pt_id = isset($_POST['pt_id']) ? intval($_POST['pt_id']) : 0;

    if ($pintuan_id <= 0 && $pintuan_level <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：取得数量 */
    $number = isset($_POST['number']) ? intval($_POST['number']) : 1;

    /* 查询：取得拼团活动信息 */
    $pintuan = pintuan_info($pintuan_id, $pintuan_level);
    if (empty($pintuan))
    {
        ecs_header("Location: ./\n");
        exit;
    }
	
     //拼团个数限制
	 if($pt_id ==0 && $pintuan_level!=0){
			$sql = "select count(*) from ". $GLOBALS['ecs']->table('pintuan') . " where status=0 and act_id=".$pintuan_id. " and user_id =".$_SESSION['user_id'];
			$total =$GLOBALS['db']->getOne($sql);
			if ($total>=$pintuan['open_limit']  && $pintuan['open_limit']!=0){    
				$_SESSION['pt_tips']="抱歉！您已经有 ".$total." 个进行中的拼团。 暂时不能继续发起拼团，快快点击左下方\"我的拼团\"把您的拼团分享给好友吧。";
				ecs_header("Location: pintuan.php?act=view&act_id=$pintuan_id\n");
			}
	 }

    /* 查询：检查拼团活动是否是进行中 */
    if ($pintuan['status'] != GBS_UNDER_WAY)
    {
        show_message($_LANG['gb_error_status'], '', '', 'error');
    }

    /* 查询：取得拼团商品信息 */
    $goods = goods_info($pintuan['goods_id']);
    if (empty($goods))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：判断数量是否足够 */
    if (($pintuan['restrict_amount'] > 0 && $number > ($pintuan['restrict_amount'] - $pintuan['valid_goods'])) || $number > $goods['goods_number'])
    {
        show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
    }

    /* 查询：取得规格 */
    $specs = '';
    foreach ($_POST as $key => $value)
    {
        if (strpos($key, 'spec_') !== false)
        {
            $specs .= ',' . intval($value);
        }
    }
    $specs = trim($specs, ',');

    /* 查询：如果商品有规格则取规格商品信息 配件除外 */
    if ($specs)
    {
        $_specs = explode(',', $specs);
        $product_info = get_products_info($goods['goods_id'], $_specs);
    }

    empty($product_info) ? $product_info = array('product_number' => 0, 'product_id' => 0) : '';

    /* 查询：判断指定规格的货品数量是否足够 */
    if ($specs && $number > $product_info['product_number'])
    {
        show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
    }

    /* 查询：查询规格名称和值，不考虑价格 */
    $attr_list = array();
    $sql = "SELECT a.attr_name, g.attr_value " .
            "FROM " . $ecs->table('goods_attr') . " AS g, " .
                $ecs->table('attribute') . " AS a " .
            "WHERE g.attr_id = a.attr_id " .
            "AND g.goods_attr_id " . db_create_in($specs);
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
    }
    $goods_attr = join(chr(13) . chr(10), $attr_list);

    /* 更新：清空购物车中所有拼团商品 */
    include_once(ROOT_PATH . 'include/lib_order.php');
    clear_cart(CART_PINTUAN_GOODS);

    /* 更新：加入购物车 */
    foreach ($pintuan['org_price_ladder'] as $item)
    {   
        if ($pintuan_level == $item['amount'])
        {
            $cur_price = $item['price'];
        }
    }
    $pintuan['cur_price'] = $cur_price;
	
    $price =  $pintuan_level>1?$pintuan['cur_price']:$pintuan['single_buy_price'];
	
	//获取该拼团信息
    $sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('pintuan').
            "  WHERE  pt_id=".$pt_id;
    $get_ptinfo =$GLOBALS['db']->getRow($sql);
	
    $goods_price =  (!empty($get_ptinfo['price']))?$get_ptinfo['price']:$price;

    $cart = array(
        'user_id'        => $_SESSION['user_id'],
        'session_id'     => SESS_ID,
        'goods_id'       => $pintuan['goods_id'],
        'product_id'     => $product_info['product_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => $pintuan['act_name']?$pintuan['act_name']:addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $goods_price,
        'goods_number'   => $number,
        'goods_attr'     => addslashes($goods_attr),
        'goods_attr_id'  => $specs,
        'is_real'        => $goods['is_real'],
        'extension_code' => addslashes($goods['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_PINTUAN_GOODS,
        'is_gift'        => 0,
		'fengcheng'      =>$goods['fengcheng']);
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');
	


	//获取用户昵称、头像
    $sql = "SELECT u.*,w.nickname,w.headimgurl FROM  " . $GLOBALS['ecs']->table('users') . " u ".
            "left join  wxch_user w on u.user_name=w.uname ".
            "WHERE  u.user_id=".$_SESSION['user_id'];
    $getinfo =$GLOBALS['db']->getRow($sql);
	

    /* 更新：记录购物流程类型：拼团 */
	if($pintuan_level>1){
		$_SESSION['flow_type'] = CART_PINTUAN_GOODS;
		$_SESSION['extension_code'] = 'pintuan';
		$_SESSION['extension_id'] = $pintuan_id;
		$_SESSION['pintuan_level'] = $pintuan_level;
		$_SESSION['pintuan_price'] =$goods_price;
		$_SESSION['pintuan_pt_id'] = $pt_id;
		$_SESSION['pintuan_nickname'] = $getinfo['nickname']?$getinfo['nickname']:$getinfo['user_name'];
		$_SESSION['pintuan_headimgurl'] =$getinfo['headimgurl'];
		$_SESSION['pintuan_time_limit'] =$pintuan['time_limit'];
		$_SESSION['pintuan_act_user']=$get_ptinfo['user_id']?$get_ptinfo['user_id']:$_SESSION['user_id'];
		/* 进入收货人页面 */
		ecs_header("Location: ./flow.php?step=consignee\n");
		exit;
	}else{
		/* 进入购物车页面 */
		$_SESSION['flow_type'] = CART_PINTUAN_GOODS;
		ecs_header("Location: ./flow.php?step=consignee\n");
		exit;
	}


}
/**
 * 拼团商品加入购物车
 */ 
elseif ($_REQUEST['act'] == 'buy_to_cart') {
    
        
   /* 查询：判断是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
         $result['error'] = 8; // 没有登录 
         $result['goods_id'] = $goods->goods_id;
         $result['parent'] = $goods->parent;
         $result['message'] = $spe_array;
         die($json->encode($result));
    }
    include_once('include/cls_json.php');
    $_POST['goods'] = strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    if (!empty($_REQUEST['goods_id']) && empty($_POST['goods'])) {
        if (!is_numeric($_REQUEST['goods_id']) || intval($_REQUEST['goods_id']) <= 0) {
            ecs_header("Location:./\n");
        }
        $goods_id = intval($_REQUEST['goods_id']);
        exit;
    } 
    $result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    $json = new JSON;

    if (empty($_POST['goods'])) {
        $result['error'] = 1;
        die($json->encode($result));
    }

    $goods = $json->decode($_POST['goods']);
    /* 查询：取得拼团活动信息 */
    $pintuan = pintuan_info($goods->goods_id, $goods->number);
    if (empty($pintuan))
    {
        ecs_header("Location: ./\n");
        exit;   
    }
    /* 查询：检查拼团活动是否是进行中 */
    if ($pintuan['status'] != GBS_UNDER_WAY)
    {
        $result['error'] = 10; //该活动已结束 
        $result['goods_id'] = $goods->goods_id;
        $result['parent'] = $goods->parent;
        $result['message'] = $spe_array;
        die($json->encode($result));
    }
     /* 查询：取得拼团商品信息 */
    $goods = goods_info( $pintuan['goods_id'] );
    if (empty($goods))
    {
        ecs_header("Location: ./\n");
        exit;
    }
     /* 检查：如果商品有规格，而post的数据没有规格，把商品的规格属性通过JSON传到前台 */
    if (empty($goods->spec) AND empty($goods->quick))
    {
        $sql = "SELECT a.attr_id, a.attr_name, a.attr_type, ".
            "g.goods_attr_id, g.attr_value, g.attr_price " .
        'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
        "WHERE a.attr_type != 0 AND g.goods_id = '" . $goods->goods_id . "' " .
        'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';

        $res = $GLOBALS['db']->getAll($sql);

        if (!empty($res))
        {
            $spe_arr = array();
            foreach ($res AS $row)
            {
                $spe_arr[$row['attr_id']]['attr_type'] = $row['attr_type'];
                $spe_arr[$row['attr_id']]['name']     = $row['attr_name'];
                $spe_arr[$row['attr_id']]['attr_id']     = $row['attr_id'];
                $spe_arr[$row['attr_id']]['values'][] = array(
                                                            'label'        => $row['attr_value'],
                                                            'price'        => $row['attr_price'],
                                                            'format_price' => price_format($row['attr_price'], false),
                                                            'id'           => $row['goods_attr_id']);
            }
            $i = 0;
            $spe_array = array();
            foreach ($spe_arr AS $row)
            {
                $spe_array[]=$row;
            }
            $result['error']   = ERR_NEED_SELECT_ATTR;
            $result['goods_id'] = $goods->goods_id;
            $result['parent'] = $goods->parent;
            $result['message'] = $spe_array;

            die($json->encode($result));
        }
    }

    /* 查询：如果商品有规格则取规格商品信息 配件除外 */
    if ($specs)
    {
        $_specs = explode(',', $specs);
        $product_info = get_products_info($goods['goods_id'], $_specs);
    }

    empty($product_info) ? $product_info = array('product_number' => 0, 'product_id' => 0) : '';
      /* 查询：判断指定规格的货品数量是否足够 */
    if ($specs && $number > $product_info['product_number'])
    {
        $result['error']   = ERR_NEED_SELECT_ATTR;
        $result['goods_id'] = $goods->goods_id;
        $result['parent'] = $goods->parent;
        $result['message'] = $spe_array;
        die($json->encode($result));
    }  
    
    /* 查询：查询规格名称和值，不考虑价格 */
    $attr_list = array();
    $sql = "SELECT a.attr_name, g.attr_value " .
            "FROM " . $ecs->table('goods_attr') . " AS g, " .
                $ecs->table('attribute') . " AS a " .
            "WHERE g.attr_id = a.attr_id " .
            "AND g.goods_attr_id " . db_create_in($specs);
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
    }
    $goods_attr = join(chr(13) . chr(10), $attr_list);

    /* 更新：清空购物车中所有拼团商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_PINTUAN_GOODS);
    /* 更新：加入购物车 */
	
	
	
    $goods_price = $pintuan['deposit'] > 0 ? $pintuan['deposit'] : $pintuan['cur_price'];
    $cart = array(
       'user_id'        => $_SESSION['user_id'],
       'session_id'     => SESS_ID,
       'goods_id'       => $pintuan['goods_id'],
       'product_id'     => $product_info['product_id'],
       'goods_sn'       => addslashes($goods['goods_sn']),
       'goods_name'     => addslashes($goods['goods_name']),
       'market_price'   => $goods['market_price'],
       'goods_price'    => $goods_price,
       'goods_number'   => $number,
       'goods_attr'     => addslashes($goods_attr),
       'goods_attr_id'  => $specs,
       'is_real'        => $goods['is_real'],
       'extension_code' => addslashes($goods['extension_code']),
       'parent_id'      => 0,
       'rec_type'       => CART_PINTUAN_GOODS,
       'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');
    /* 更新：记录购物流程类型：拼团 */
    $_SESSION['flow_type'] = CART_PINTUAN_GOODS;
    $_SESSION['extension_code'] = 'pintuan';
    $_SESSION['extension_id'] = $pintuan_id;
        
    $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    die($json->encode($result));
}



?>