<?php
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/user.php');

$user_id = $_SESSION['user_id'];
$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$wxid = !empty($_GET['wxid']) ? $_GET['wxid'] : '';

if(empty($wxid))
{
    $wxid = $_SESSION['wxid'];
}
else
{
    $_SESSION['wxid'] = $wxid;
}
if(empty($_SESSION['wxid']))
{
    exit('请从微信界面重新进入');
}

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act='';


// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr =
array('login','act_login','register','act_register','act_edit_password','get_password','send_pwd_email','password', 'signin', 'add_tag', 'collect', 'return_to_cart', 'logout', 'email_list', 'validate_email', 'send_hash_mail', 'order_query', 'is_registered', 'check_email','clear_history','qpassword_name', 'get_passwd_question', 'check_answer');

/* 显示页面的action列表 */
$ui_arr = array('register', 'login', 'profile', 'order_list', 'order_detail', 'address_list', 'collection_list',
'message_list', 'tag_list', 'get_password', 'reset_password', 'booking_list', 'add_booking', 'account_raply',
'account_deposit', 'account_log', 'account_detail', 'act_account', 'pay', 'default', 'bonus', 'group_buy', 'group_buy_detail', 'affiliate', 'comment_list','validate_email','track_packages', 'transform_points','qpassword_name', 'get_passwd_question', 'check_answer');
/* 如果是显示页面，对页面进行相应赋值 */
if (in_array($action, $ui_arr))
{
    assign_template();
    $position = assign_ur_here(0, $_LANG['user_center']);
    $smarty->assign('page_title', $position['title']); // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);
    $sql = "SELECT value FROM " . $ecs->table('shop_config') . " WHERE id = 419";
    $row = $db->getRow($sql);
    $car_off = $row['value'];
    $smarty->assign('car_off',       $car_off);
    /* 是否显示积分兑换 */
    if (!empty($_CFG['points_rule']) && unserialize($_CFG['points_rule']))
    {
        $smarty->assign('show_transform_points',     1);
    }
    $smarty->assign('helps',      get_shop_help());        // 网店帮助
    $smarty->assign('data_dir',   DATA_DIR);   // 数据目录
    $smarty->assign('action',     $action);
    $smarty->assign('lang',       $_LANG);
}

//用户中心欢迎页
/* 用户登录界面 */
if ($action == 'default')
{
    if (empty($back_act))
    {
        if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
        {
            $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
        }
        else
        {
            $back_act = 'user.php';
        }

    }
	$smarty->assign('wxid', $wxid);
    $smarty->assign('back_act', $back_act);
    $smarty->display('user_wxch.dwt');
}

