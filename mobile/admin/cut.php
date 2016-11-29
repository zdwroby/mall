<?php
//decode by QQ:270656184 http://www.yunlu99.com/
error_reporting(0);

function getTopDomainhuo(){

		$host=$_SERVER['HTTP_HOST'];

		$host=strtolower($host);

		if(strpos($host,'/')!==false){

			$parse = @parse_url($host);

			$host = $parse['host'];

		}

		$topleveldomaindb=array

('com','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me');

		$str='';

		foreach($topleveldomaindb as $v){

			$str.=($str ? '|' : '').$v;

		}

		$matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";

		if(preg_match("/".$matchstr."/ies",$host,$matchs)){

			$domain=$matchs['0'];

		}else{

			$domain=$host;

		}

		return $domain;

}

// $domain=getTopDomainhuo();

// $real_domain='baidu.com'; //本地检查时 用户的授权域名 和时间

// $check_host='http://auc.coolhong.com/update.php?a=client_check&u='.$domain;

// $check_info=file_get_contents($check_host);



// if($check_info=='1'){

   // echo '域名未授权,联系QQ：120029121';

   // die;

// }elseif($check_info=='2'){

   // echo '授权已经到期，联系QQ：120029121';

   // die;

// }

// if($check_info!=='0'){ // 远程检查失败的时候 本地检查

   // if($domain!==$real_domain){

      // echo '域名未经授权,联系QQ：120029121';

	  // die;

   // }

// }

unset($domain);
/**
 * 砍价活动管理程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: PRINCE $
 * $Id: cut.php 17217 2016-01-07 06:29:08Z PRINCE 120029121 $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("goods_activity"), $db, 'act_id', 'act_name');

/*------------------------------------------------------ */
//-- 添加活动
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('cut_manage');

    /* 初始化信息 */
    $start_time = local_date('Y-m-d H:i');
    $end_time   = local_date('Y-m-d H:i', strtotime('+1 month'));
    $cut     = array('start_price'=>'1.00','end_price'=>'5.00','max_price'=>'0','orders_limit'=>'0', 'cost_points'=>'1','start_time' => $start_time,'end_time' => $end_time,'option'=>'<option value="0">'.$_LANG['make_option'].'</option>');

    $smarty->assign('cut',       $cut);
    $smarty->assign('ur_here',      '添加砍价活动');
    $smarty->assign('action_link',  array('text' => '砍价活动列表', 'href'=>'cut.php?act=list'));
    $smarty->assign('cat_list',     cat_list());
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('form_action',  'insert');

    assign_query_info();
    $smarty->display('cut_info.htm');
}

elseif ($_REQUEST['act'] =='insert')
{
    /* 权限判断 */
    admin_priv('cut_manage');

    /* 检查商品是否存在 */
    $sql = "SELECT goods_name FROM ".$ecs->table('goods')." WHERE goods_id = '$_POST[goods_id]'";
    $_POST['goods_name'] = $db->GetOne($sql);
    if (empty($_POST['goods_name']))
    {
        sys_msg($_LANG['no_goods'], 1);
        exit;
    }

    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_activity').
           " WHERE act_type='" . GAT_CUT . "' AND act_name='" . $_POST['cut_name'] . "'" ;
    if ($db->getOne($sql))
    {
        sys_msg(sprintf($_LANG['cut_name_exist'],  $_POST['cut_name']) , 1);
    }

    /* 将时间转换成整数 */
    $_POST['start_time'] = local_strtotime($_POST['start_time']);
    $_POST['end_time']   = local_strtotime($_POST['end_time']);

    /* 处理提交数据 */
    if (empty($_POST['start_price']))
    {
        $_POST['start_price'] = 0;
    }
    if (empty($_POST['end_price']))
    {
        $_POST['end_price'] = 0;
    }
    if (empty($_POST['max_price']))
    {
        $_POST['max_price'] = 0;
    }
    if (empty($_POST['cost_points']))
    {
        $_POST['cost_points'] = 0;
    }
    if (isset($_POST['product_id']) && empty($_POST['product_id']))
    {
        $_POST['product_id'] = 0;
    }
    $orders_limit = intval($_POST['orders_limit']);
    if ($orders_limit < 0){
            $orders_limit = 0;
    }

    $info = array('start_price'=>$_POST['start_price'], 'end_price'=>$_POST['end_price'], 'max_price'=>$_POST['max_price'], 'cost_points'=>$_POST['cost_points'],'showlimit'=>$_POST['showlimit'],'needreg'=>$_POST['needreg'],'orders_limit'=>$orders_limit,'share_title'=>$_POST['share_title'],'share_brief'=>$_POST['share_brief']);

    /* 插入数据 */
    $record = array('act_name'=>$_POST['cut_name'], 'act_desc'=>$_POST['desc'],
                    'act_type'=>GAT_CUT, 'goods_id'=>$_POST['goods_id'], 'goods_name'=>$_POST['goods_name'],
                    'start_time'=>$_POST['start_time'], 'end_time'=>$_POST['end_time'],
                    'product_id'=>$_POST['product_id'],
                    'is_finished'=>0, 'ext_info'=>serialize($info));

    $db->AutoExecute($ecs->table('goods_activity'),$record,'INSERT');

    admin_log($_POST['cut_name'],'add','cut');
    $link[] = array('text' => $_LANG['back_list'], 'href'=>'cut.php?act=list');
    $link[] = array('text' => $_LANG['continue_add'], 'href'=>'cut.php?act=add');
    sys_msg($_LANG['add_succeed'],0,$link);
}

