<?php

/**
 * ECSHOP 砍价前台文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: PRINCE $
 * $Id: cut.php 17217 2016-01-10 06:29:08Z PRINCE QQ 120029121 $
 */

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
require(ROOT_PATH . 'include/lib_weixintong.php');
$user_id = $wechat->get_userid();
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/*------------------------------------------------------ */
//-- act 操作项的初始化  PRINCE QQ 120029121
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 砍价活动列表 PRINCE QQ 120029121
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得砍价活动总数 */
    $count = cut_count();

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
    if (!$smarty->is_cached('cut_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的砍价活动 */
            $cut_list = cut_list($size, $page);
            $smarty->assign('cut_list',  $cut_list);

            /* 设置分页链接 */
            $pager = get_pager('cut.php', array('act' => 'list'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('u', $_SESSION['user_id']?$_SESSION['user_id']:0);    // 
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        $smarty->assign('categories', get_categories_tree()); // 分类树
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        $smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typecut.xml" : 'feed.php?type=cut'); // RSS URL

        assign_dynamic('cut_list');
    }

    /* 显示模板 */
    $smarty->display('cut_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 某会员砍价活动列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'userlist')
{
    /* 取得会员砍价活动总数 */
    $now = gmtime();
    $sql = "SELECT COUNT(*) " .
            "FROM " . $GLOBALS['ecs']->table('users_activity') .
            "WHERE act_type = '" . GAT_CUT . "' " .
            "AND user_id=".$_SESSION['user_id'];
    $count =  $GLOBALS['db']->getOne($sql);

    if ($count > 0)
    {
        /* 取得每页记录数 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 1;

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
    if (!$smarty->is_cached('cut_user_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的砍价活动 */
            $cut_list = cut_user_list($size, $page,$_SESSION['user_id']);
            $smarty->assign('cut_user_list',  $cut_list);

            /* 设置分页链接 */
            $pager = get_pager('cut.php', array('act' => 'userlist'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('u', $_SESSION['user_id']?$_SESSION['user_id']:0);    // 


        assign_dynamic('cut_user_list');
    }

    /* 显示模板 */
    $smarty->display('cut_user_list.dwt', $cache_id);
}


/*------------------------------------------------------ */
//-- 砍价商品 --> 商品详情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 取得参数：砍价活动id */
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $actuid = isset($_REQUEST['actuid']) ? intval($_REQUEST['actuid']) : $_SESSION['user_id']; //不传过来默认是自己的
    $u=$_SESSION['user_id'];
	
	if(empty($_SESSION['user_id']) || $_SESSION['user_id']==0){
		$continue_url="http://".$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		$sql = "UPDATE `wxch_user`  SET continue_url = '". $continue_url . "'".
			   " WHERE wxid = '" . $_SESSION['wechat_id'] . "'";
		$db->query($sql);
	}

    if ($id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得砍价活动信息 */
    $cut = cut_info($id);
    if (empty($cut))
    {
		ecs_header("Location: ./\n");
        exit;
    }

    /* 缓存id：语言，砍价活动id，状态，如果是进行中，还要最后出价的时间（如果有的话） */
    $cache_id = $_CFG['lang'] . '-' . $id . '-' . $cut['status_no'];
    if ($cut['status_no'] == UNDER_WAY)
    {
        if (isset($cut['last_bid']))
        {
            $cache_id = $cache_id . '-' . $cut['last_bid']['bid_time'];
        }
    }
    elseif ($cut['status_no'] == FINISHED && $cut['last_bid']['bid_user'] == $_SESSION['user_id']
        && $cut['order_count'] == 0)
    {
        $cut['is_winner'] = 1;
        $cache_id = $cache_id . '-' . $cut['last_bid']['bid_time'] . '-1';
    }

    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('cut.dwt', $cache_id))
    {
        //取货品信息
        if ($cut['product_id'] > 0)
        {
            $goods_specifications = get_specifications_list($cut['goods_id']);

            $good_products = get_good_products($cut['goods_id'], 'AND product_id = ' . $cut['product_id']);

            $_good_products = explode('|', $good_products[0]['goods_attr']);
            $products_info = '';
            foreach ($_good_products as $value)
            {
                $products_info .= ' ' . $goods_specifications[$value]['attr_name'] . '：' . $goods_specifications[$value]['attr_value'];
            }
            $smarty->assign('products_info',     $products_info);
            unset($goods_specifications, $good_products, $_good_products,  $products_info);
        }

        $cut['gmt_end_time'] = local_strtotime($cut['end_time']);
        $cut['share_url'] ='http://'.$_SERVER['HTTP_HOST']."/mobile/cut.php?act=view&id=".$id."&actuid=".$actuid."&u=".$_SESSION['user_id']; //20160119 added by PRINCE 120029121
        $smarty->assign('cut', $cut);

        /* 取得砍价商品信息 */
        $goods_id = $cut['goods_id'];
        $goods = goods_info($goods_id);
        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        $goods['url'] = build_uri('goods', array('gid' => $goods_id), $goods['goods_name']);
        $smarty->assign('cut_goods', $goods);
		
		
		/* 判断能否参与 判断是否发起者*/
		$sql = "select * from ". $GLOBALS['ecs']->table('users_activity') . " where act_id='$id' and user_id=".$_SESSION['user_id'];
		$chk_join =$GLOBALS['db']->getRow($sql);

        
    	$smarty->assign('actuid', $actuid);
		
		/* 判断能否砍价 */	
		$sql = "select * from ". $GLOBALS['ecs']->table('cut_log') . " where act_user='$actuid' and act_id='$id' and bid_user=".$_SESSION['user_id'];
		$chk_cut =$GLOBALS['db']->getRow($sql);
		
		
        /* 出价记录 */	
		if($_SESSION['cut_logpage'] ){
			$user_cut_log=	 user_cut_log($actuid,$id,$_SESSION['cut_logpage']);
			 unset($_SESSION['cut_logpage']);
		}else{
			$user_cut_log=	 user_cut_log($actuid,$id);
		}
		
        $smarty->assign('user_cut_log', $user_cut_log['log']);
        $smarty->assign('pager', $user_cut_log['pager']);
        $smarty->assign('cut_log', cut_log($id));
        $smarty->assign('userid', $_SESSION['user_id']?$_SESSION['user_id']:0);
		
		
        /*左边按钮*/
	    if($cut['status_no'] == FINISHED){
			$left_action="不好意思<br />您来晚了";
		}elseif($cut['status_no'] == 0){
			 $left_action="注意啦<br />活动即将开始";
	    }else{
			if($_SESSION['cut_done'] && $chk_cut){
				 $left_action="恭喜您已砍掉<br />¥".$chk_cut['bid_price'];
                 $smarty->assign('cut_done', $chk_cut['bid_price']);  
				 unset($_SESSION['cut_done']);
			}elseif($_SESSION['cut_join']){
				 $left_action="恭喜您<br />成功参与活动";
                 $smarty->assign('cut_join', $_SESSION['cut_join']);  
				 unset($_SESSION['cut_join']);
			}elseif($actuid==$_SESSION['user_id'] && $chk_join){
				 $left_action="当前价格<br />¥".$chk_join['new_price'];
			}elseif($chk_join  && $chk_cut){
				 $left_action="快喊上朋友<br />帮忙砍价吧";
			}else{
				 $left_action="快喊上朋友<br />一起参加吧";
			}
		}
		$smarty->assign('left_action', $left_action);    // 左边按钮
		
        /*中间按钮*/
	    if($cut['status_no'] == FINISHED){
			 $center_action="已结束";
			 $center_url="cut.php?act=list";
		}elseif($cut['status_no'] == 0){
			 $center_action="即将开始";
			 $center_url="cut.php?act=list";
	    }else{
			if($chk_join  && $chk_cut){
				 $center_action='分享活动';
			}elseif(!$chk_cut){
				 if($chk_join && $actuid==$_SESSION['user_id']){
					$center_action='自砍一刀';
					$center_cut=1;
				 }elseif($actuid!=$_SESSION['user_id']){
					$center_action='帮砍一刀';
					$center_cut=1;
				 }else{
					$center_action='分享活动';
				 }
			}else{
				 $center_action='分享活动';
			}
		}
		$smarty->assign('center_action', $center_action);    // 中间按钮
		$smarty->assign('center_url', $center_url);    // 中间按钮
		$smarty->assign('center_cut', $center_cut);    // 中间按钮


        /*右边按钮*/
	    if($cut['status_no'] == FINISHED || $cut['status_no'] == 0){
			 $right_action="活动列表";
			 $right_url="cut.php?act=list";
		}else{
			if($chk_join && $actuid==$_SESSION['user_id']){
				 if($chk_join['order_times']>=$cut['orders_limit'] && $cut['orders_limit']>0){
				 $right_action='您已购买';
				 $right_click="1";
				 }else{
				 $right_action='立即购买';
				 $right_click="1";
				 }
			}elseif($chk_join){
				 $right_action='我参加的';
				 $right_url="cut.php?act=view&id=$id&actuid=".$_SESSION['user_id']."&u=$u";
			}else{
				 $right_action='参与活动';
				 $right_url="cut.php?act=join&id=$id";
			}
		}
        $smarty->assign('right_action', $right_action);  // 右边按钮
        $smarty->assign('right_url', $right_url);   // 右边按钮
        $smarty->assign('right_click', $right_click);   // 右边按钮


		$fenxiao_url = $db->getOne("SELECT cfg_value  FROM `wxch_cfg` WHERE `cfg_name` = 'tianxin_url'");
        $smarty->assign('fenxiao_url',    $fenxiao_url);  // 引导关注

        $affiliate_u=isset($_REQUEST['u']) ? intval($_REQUEST['u']) : 0;
	    $qr_path = $GLOBALS['db']->getOne("SELECT qr_path FROM wxch_qr_tianxin100 WHERE scene_id = '$affiliate_u'");
        $smarty->assign('qr_path',    $qr_path);  // 图片二维码


        //模板赋值
        $smarty->assign('cfg', $_CFG);
        assign_template();

        $position = assign_ur_here(0, $cut['goods_name']?$cut['goods_name']:$goods['goods_name']);
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置

		require_once "wxjs/jssdk.php";
		$ret = $db->getRow("SELECT  *  FROM `wxch_config`");
		$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
		$signPackage = $jssdk->GetSignPackage();
		$smarty->assign('signPackage',  $signPackage);

        assign_dynamic('cut');
    }

    //更新商品点击次数  P R I N C E Q Q 120 029 121
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $cut['goods_id'] . "'";
    $db->query($sql);
	
	
	


    $smarty->assign('now_time',  gmtime());           // 当前系统时间
    $smarty->display('cut.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 砍价商品 --> 发起
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'join')
{
    include_once(ROOT_PATH . 'include/lib_order.php');

    /* 取得参数：砍价活动id */
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $u=$_SESSION['user_id'];

    if ($id <= 0 )
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得砍价活动信息 */
    $cut = cut_info($id);
    if (empty($cut))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 活动是否正在进行 */
    if ($cut['status_no'] != UNDER_WAY)
    {
        show_message('活动已结束', '', '', 'error');
    }

    /* 是否登录 */
    $user_id = $_SESSION['user_id'];
    if ($user_id <= 0)
    {
        show_message($_LANG['au_buy_after_login'], "马上登陆", 'user.php', 'error');
    }
		
    /* 判断能否参加 */	
    $sql = "select * from ". $GLOBALS['ecs']->table('users_activity') . " where user_id='$user_id' and act_id='$id' ";
    $chk_join =$GLOBALS['db']->getOne($sql);
	
	if($chk_join){
		/* 已参加成功 跳转到活动详情页 */
		ecs_header("Location: cut.php?act=view&id=$id&actuid=$user_id&join=1\n");
		exit;
	}

	
	//获取用户昵称、头像
    $sql = "SELECT u.*,w.nickname,w.headimgurl FROM  " . $GLOBALS['ecs']->table('users') . " u ".
            "left join  wxch_user w on u.user_name=w.uname ".
            "WHERE  u.user_id=".$_SESSION['user_id'];
    $getinfo =$GLOBALS['db']->getRow($sql);
	
    $goods_id = $cut['goods_id'];
    $goods = goods_info($goods_id);
	
    
    /* 插入用户活动记录 */
    $users_activity = array(
        'user_id'    => $user_id,
        'user_nickname'  => $getinfo['nickname']?$getinfo['nickname']:$getinfo['user_name'],
        'user_head'  => $getinfo['headimgurl'],
        'act_id'  => $id,
        'act_type'  => GAT_CUT,
        'shop_price' => $goods['shop_price'],
        'new_price' => $goods['shop_price'],
        'activity_time'  => gmtime()
    );
    $db->autoExecute($ecs->table('users_activity'), $users_activity, 'INSERT');	
	
     $_SESSION['cut_join']=1;

    /* 参加成功 跳转到活动详情页 */
    ecs_header("Location: cut.php?act=view&id=$id&actuid=$user_id&u=$u\n");
    exit;
}

/*------------------------------------------------------ */
//-- 砍价商品 --> 砍价记录
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'logpage')
{

    /* 取得参数：砍价活动id */
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $actuid = isset($_REQUEST['actuid']) ? intval($_REQUEST['actuid']) : 0;
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $u=$_SESSION['user_id'];

    if ($id <= 0 ||  $actuid <=0)
    {
        ecs_header("Location: ./\n");
        exit;
    }else{
        $_SESSION['cut_logpage']=$page;
		/* 跳转到活动详情页 */
		ecs_header("Location: cut.php?act=view&id=$id&actuid=$actuid&u=$u\n");
		exit;
	}
	
	
}

/*------------------------------------------------------ */
//-- 砍价商品 --> 砍价
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'bid')
{
    include_once(ROOT_PATH . 'include/lib_order.php');
    $u=$_SESSION['user_id'];

    /* 取得参数：砍价活动id */
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $actuid = isset($_POST['actuid']) ? intval($_POST['actuid']) : 0;

    if ($id <= 0 || $actuid <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得砍价活动信息 */
    $cut = cut_info($id);
    if (empty($cut))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 活动是否正在进行 */
    if ($cut['status_no'] != UNDER_WAY)
    {
        show_message($_LANG['au_not_under_way'], '', '', 'error');
    }

    /* 是否登录 */
    $user_id = $_SESSION['user_id'];
    if ($user_id <= 0)
    {
        show_message($_LANG['au_buy_after_login'], "马上登陆", 'user.php', 'error');
    }
		

	
	
	//获取用户昵称、头像
    $sql = "SELECT u.*,w.nickname,w.headimgurl FROM  " . $GLOBALS['ecs']->table('users') . " u ".
            "left join  wxch_user w on u.user_name=w.uname ".
            "WHERE  u.user_id='$user_id'";
    $getinfo =$GLOBALS['db']->getRow($sql);
	
	
	/* 判断是否有团长 */	
	$sql = "select * from ". $GLOBALS['ecs']->table('users_activity') . " where user_id='$actuid' and act_id='$id' ";
	$chk_owner =$GLOBALS['db']->getRow($sql);
	
	/* 判断能否砍价 */	
	$sql = "select * from ". $GLOBALS['ecs']->table('cut_log') . " where act_user='$actuid' and act_id='$id' and bid_user=".$_SESSION['user_id'];
	$chk_cut =$GLOBALS['db']->getRow($sql);
	
	if(!$chk_cut && $chk_owner){
		
		/* 取得当前价 */	
		$sql = "select new_price from ". $GLOBALS['ecs']->table('users_activity') . " where user_id='$actuid' and act_id='$id' ";
		$current_price =$GLOBALS['db']->getOne($sql);
	
		/* 取得砍价 */	
		$bid_price = round(randomFloat($cut['start_price'],$cut['end_price']),2);
		
		
		/* 如果砍后价格大于最低限价，则修改砍价值 */
		if ( $current_price - $bid_price<$cut['max_price'])
		{
			$bid_price =$current_price-$cut['max_price'];
			  /* 结束该会员砍价活动 */
			  $sql = "UPDATE " . $ecs->table('users_activity') . " SET is_finished = 1 WHERE user_id='$actuid' and act_id='$id' ";
			  $db->query($sql);
		}
		
		$after_bid_price=$current_price-$bid_price;
		
		
		/* 插入砍价记录 */
		$cut_log = array(
			'act_id'    => $id,
			'act_user'  => $actuid,
			'bid_user'  => $user_id,
			'bid_user_nickname'  => $getinfo['nickname']?$getinfo['nickname']:$getinfo['user_name'],
			'bid_user_head'  => $getinfo['headimgurl'],
			'bid_price' => $bid_price,
			'after_bid_price' => $after_bid_price,
			'bid_time'  => gmtime()
		);
		$db->autoExecute($ecs->table('cut_log'), $cut_log, 'INSERT');
		
		/*更新发起者价格*/
		$sql = "UPDATE " . $ecs->table('users_activity') . " SET new_price = '$after_bid_price' WHERE act_id = '$id' and user_id='$actuid' LIMIT 1";
		$db->query($sql);
		
        $_SESSION['cut_done']=1;
		
		//发送微信提醒
		$nowuserid=$_SESSION['user_id']?$_SESSION['user_id']:0;
		if($actuid!=$nowuserid ){
		    require(ROOT_PATH . 'wxch_cut.php');
		}
	}
	
	

    /* 跳转到活动详情页 */
    ecs_header("Location: cut.php?act=view&id=$id&actuid=$actuid&u=$u\n");
    exit;
}

/*------------------------------------------------------ */
//-- 砍价商品 --> 购买
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'buy')
{
    /* 查询：取得参数：砍价活动id */
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $actuid = isset($_POST['actuid']) ? intval($_POST['actuid']) : $_SESSION['user_id']; //理应就是自己的

    if ($id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：取得砍价活动信息 */
    $cut = cut_info($id);
    if (empty($cut))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 活动是否正在进行 */
    if ($cut['status_no'] != UNDER_WAY)
    {
        show_message('活动已结束', '', '', 'error');
    }


    /* 查询：是否登录 */
    $user_id = $_SESSION['user_id'];
    if ($user_id <= 0)
    {
		show_message($_LANG['au_buy_after_login'], "马上登陆", 'user.php', 'error');
    }


	$sql = "select * from ". $GLOBALS['ecs']->table('users_activity') . " where user_id='$actuid' and act_id='$id' ";
	$users_activity =$GLOBALS['db']->getRow($sql);
	/* 查询：检查已经购买过 */
    /*if ($users_activity['is_finished']==2 ){   
        show_message('您已经购买过了', '', '', 'error');
    }*/

    /* 查询：取得商品信息 */
    $goods = goods_info($cut['goods_id']);

    /* 查询：处理规格属性 */
    $goods_attr = '';
    $goods_attr_id = '';
    if ($cut['product_id'] > 0)
    {
        $product_info = get_good_products($cut['goods_id'], 'AND product_id = ' . $cut['product_id']);

        $goods_attr_id = str_replace('|', ',', $product_info[0]['goods_attr']);

        $attr_list = array();
        $sql = "SELECT a.attr_name, g.attr_value " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
                    $ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_attr_id " . db_create_in($goods_attr_id);
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
        }
        $goods_attr = join(chr(13) . chr(10), $attr_list);
    }
    else
    {
        $cut['product_id'] = 0;
    }

    /* 清空购物车中所有砍价商品 */
    include_once(ROOT_PATH . 'include/lib_order.php');
    clear_cart(CART_CUT_GOODS);

    /* 加入购物车 */
    $cart = array(
        'user_id'        => $user_id,
        'session_id'     => SESS_ID,
        'goods_id'       => $cut['goods_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $users_activity['new_price'],
        'fencheng'       => addslashes($goods['fencheng']),
        'goods_number'   => 1,
        'goods_attr'     => $goods_attr,
        'goods_attr_id'  => $goods_attr_id,
        'is_real'        => $goods['is_real'],
        'extension_code' => addslashes($goods['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_CUT_GOODS,
        'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 记录购物流程类型：砍价 */
    $_SESSION['flow_type'] = CART_CUT_GOODS;
    $_SESSION['extension_code'] = 'cut';
    $_SESSION['extension_id'] = $id;

    /* 进入收货人页面 */
    ecs_header("Location: ./flow.php?step=consignee\n");
    exit;
}

/**
 * 取得砍价活动数量
 * @return  int
 */
function cut_count()
{
    $now = gmtime();
    $sql = "SELECT COUNT(*) " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_type = '" . GAT_CUT . "' " .
            "AND start_time <= '$now' AND end_time >= '$now' AND is_finished < 2";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某页的砍价活动
 * @param   int     $size   每页记录数
 * @param   int     $page   当前页
 * @return  array
 */
function cut_list($size, $page)
{
    $cut_list = array();
    $cut_list['finished'] = $cut_list['finished'] = array();

    $now = gmtime();
    $sql = "SELECT a.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS a " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON a.goods_id = g.goods_id " .
            "WHERE a.act_type = '" . GAT_CUT . "' " .
            "AND a.start_time <= '$now' AND a.end_time >= '$now' AND a.is_finished < 2 ORDER BY a.act_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($row['ext_info']);
        $cut = array_merge($row, $ext_info);
        $cut['status_no'] = cut_status($cut);

        $cut['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $cut['start_time']);
        $cut['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $cut['end_time']);
        $cut['formated_start_price'] = price_format($cut['start_price']);
        $cut['formated_end_price'] = price_format($cut['end_price']);
        $cut['formated_deposit'] = price_format($cut['deposit']);
        $cut['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $cut['url'] = build_uri('cut', array('auid'=>$cut['act_id']));  //???
        $cut['shop_price'] = price_format($row['shop_price']);
        $cut['goods_name'] = $row['act_name']?$row['act_name']:$row['goods_name'];

        if($cut['status_no'] < 2)
        {
            $cut_list['under_way'][] = $cut;
        }
        else
        {
            $cut_list['finished'][] = $cut;
        }
    }

    $cut_list = @array_merge($cut_list['under_way'], $cut_list['finished']);

    return $cut_list;
}

/**
 * 取得某页的砍价活动
 * @param   int     $size   每页记录数
 * @param   int     $page   当前页
 * @return  array
 */
function cut_user_list($size, $page,$act_user)
{
    $cut_list = array();
    $cut_list['finished'] = $cut_list['finished'] = array();

    $now = gmtime();
    $sql = "SELECT a.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb " .
            "FROM " . $GLOBALS['ecs']->table('users_activity') . " AS u " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods_activity') . " AS a ON u.act_id  = a.act_id  " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON a.goods_id = g.goods_id " .
            "WHERE a.act_type = '" . GAT_CUT . "' " .
            "AND u.user_id='$act_user' and a.start_time <= '$now' AND a.end_time >= '$now' AND a.is_finished < 2 ORDER BY a.act_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($row['ext_info']);
        $cut = array_merge($row, $ext_info);
        $cut['status_no'] = cut_status($cut);

        $cut['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $cut['start_time']);
        $cut['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $cut['end_time']);
        $cut['formated_start_price'] = price_format($cut['start_price']);
        $cut['formated_end_price'] = price_format($cut['end_price']);
        $cut['formated_deposit'] = price_format($cut['deposit']);
        $cut['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $cut['url'] = build_uri('cut', array('auid'=>$cut['act_id']));  //???
        $cut['shop_price'] = price_format($row['shop_price']);
        $cut['goods_name'] = $row['act_name']?$row['act_name']:$row['goods_name'];

        if($cut['status_no'] < 2)
        {
            $cut_list['under_way'][] = $cut;
        }
        else
        {
            $cut_list['finished'][] = $cut;
        }
    }

    $cut_list = @array_merge($cut_list['under_way'], $cut_list['finished']);

    return $cut_list;
}

//生成随机数 P R I N C E Q Q 120 029 121
function randomFloat($min = 0, $max = 1) {
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

?>