/* 处理会员的登录 */
elseif ($action == 'act_login')
{

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';
	$oldpassword = isset($_POST['oldpassword']) ? trim($_POST['oldpassword']) : '';
	$wxid = isset($_POST['wxid']) ? trim($_POST['wxid']) : '';
	
	/*甜心100  新增会员绑定开始验证*/
	$user_id = $db -> getOne("SELECT `user_id` FROM `ecs_users` WHERE `wxid` = '$wxid'");
	
	if(!empty($user_id)&&empty($oldpassword)){
		show_message("请先输入原始绑定账号的对应密码", '重新输入', 'user_wxch.php', 'error');
	}
	if(!empty($user_id)&&!empty($oldpassword)){
		
		$oleuser_name = $db -> getOne("SELECT `user_name` FROM `ecs_users` WHERE `wxid` = '$wxid'");
		if($oleuser_name==$username){
			
			show_message("已经绑定无需重新绑定", '重新输入', 'user_wxch.php', 'error');
		}
		if ($user->login($oleuser_name, $oldpassword, isset($_POST['remember']))){
			
				$user->logout();
		}else{
			
			
			show_message("原始密码输入错误", '重新输入', 'user_wxch.php', 'error');
		}
		
	}	
	//用户名是邮箱格式 by wang
    if(is_email($username))
    {
        $sql ="select user_name from ".$ecs->table('users')." where email='".$username."'";
        $username_try = $db->getOne($sql);
        $username = $username_try ? $username_try:$username;
    }

    //用户名是手机格式 by wang
    if(is_mobile($username))
    {
        $sql ="select user_name from ".$ecs->table('users')." where mobile_phone='".$username."'";
        $username_try = $db->getOne($sql);
        $username = $username_try ? $username_try:$username;
    }

    if ($user->login($username, $password, isset($_POST['remember'])))
    {
        update_user_info();
        recalculate_price();
        $ucdata = isset($user->ucdata)? $user->ucdata : '';
        $uname = $db->getOne("SELECT `uname` FROM `wxch_user` WHERE `uname` = '$username' ");
        if(empty($uname))
        {
            $db->query("UPDATE `wxch_user` SET `setp`= 3,`uname`='$username' WHERE `wxid`= '$wxid';");
        }
        else
        {
            $db->query("UPDATE `wxch_user` SET `uname`='' WHERE `uname`='$username';");
            $db->query("UPDATE `wxch_user` SET `setp`= 3,`uname`='$username' WHERE `wxid`= '$wxid';");
			if(!empty($user_id)){
			$db->query("UPDATE `ecs_users` SET `wxid`='' WHERE `user_id`='$user_id';");
			}
        }
        $db->query("UPDATE ".$ecs->table('users')." SET `wxch_bd`='ok',`wxid`='$wxid' WHERE `user_name`='$username'");
		//甜心100修复，重新绑定账号后，上下级关系不变。
		$affiliate = $db -> getOne("SELECT `affiliate` FROM  `wxch_user` WHERE `wxid` = '$wxid'");
		$db -> query("UPDATE ".$ecs->table('users')." SET `parent_id`='$affiliate' WHERE `wxid`= '$wxid';");	
		//甜心新增


        show_message('绑定成功' . $ucdata , array($_LANG['back_up_page'], $_LANG['profile_lnk']), array($back_act,'user.php'), 'info');
    }
    else
    {
        
        $_SESSION['login_fail'] ++ ;
        show_message($_LANG['login_failure'], '重新绑定', 'user_wxch.php', 'error');
    }
}


//记录分享积分记录  by  tianxin100
function  record_point($keyword,$fromUsername){
		
		$db=$GLOBALS['db'];
		$sql = "SELECT * FROM `wxch_point_record` WHERE `point_name` = '$keyword' AND `wxid` = '$fromUsername'";
		$record = $db -> getRow($sql);
		$num = $db -> getOne("SELECT `point_num` FROM `wxch_point` WHERE `point_name` = '$keyword'");
		$lasttime = time();
		if (empty($record)) {
			$dateline = time();
			$insert_sql = "INSERT INTO `wxch_point_record` (`wxid`, `point_name`, `num`, `lasttime`, `datelinie`) VALUES
('$fromUsername', '$keyword' , 1, $lasttime, $dateline);";
			$potin_name = $db -> getOne("SELECT `point_name` FROM `wxch_point` WHERE `point_name` = '$keyword'");
			if (!empty($potin_name)) {
				$db -> query($insert_sql);
				return true;
			}
			
		} else {

			$time = time();
			$lasttime_sql = "SELECT `lasttime` FROM `wxch_point_record` WHERE `point_name` = '$keyword' AND `wxid` = '$fromUsername'";
			$db_lasttime = $db -> getOne($lasttime_sql);
			if (($time - $db_lasttime) > (60 * 60 * 24)) {
				$update_sql = "UPDATE `wxch_point_record` SET `num` = 0,`lasttime` = '$lasttime' WHERE `wxid` ='$fromUsername';";
				$db -> query($update_sql);
			} 
			$record_num = $db -> getOne("SELECT `num` FROM `wxch_point_record` WHERE `point_name` = '$keyword' AND `wxid` = '$fromUsername'");
			if ($record_num < $num) {
				$update_sql = "UPDATE `wxch_point_record` SET `num` = `num`+1,`lasttime` = '$lasttime' WHERE `point_name` = '$keyword' AND `wxid` ='$fromUsername';";
				$db -> query($update_sql);
				return true;
			} else {
				return false;
			} 
		}
}
?>