/*------------------------------------------------------ */
//-- 活动列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      '砍价活动');
    $smarty->assign('action_link',  array('text' => '添加砍价活动', 'href'=>'cut.php?act=add'));

    $cuts = get_cutlist();

    $smarty->assign('cut_list',  $cuts['cuts']);
    $smarty->assign('filter',       $cuts['filter']);
    $smarty->assign('record_count', $cuts['record_count']);
    $smarty->assign('page_count',   $cuts['page_count']);

    $sort_flag  = sort_flag($cuts['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $smarty->assign('full_page',    1);
    assign_query_info();
    $smarty->display('cut_list.htm');
}

/*------------------------------------------------------ */
//-- 查询、翻页、排序
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $cuts = get_cutlist();

    $smarty->assign('cut_list',  $cuts['cuts']);
    $smarty->assign('filter',       $cuts['filter']);
    $smarty->assign('record_count', $cuts['record_count']);
    $smarty->assign('page_count',   $cuts['page_count']);

    $sort_flag  = sort_flag($cuts['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('cut_list.htm'), '',
        array('filter' => $cuts['filter'], 'page_count' => $cuts['page_count']));
}

/*------------------------------------------------------ */
//-- 编辑活动名称
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_cut_name')
{
    check_authz_json('cut_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));

    /* 检查活动重名 */
    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_activity').
           " WHERE act_type='" . GAT_CUT . "' AND act_name='$val' AND act_id <> '$id'" ;
    if ($db->getOne($sql))
    {
        make_json_error(sprintf($_LANG['cut_name_exist'],  $val));
    }

    $exc->edit("act_name='$val'", $id);
    make_json_result(stripslashes($val));
}

/*------------------------------------------------------ */
//-- 删除指定的活动
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('attr_manage');

    $id = intval($_GET['id']);

    $exc->drop($id);

    $url = 'cut.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 编辑活动
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('cut_manage');

    $cut        = get_cut_info($_REQUEST['id']);

    $cut['option'] = '<option value="'.$cut['goods_id'].'">'.$cut['goods_name'].'</option>';
    $smarty->assign('cut',               $cut);
    $smarty->assign('ur_here',              $_LANG['cut_edit']);
    $smarty->assign('action_link',          array('text' => '砍价活动列表', 'href'=>'cut.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action',        'update');

    /* 商品货品表 */
    $smarty->assign('good_products_select', get_good_products_select($cut['goods_id']));

    assign_query_info();
    $smarty->display('cut_info.htm');
}
elseif ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('cut_manage');

    /* 将时间转换成整数 */
    $_POST['start_time'] = local_strtotime($_POST['start_time']);
    $_POST['end_time']   = local_strtotime($_POST['end_time']);

    /* 处理提交数据 */
    if (empty($_POST['cut_name']))
    {
        $_POST['cut_name'] = '';
    }
    if (empty($_POST['goods_id']))
    {
        $_POST['goods_id'] = 0;
    }
    else
    {
        $_POST['goods_name'] = $db->getOne("SELECT goods_name FROM " . $ecs->table('goods') . "WHERE goods_id= '$_POST[goods_id]'");
    }
    if (empty($_POST['start_price']))
    {
        $_POST['start_price'] = 0;
    }
    if (empty($_POST['end_price']))
    {
        $_POST['end_price'] = 0;
    }
    if (empty($_POST['max_price']))
    {
        $_POST['max_price'] = 0;
    }
    if (empty($_POST['cost_points']))
    {
        $_POST['cost_points'] = 0;
    }
    if (isset($_POST['product_id']) && empty($_POST['product_id']))
    {
        $_POST['product_id'] = 0;
    }
    $orders_limit = intval($_POST['orders_limit']);
    if ($orders_limit < 0){
            $orders_limit = 0;
    }
    /* 检查活动重名 */
    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_activity').
           " WHERE act_type='" . GAT_CUT . "' AND act_name='" . $_POST['cut_name'] . "' AND act_id <> '" .  $_POST['id'] . "'" ;
    if ($db->getOne($sql))
    {
        sys_msg(sprintf($_LANG['cut_name_exist'],  $_POST['cut_name']) , 1);
    }

    $info = array('start_price'=>$_POST['start_price'], 'end_price'=>$_POST['end_price'], 'max_price'=>$_POST['max_price'], 'cost_points'=>$_POST['cost_points'], 'showlimit'=>$_POST['showlimit'], 'needreg'=>$_POST['needreg'],'orders_limit'=>$orders_limit,'share_title'=>$_POST['share_title'],'share_brief'=>$_POST['share_brief']);

    /* 更新数据 */
    $record = array('act_name' => $_POST['cut_name'], 'goods_id' => $_POST['goods_id'],
                    'goods_name' =>$_POST['goods_name'], 'start_time' => $_POST['start_time'],
                    'end_time' => $_POST['end_time'], 'act_desc' => $_POST['desc'],
                    'product_id'=>$_POST['product_id'],
                    'ext_info'=>serialize($info));
    $db->autoExecute($ecs->table('goods_activity'), $record, 'UPDATE', "act_id = '" . $_POST['id'] . "' AND act_type = " . GAT_CUT );

    admin_log($_POST['cut_name'],'edit','cut');
    $link[] = array('text' => $_LANG['back_list'], 'href'=>'cut.php?act=list&' . list_link_postfix());
    sys_msg($_LANG['edit_succeed'],0,$link);
 }

/*------------------------------------------------------ */
//-- 查看活动详情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 权限判断 */
    //admin_priv('cut_manage');

    $id = empty($_REQUEST['act_id']) ? 0 : intval($_REQUEST['act_id']);
    $uid = empty($_REQUEST['uid']) ? 0 : intval($_REQUEST['uid']);

    if($id && $uid){
		$bid_list = get_user_cut_detail();
	
		$smarty->assign('bid_list',     $bid_list['bid']);
		$smarty->assign('filter',       $bid_list['filter']);
		$smarty->assign('record_count', $bid_list['record_count']);
		$smarty->assign('page_count',   $bid_list['page_count']);
	
		$sort_flag  = sort_flag($bid_list['filter']);
		$smarty->assign($sort_flag['tag'], $sort_flag['img']);
		/* 赋值 */
		$smarty->assign('info',         get_cut_info($id));
		$smarty->assign('full_page',    1);
		//$smarty->assign('result',       get_cut_result($id));
		$smarty->assign('ur_here',      '活动详情' );
		$smarty->assign('action_link',  array('text' => '砍价活动参与者列表', 'href'=>'cut.php?act=view&act_id='.$id));
		$smarty->display('cut_user_view.htm');
	}else{
		$bid_list = get_cut_detail();
	
		$smarty->assign('bid_list',     $bid_list['bid']);
		$smarty->assign('filter',       $bid_list['filter']);
		$smarty->assign('record_count', $bid_list['record_count']);
		$smarty->assign('page_count',   $bid_list['page_count']);
	
		$sort_flag  = sort_flag($bid_list['filter']);
		$smarty->assign($sort_flag['tag'], $sort_flag['img']);
		/* 赋值 */
		$smarty->assign('info',         get_cut_info($id));
		$smarty->assign('full_page',    1);
		//$smarty->assign('result',       get_cut_result($id));
		$smarty->assign('ur_here',      '活动详情' );
		$smarty->assign('action_link',  array('text' => '砍价活动列表', 'href'=>'cut.php?act=list'));
		$smarty->display('cut_view.htm');
	}
}

/*------------------------------------------------------ */
//-- 排序、翻页活动详情
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query_bid')
{
    $bid_list = get_cut_detail();

    $smarty->assign('bid_list',     $bid_list['bid']);
    $smarty->assign('filter',       $bid_list['filter']);
    $smarty->assign('record_count', $bid_list['record_count']);
    $smarty->assign('page_count',   $bid_list['page_count']);

    $sort_flag  = sort_flag($bid_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('cut_view.htm'), '',
        array('filter' => $bid_list['filter'], 'page_count' => $bid_list['page_count']));
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_goods')
{
    include_once(ROOT_PATH . 'include/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr['goods'] = get_goods_list($filters);

    if (!empty($arr['goods'][0]['goods_id']))
    {
        $arr['products'] = get_good_products($arr['goods'][0]['goods_id']);
    }

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 搜索货品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_products')
{
    include_once(ROOT_PATH . 'include/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    if (!empty($filters->goods_id))
    {
        $arr['products'] = get_good_products($filters->goods_id);
    }

    make_json_result($arr);
}

/**
 * 获取活动列表
 *
 * @access  public
 *
 * @return void
 */
function get_cutlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
        $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'act_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = (!empty($filter['keywords'])) ? " AND act_name like '%". mysql_like_quote($filter['keywords']) ."%'" : '';

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods_activity') .
               " WHERE act_type =" . GAT_CUT . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获活动数据 */
        $sql = "SELECT act_id, act_name AS cut_name, goods_name, start_time, end_time, is_finished, ext_info, product_id ".
               " FROM " . $GLOBALS['ecs']->table('goods_activity') .
               " WHERE act_type = " . GAT_CUT . $where .
               " ORDER by $filter[sort_by] $filter[sort_order] LIMIT ". $filter['start'] .", " . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        $row[$key]['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['start_time']);
        $row[$key]['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $val['end_time']);
        $info = unserialize($row[$key]['ext_info']);
        unset($row[$key]['ext_info']);
        if ($info)
        {
            foreach ($info as $info_key => $info_val)
            {
                $row[$key][$info_key] = $info_val;
            }
        }
    }

    $arr = array('cuts' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 获取指定id cut 的信息
 *
 * @access  public
 * @param   int         $id         act_id
 *
 * @return array       array(act_id, cut_name, goods_id,start_time, end_time, min_price, integral)
 */
function get_cut_info($id)
{
    global $ecs, $db,$_CFG;

    $sql = "SELECT act_id, act_name AS cut_name, goods_id, product_id, goods_name, start_time, end_time, act_desc, ext_info" .
           " FROM " . $GLOBALS['ecs']->table('goods_activity') .
           " WHERE act_id='$id' AND act_type = " . GAT_CUT;

    $cut = $db->GetRow($sql);

    /* 将时间转成可阅读格式 */
    $cut['start_time'] = local_date('Y-m-d H:i', $cut['start_time']);
    $cut['end_time']   = local_date('Y-m-d H:i', $cut['end_time']);
    $row = unserialize($cut['ext_info']);
    unset($cut['ext_info']);
    if ($row)
    {
        foreach ($row as $key=>$val)
        {
            $cut[$key] = $val;
        }
    }

    return $cut;
}

/**
 * 返回活动详细列表
 *
 * @access  public
 *
 * @return array
 */
function get_user_cut_detail()
{
    $filter['act_id']  = empty($_REQUEST['act_id']) ? 0 : intval($_REQUEST['act_id']);
    $filter['uid']  = empty($_REQUEST['uid']) ? 0 : intval($_REQUEST['uid']);
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'bid_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = (empty($filter['act_id']) || empty($filter['uid']) )? '' : " WHERE act_id='$filter[act_id]' and act_user='$filter[uid]'";

    /* 获得记录总数以及总页数 */
    $sql = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('cut_log'). $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 获得活动数据 */
    $sql = "SELECT s.* ".
            " FROM ".$GLOBALS['ecs']->table('cut_log')." AS s ".
            $where.
            " ORDER by ".$filter['sort_by']." ".$filter['sort_order'].
            " LIMIT ". $filter['start'] .", " . $filter['page_size'];
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        //$row[$key]['bid_time'] = date($GLOBALS['_CFG']['time_format'], $val['bid_time']);
		 $row[$key]['bid_time'] =  local_date('Y-m-d H:i', $val['bid_time']);

    }

    $arr = array('bid' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 返回活动详细列表
 *
 * @access  public
 *
 * @return array
 */
function get_cut_detail()
{
    $filter['act_id']  = empty($_REQUEST['act_id']) ? 0 : intval($_REQUEST['act_id']);
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'activity_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = empty($filter['act_id']) ? '' : " WHERE act_id='$filter[act_id]' ";

    /* 获得记录总数以及总页数 */
    $sql = "SELECT count(*) FROM ".$GLOBALS['ecs']->table('users_activity'). $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 获得活动数据 */
    $sql = "SELECT * ".
            " FROM ".$GLOBALS['ecs']->table('users_activity'). $where.
            " ORDER by ".$filter['sort_by']." ".$filter['sort_order'].
            " LIMIT ". $filter['start'] .", " . $filter['page_size'];
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        //$row[$key]['activity_time'] = date($GLOBALS['_CFG']['time_format'], $val['activity_time']);
		 $row[$key]['activity_time'] =  local_date('Y-m-d H:i', $val['activity_time']);

    }

    $arr = array('bid' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}