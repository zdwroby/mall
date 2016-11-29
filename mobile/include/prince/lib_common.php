<?php
//decode by QQ:270656184 http://www.yunlu99.com/
error_reporting(0);

/**
 * 取得云购活动信息
 * @param   int $lucky_buy_id 云购活动id
 * @param   int $current_num 本次购买数量（计算当前价时要加上的数量）
 * @return  array
 *                  status          状态：
 */
function lucky_buy_info($lucky_buy_id, $current_num = 0)
{
	/* 取得云购活动信息 */
	$lucky_buy_id = intval($lucky_buy_id);
	$sql = "SELECT *, act_id AS lucky_buy_id, act_desc AS lucky_buy_desc, start_time AS start_date, end_time AS end_date " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') .
		"WHERE act_id = '$lucky_buy_id' " .
		"AND act_type = '" . GAT_LUCKY_BUY . "'";
	$lucky_buy = $GLOBALS['db']->getRow($sql);

	/* 如果为空，返回空数组 */
	if (empty($lucky_buy)) {
		return array();
	}

	$ext_info = unserialize($lucky_buy['ext_info']);
	$lucky_buy = array_merge($lucky_buy, $ext_info);

	/* 格式化时间 */
	$lucky_buy['formated_start_date'] = local_date('Y-m-d H:i', $lucky_buy['start_time']);
	$lucky_buy['formated_end_date'] = local_date('Y-m-d H:i', $lucky_buy['end_time']);

	/* 格式化保证金 */
	$lucky_buy['formated_deposit'] = price_format($lucky_buy['deposit'], false);

	/* 处理价格阶梯 */
	$price_ladder = $lucky_buy['price_ladder'];
	if (!is_array($price_ladder) || empty($price_ladder)) {
		$price_ladder = array(array('amount' => 0, 'price' => 0));
	} else {
		foreach ($price_ladder as $key => $amount_price) {
			$price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
		}
	}
	$lucky_buy['price_ladder'] = $price_ladder;

	/* 统计信息 */
	$stat = lucky_buy_stat($lucky_buy_id, $lucky_buy['deposit']);
	$lucky_buy = array_merge($lucky_buy, $stat);

	/* 计算当前价 */
	$cur_price = $price_ladder[0]['price']; // 初始化
	$cur_amount = $stat['valid_goods'] + $current_num; // 当前数量
	foreach ($price_ladder as $amount_price) {
		if ($cur_amount >= $amount_price['amount']) {
			$cur_price = $amount_price['price'];
		} else {
			break;
		}
	}
	$lucky_buy['cur_price'] = $cur_price;
	$lucky_buy['formated_cur_price'] = price_format($cur_price, false);

	/* 最终价 */
	$lucky_buy['trans_price'] = $lucky_buy['cur_price'];
	$lucky_buy['formated_trans_price'] = $lucky_buy['formated_cur_price'];
	$lucky_buy['trans_amount'] = $lucky_buy['valid_goods'];

	/* 状态 */
	$lucky_buy['status_no'] = lucky_buy_status($lucky_buy);
	if (isset($GLOBALS['_LANG']['gbs'][$lucky_buy['status']])) {
		$lucky_buy['status_desc'] = $GLOBALS['_LANG']['gbs'][$lucky_buy['status']];
	}

	$lucky_buy['start_time'] = $lucky_buy['formated_start_date'];
	$lucky_buy['end_time'] = $lucky_buy['formated_end_date'];

	return $lucky_buy;
}

/*
 * 取得某云购活动统计信息
 * @param   int     $lucky_buy_id   云购活动id
 * @param   float   $deposit        保证金
 * @return  array   统计信息
 *                  total_order     总订单数
 *                  total_goods     总商品数
 *                  valid_order     有效订单数
 *                  valid_goods     有效商品数
 */
function lucky_buy_stat($lucky_buy_id, $deposit)
{
	$lucky_buy_id = intval($lucky_buy_id);

	/* 取得云购活动商品ID */
	$sql = "SELECT goods_id " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') .
		"WHERE act_id = '$lucky_buy_id' " .
		"AND act_type = '" . GAT_LUCKY_BUY . "'";
	$lucky_buy_goods_id = $GLOBALS['db']->getOne($sql);

	/* 取得总订单数和总商品数 */
	$sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
		"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
		$GLOBALS['ecs']->table('order_goods') . " AS g " .
		" WHERE o.order_id = g.order_id " .
		"AND o.extension_code = 'lucky_buy' " .
		"AND o.extension_id = '$lucky_buy_id' " .
		"AND g.goods_id = '$lucky_buy_goods_id' " .
		"AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
	$stat = $GLOBALS['db']->getRow($sql);
	if ($stat['total_order'] == 0) {
		$stat['total_goods'] = 0;
	}

	/* 取得有效订单数和有效商品数 */
	$deposit = floatval($deposit);
	if ($deposit > 0 && $stat['total_order'] > 0) {
		$sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
		$row = $GLOBALS['db']->getRow($sql);
		$stat['valid_order'] = $row['total_order'];
		if ($stat['valid_order'] == 0) {
			$stat['valid_goods'] = 0;
		} else {
			$stat['valid_goods'] = $row['total_goods'];
		}
	} else {
		$stat['valid_order'] = $stat['total_order'];
		$stat['valid_goods'] = $stat['total_goods'];
	}

	return $stat;
}

/**
 * 获得云购的状态
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function lucky_buy_status($lucky_buy)
{
	$now = gmtime();
	if ($lucky_buy['is_finished'] == 0) {
		/* 未处理 */
		if ($now < $lucky_buy['start_time']) {
			$status = GBS_PRE_START;
		} elseif ($now > $lucky_buy['end_time']) {
			$status = GBS_FINISHED;
		} else {
			$status = GBS_UNDER_WAY;
		}
	} elseif ($lucky_buy['is_finished'] == 1) {
		/* 已结束 */
		$status = 2;
	}


	return $status;
}


/* 取得用户云购活动总数 */
function user_lucky_buy_count()
{
	$sql = "SELECT COUNT(DISTINCT order_id) " .
		"FROM " . $GLOBALS['ecs']->table('lucky_buy_detail') .
		"WHERE user_id  = '" . $_SESSION['user_id'] . "' ";

	return $GLOBALS['db']->getOne($sql);
}


function get_lucky_buy_detail()
{
	$filter['act_id'] = empty($_REQUEST['act_id']) ? 0 : intval($_REQUEST['act_id']);
	$filter['schedule_id'] = empty($_REQUEST['schedule_id']) ? 0 : intval($_REQUEST['schedule_id']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'used_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = (empty($filter['act_id']) || empty($filter['schedule_id'])) ? '' : " WHERE act_id='$filter[act_id]' and schedule_id='$filter[schedule_id]'";

	/* 获得记录总数以及总页数 */
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('lucky_buy_detail') . $where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	$filter = page_and_size($filter);

	/* 获得活动数据 */
	$sql = "SELECT s.* " .
		" FROM " . $GLOBALS['ecs']->table('lucky_buy_detail') . " AS s " .
		$where .
		" ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'] .
		" LIMIT " . $filter['start'] . ", " . $filter['page_size'];
	$row = $GLOBALS['db']->getAll($sql);

	foreach ($row AS $key => $val) {
		$row[$key]['create_time'] = local_date('Y-m-d H:i', $val['create_time']);
		$row[$key]['used_time'] = local_date('Y-m-d H:i', $val['used_time']);
	}

	$arr = array('info' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}

/**
 * 返回活动详细列表
 *
 * @access  public
 *
 * @return array
 */
function get_lucky_buy($id)
{
	$filter['act_id'] = empty($_REQUEST['act_id']) ? 0 : intval($_REQUEST['act_id']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'schedule_id' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = (empty($filter['act_id'])) ? '' : " WHERE act_id='$filter[act_id]' ";

	/* 获得记录总数以及总页数 */
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('lucky_buy') . $where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	$filter = page_and_size($filter);

	/* 获得活动数据 */
	$sql = "SELECT * " .
		" FROM " . $GLOBALS['ecs']->table('lucky_buy') . $where .
		" ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'] .
		" LIMIT " . $filter['start'] . ", " . $filter['page_size'];
	$row = $GLOBALS['db']->getAll($sql);

	foreach ($row AS $key => $val) {

		$row[$key]['start_time'] = local_date('Y-m-d H:i', $val['start_time']);
		$row[$key]['end_time'] = local_date('Y-m-d H:i', $val['end_time'] > 0 ? $val['end_time'] : 0);

	}

	$arr = array('info' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}


/**
 * 取得云购活动数量
 * @return  int
 */
function lucky_buy_count()
{
	$now = gmtime();
	$sql = "SELECT COUNT(*) " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') .
		"WHERE act_type = '" . GAT_LUCKY_BUY . "' " .
		"AND start_time <= '$now' AND end_time >= '$now' AND is_finished < 1";

	return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某页的云购活动
 * @param   int $size 每页记录数
 * @param   int $page 当前页
 * @return  array
 */
function lucky_buy_list($size, $page)
{
	$lucky_buy_list = array();
	$lucky_buy_list['finished'] = $lucky_buy_list['finished'] = array();

	$now = gmtime();
	$sql = "SELECT a.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS a " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON a.goods_id = g.goods_id " .
		"WHERE a.act_type = '" . GAT_LUCKY_BUY . "' " .
		"AND a.start_time <= '$now' AND a.end_time >= '$now' AND a.is_finished < 1 ORDER BY a.act_id DESC";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
	while ($row = $GLOBALS['db']->fetchRow($res)) {
		$ext_info = unserialize($row['ext_info']);
		$lucky_buy = array_merge($row, $ext_info);
		$lucky_buy['status_no'] = lucky_buy_status($lucky_buy);

		$lucky_buy['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $lucky_buy['start_time']);
		$lucky_buy['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $lucky_buy['end_time']);
		$lucky_buy['formated_start_price'] = price_format($lucky_buy['start_price']);
		$lucky_buy['formated_end_price'] = price_format($lucky_buy['end_price']);
		$lucky_buy['formated_deposit'] = price_format($lucky_buy['deposit']);
		$lucky_buy['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$lucky_buy['url'] = 'lucky_buy.php?act=view&act_id=' . $row['act_id'] . '&u=' . $_SESSION['user_id'];
		$lucky_buy['shop_price'] = price_format($row['shop_price']);

		if ($lucky_buy['status_no'] < 2) {
			$lucky_buy_list['under_way'][] = $lucky_buy;
		} else {
			$lucky_buy_list['finished'][] = $lucky_buy;
		}
	}

	$lucky_buy_list = @array_merge($lucky_buy_list['under_way'], $lucky_buy_list['finished']);

	return $lucky_buy_list;
}

/**
 * 取得某页的云购活动
 * @param   int $size 每页记录数
 * @param   int $page 当前页
 * @return  array
 */
function lucky_buy_user_list($size, $page, $act_user)
{
	$lucky_buy_list = array();

	$now = gmtime();
	$sql = "SELECT DISTINCT lbd.order_id ,lb.status AS luck_buy_status,lb.*,ga.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb  " .
		"FROM  " . $GLOBALS['ecs']->table('lucky_buy_detail') . " AS lbd  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('lucky_buy') . " AS lb ON lbd.lucky_buy_id   = lb.lucky_buy_id   " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods_activity') . " AS ga ON lbd.act_id  = ga.act_id  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON ga.goods_id = g.goods_id " .
		"WHERE lbd.user_id=" . $_SESSION['user_id'] . " and lbd.user_id >0 ORDER BY lbd.order_id DESC";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
	while ($row = $GLOBALS['db']->fetchRow($res)) {
		$ext_info = unserialize($row['ext_info']);
		$lucky_buy = array_merge($row, $ext_info);
		$lucky_buy['status_no'] = lucky_buy_status($lucky_buy);

		$lucky_buy['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $lucky_buy['start_time']);
		$lucky_buy['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $lucky_buy['end_time']);
		$lucky_buy['formated_start_price'] = price_format($lucky_buy['start_price']);
		$lucky_buy['formated_end_price'] = price_format($lucky_buy['end_price']);
		$lucky_buy['formated_deposit'] = price_format($lucky_buy['deposit']);
		$lucky_buy['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$lucky_buy['shop_price'] = price_format($row['shop_price']);

		$lucky_buy['status'] = $row['luck_buy_status'];

		$lucky_buy_list[] = $lucky_buy;


	}

	return $lucky_buy_list;
}

/**
 * 取得云购活动记录
 * @param   int $size 每页记录数
 * @param   int $page 当前页
 * @return  array
 */
function lucky_buy_detail($size, $page, $lucky_buy_id)
{
	$lucky_buy_detail = array();

	$now = gmtime();
	$sql = "SELECT user_id ,used_time, user_name,user_head,used_time,count(code) as total " .
		"  FROM  " . $GLOBALS['ecs']->table('lucky_buy_detail') .
		"  WHERE lucky_buy_id=" . $lucky_buy_id . " and user_id >0 GROUP BY user_id,order_id ORDER BY used_time DESC";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
	while ($row = $GLOBALS['db']->fetchRow($res)) {
		$lucky_buy_detail[] = $row;
	}

	return $lucky_buy_detail;
}


/**
 * 取得拼团活动信息
 * @param   int $pintuan_id 拼团活动id
 * @param   int $current_num 本次购买数量（计算当前价时要加上的数量）
 * @return  array
 *                  status          状态：
 */
function pintuan_info($pintuan_id, $current_num = 0)
{
	/* 取得拼团活动信息 */
	$pintuan_id = intval($pintuan_id);
	$sql = "SELECT *, act_id AS pintuan_id, act_desc AS pintuan_desc, start_time AS start_date, end_time AS end_date " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') .
		"WHERE act_id = '$pintuan_id' " .
		"AND act_type = '" . GAT_PINTUAN . "'";
	$pintuan = $GLOBALS['db']->getRow($sql);

	/* 如果为空，返回空数组 */
	if (empty($pintuan)) {
		return array();
	}

	$ext_info = unserialize($pintuan['ext_info']);
	$pintuan = array_merge($pintuan, $ext_info);

	/* 格式化时间 */
	$pintuan['formated_start_date'] = local_date('Y-m-d H:i', $pintuan['start_time']);
	$pintuan['formated_end_date'] = local_date('Y-m-d H:i', $pintuan['end_time']);

	/* 格式化保证金 */
	$pintuan['formated_deposit'] = price_format($pintuan['deposit'], false);


	/* 处理价格阶梯 */
	$pintuan['org_price_ladder'] = $pintuan['price_ladder'];
	$price_ladder = $pintuan['price_ladder'];
	$i = 0;
	if (!is_array($price_ladder) || empty($price_ladder)) {
		$price_ladder = array(array('amount' => 0, 'price' => 0));
	} else {
		foreach ($price_ladder as $key => $amount_price) {
			//$price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
			$price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
			$i = $i + 1;
		}
	}
	$pintuan['price_ladder'] = $price_ladder;
	$pintuan['ladder_amount'] = $i;

	/* 统计信息 */
	$stat = pintuan_stat($pintuan_id, $pintuan['deposit']);
	$pintuan = array_merge($pintuan, $stat);

	/* 计算当前价 */
	$cur_price = $price_ladder[0]['price']; // 初始化
	$cur_amount = $stat['valid_goods'] + $current_num; // 当前数量
	foreach ($price_ladder as $amount_price) {
		if ($cur_amount >= $amount_price['amount']) {
			$cur_price = $amount_price['price'];
		} else {
			break;
		}
	}
	$pintuan['cur_price'] = $cur_price;
	$pintuan['formated_cur_price'] = price_format($cur_price, false);

	/* 最终价 */
	$pintuan['trans_price'] = $pintuan['cur_price'];
	$pintuan['formated_trans_price'] = $pintuan['formated_cur_price'];
	$pintuan['trans_amount'] = $pintuan['valid_goods'];

	/* 状态 */
	$pintuan['status'] = pintuan_status($pintuan);
	if (isset($GLOBALS['_LANG']['gbs'][$pintuan['status']])) {
		$pintuan['status_desc'] = $GLOBALS['_LANG']['gbs'][$pintuan['status']];
	}

	$pintuan['start_time'] = $pintuan['formated_start_date'];
	$pintuan['end_time'] = $pintuan['formated_end_date'];

	return $pintuan;
}

/*
 * 取得某拼团活动统计信息
 * @param   int     $pintuan_id   拼团活动id
 * @param   float   $deposit        保证金
 * @return  array   统计信息
 *                  total_order     总订单数
 *                  total_goods     总商品数
 *                  valid_order     有效订单数
 *                  valid_goods     有效商品数
 */
function pintuan_stat($pintuan_id, $deposit)
{
	$pintuan_id = intval($pintuan_id);

	/* 取得拼团活动商品ID */
	$sql = "SELECT goods_id " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') .
		"WHERE act_id = '$pintuan_id' " .
		"AND act_type = '" . GAT_PINTUAN . "'";
	$pintuan_goods_id = $GLOBALS['db']->getOne($sql);

	/* 取得总订单数和总商品数 */
	$sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
		"FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
		$GLOBALS['ecs']->table('order_goods') . " AS g " .
		" WHERE o.order_id = g.order_id " .
		"AND o.extension_code = 'pintuan' " .
		"AND o.extension_id = '$pintuan_id' " .
		"AND g.goods_id = '$pintuan_goods_id' " .
		"AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
	$stat = $GLOBALS['db']->getRow($sql);
	if ($stat['total_order'] == 0) {
		$stat['total_goods'] = 0;
	}

	/* 取得有效订单数和有效商品数 */
	$deposit = floatval($deposit);
	if ($deposit > 0 && $stat['total_order'] > 0) {
		$sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
		$row = $GLOBALS['db']->getRow($sql);
		$stat['valid_order'] = $row['total_order'];
		if ($stat['valid_order'] == 0) {
			$stat['valid_goods'] = 0;
		} else {
			$stat['valid_goods'] = $row['total_goods'];
		}
	} else {
		$stat['valid_order'] = $stat['total_order'];
		$stat['valid_goods'] = $stat['total_goods'];
	}

	return $stat;
}

/**
 * 获得拼团的状态
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function pintuan_status($pintuan)
{
	$now = gmtime();
	if ($pintuan['is_finished'] == 0) {
		/* 未处理 */
		if ($now < $pintuan['start_time']) {
			$status = GBS_PRE_START;
		} elseif ($now > $pintuan['end_time']) {
			$status = GBS_FINISHED;
		} else {
			if ($pintuan['restrict_amount'] == 0 || $pintuan['valid_goods'] < $pintuan['restrict_amount']) {
				$status = GBS_UNDER_WAY;
			} else {
				$status = GBS_FINISHED;
			}
		}
	} elseif ($pintuan['is_finished'] == GBS_SUCCEED) {
		/* 已处理，拼团成功 */
		$status = GBS_SUCCEED;
	} elseif ($pintuan['is_finished'] == GBS_FAIL) {
		/* 已处理，拼团失败 */
		$status = GBS_FAIL;
	}

	return $status;
}

/**
 *
 * @access  public
 */
function update_pintuan_info($pt_id)
{

	//处理拼团数据  Start
	$now = gmtime();
	$sql = "SELECT a.* " .
		"FROM " . $GLOBALS['ecs']->table('pintuan') . " AS a " .
		"WHERE status=0  " .
		"ORDER BY a.create_time asc ";
	$row = $GLOBALS['db']->getAll($sql);

	foreach ($row AS $key => $val) {
		if ($val['create_succeed'] == 1) {//处理开团成功的拼团及订单

			if ($val['available_people'] == 0) {// 所需人数剩余0 开团成功
				$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pintuan') . ' SET status =1 ' .
					"WHERE pt_id = '" . $val['pt_id'] . "'";
				$GLOBALS['db']->query($sql);
			} else {//所需人数大于零
				$sql = "SELECT count(*) " .
					"FROM  " . $GLOBALS['ecs']->table('pintuan_orders') . " AS pto  " .
					"LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON pto.order_id    = o.order_id    " .
					"WHERE pto.pt_id=" . $val['pt_id'] .
					"  and o.pay_status =2 ";
				$valid_orders = $GLOBALS['db']->getOne($sql);
				$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pintuan') . ' SET `available_people` =`need_people`-' . $valid_orders .
					" WHERE pt_id = '" . $val['pt_id'] . "'";
				$GLOBALS['db']->query($sql);
				if ($val['need_people'] <= $valid_orders) {
					$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pintuan') . ' SET status =1 ' .
						"WHERE pt_id = '" . $val['pt_id'] . "'";
					$GLOBALS['db']->query($sql);
				} else {
					$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pintuan') . ' SET status =2 ' .
						"WHERE pt_id = '" . $val['pt_id'] . "' and end_time<$now ";
					$GLOBALS['db']->query($sql);
				}
			}

		} else {//处理开团中的拼团及订单
			if ($val['end_time'] > $now) {//未开团 未超时
				$sql = "SELECT pto.*,o.order_status,o.shipping_status,o.pay_status " .
					"FROM  " . $GLOBALS['ecs']->table('pintuan_orders') . " AS pto  " .
					"LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON pto.order_id    = o.order_id    " .
					"WHERE pto.pt_id=" . $val['pt_id'] . " and pto.follow_user=pto.act_user and o.pay_status =2";
				$act_user_order = $GLOBALS['db']->getRow($sql);
				if ($act_user_order) {
					$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pintuan') . ' SET create_succeed =1 ' .
						"WHERE pt_id = '" . $val['pt_id'] . "'";
					$GLOBALS['db']->query($sql);
				}
			} else {//未开团，已超时
				$sql = 'UPDATE ' . $GLOBALS['ecs']->table('pintuan') . ' SET status =2 ' .
					"WHERE pt_id = '" . $val['pt_id'] . "'";
				$GLOBALS['db']->query($sql);
			}
		}

	}
	//处理拼团数据  End

	//拼团订单数据  Start
	$sql = "SELECT pto.order_id " .
		"FROM  " . $GLOBALS['ecs']->table('pintuan') . " AS pt  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('pintuan_orders') . " AS pto ON pto.pt_id    = pt.pt_id    " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON pto.order_id    = o.order_id    " .
		"WHERE pt.status!=0 AND o.pay_status <2 and order_status<2 ";
	$row = $GLOBALS['db']->getAll($sql);
	foreach ($row AS $key => $val) {
		$sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') . ' SET order_status =2 ' .
			"WHERE order_id = '" . $val['order_id'] . "'";
		$GLOBALS['db']->query($sql);
	}

	//拼团订单数据  End


}

/* 取得拼团活动总数 */
function pintuan_count()
{
	$now = gmtime();
	$sql = "SELECT COUNT(*) " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') .
		"WHERE act_type = '" . GAT_PINTUAN . "' " .
		"AND start_time <= '$now' AND is_finished < 3";

	return $GLOBALS['db']->getOne($sql);
}

/* 取得用户拼团活动总数 */
function user_pintuan_count()
{
	$sql = "SELECT COUNT(*) " .
		"FROM " . $GLOBALS['ecs']->table('pintuan_orders') .
		"WHERE follow_user  = '" . $_SESSION['user_id'] . "' ";

	return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某页的所有拼团活动
 * @param   int $size 每页记录数
 * @param   int $page 当前页
 * @return  array
 */
function pintuan_list($size, $page)
{
	/* 取得拼团活动 */
	$pt_list = array();
	$now = gmtime();
	$sql = "SELECT b.*, IFNULL(g.goods_thumb, '') AS goods_thumb, g.*,b.act_id AS pintuan_id, " .
		"b.start_time AS start_date, b.end_time AS end_date " .
		"FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS b " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON b.goods_id = g.goods_id " .
		"WHERE b.act_type = '" . GAT_PINTUAN . "' " .
		"AND b.start_time <= '$now' AND b.end_time > '$now'  ORDER BY b.act_id DESC";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
	while ($pintuan = $GLOBALS['db']->fetchRow($res)) {
		$ext_info = unserialize($pintuan['ext_info']);
		$pintuan = array_merge($pintuan, $ext_info);

		/* 格式化时间 */
		$pintuan['formated_start_date'] = local_date($GLOBALS['_CFG']['time_format'], $pintuan['start_date']);
		$pintuan['formated_end_date'] = local_date($GLOBALS['_CFG']['time_format'], $pintuan['end_date']);

		/* 格式化保证金 */
		$pintuan['formated_deposit'] = price_format($pintuan['deposit'], false);
		/* 处理价格阶梯 */
		$price_ladder = $pintuan['price_ladder'];
		$i = 0;
		if (!is_array($price_ladder) || empty($price_ladder)) {
			$price_ladder = array(array('amount' => 0, 'price' => 0));
		} else {
			foreach ($price_ladder as $key => $amount_price) {
				$price_ladder[$key]['formated_price'] = price_format($amount_price['price']);
				$i = $i + 1;
			}
		}
		$pintuan['price_ladder'] = $price_ladder;
		$pintuan['lowest_price'] = price_format(get_lowest_price($price_ladder));
		$pintuan['lowest_amount'] = get_lowest_amount($price_ladder);
		$pintuan['ladder_amount'] = $i;
		$pintuan['sold'] = $pintuan['virtual_sold'] + $pintuan['sales_count'];

		/* 处理图片 */
		if (empty($pintuan['goods_thumb'])) {
			$pintuan['goods_thumb'] = get_image_path($pintuan['goods_id'], $pintuan['goods_thumb'], true);
		}
		/* 处理链接 */
		$pintuan['url'] = 'pintuan.php?act=view&act_id=' . $pintuan['pintuan_id'] . '&u=' . $_SESSION['user_id'];
		/* 加入数组 */
		$pt_list[] = $pintuan;
	}

	return $pt_list;
}

/**
 * 取得某用户的所有拼团活动
 * @param   int $size 每页记录数
 * @param   int $page 当前页
 * @return  array
 */
function pintuan_user_list($size, $page)
{
	/* 取得拼团活动 */
	$pt_list = array();
	$now = gmtime();

	$sql = "SELECT ga.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb, pto.order_id ,pt.status,pt.need_people,pt.pt_id,pt.price as pt_price " .
		"FROM  " . $GLOBALS['ecs']->table('pintuan_orders') . " AS pto  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('pintuan') . " AS pt ON pto.pt_id   = pt.pt_id   " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods_activity') . " AS ga ON pt.act_id  = ga.act_id  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON ga.goods_id = g.goods_id " .
		"WHERE pto.follow_user=" . $_SESSION['user_id'] . "  ORDER BY pto.order_id DESC";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
	while ($pintuan = $GLOBALS['db']->fetchRow($res)) {
		$ext_info = unserialize($pintuan['ext_info']);
		$pintuan = array_merge($pintuan, $ext_info);

		/* 格式化时间 */
		$pintuan['formated_start_date'] = local_date($GLOBALS['_CFG']['time_format'], $pintuan['start_date']);
		$pintuan['formated_end_date'] = local_date($GLOBALS['_CFG']['time_format'], $pintuan['end_date']);
		$pintuan['price'] = price_format($pintuan['pt_price'], false);

		/* 格式化保证金 */
		$pintuan['formated_deposit'] = price_format($pintuan['deposit'], false);
		/* 处理价格阶梯 */
		$price_ladder = $pintuan['price_ladder'];
		$i = 0;
		if (!is_array($price_ladder) || empty($price_ladder)) {
			$price_ladder = array(array('amount' => 0, 'price' => 0));
		} else {
			foreach ($price_ladder as $key => $amount_price) {
				$price_ladder[$key]['formated_price'] = price_format($amount_price['price']);
				$i = $i + 1;
			}
		}
		$pintuan['price_ladder'] = $price_ladder;
		$pintuan['lowest_price'] = price_format(get_lowest_price($price_ladder));
		$pintuan['lowest_amount'] = get_lowest_amount($price_ladder);
		$pintuan['ladder_amount'] = $i;
		$pintuan['sold'] = $pintuan['virtual_sold'] + $pintuan['sales_count'];

		/* 处理图片 */
		if (empty($pintuan['goods_thumb'])) {
			$pintuan['goods_thumb'] = get_image_path($pintuan['goods_id'], $pintuan['goods_thumb'], true);
		}
		/* 处理链接 */
		$pintuan['url'] = 'pintuan.php?act=view&act_id=' . $pintuan['pintuan_id'] . '&u=' . $_SESSION['user_id'];
		/* 加入数组 */
		$pt_list[] = $pintuan;
	}

	return $pt_list;
}


function pintuan_detail_info($pintuan_id)
{
	$sql = "SELECT ga.*,IFNULL(g.goods_thumb, '') AS goods_thumb, pt.*,g.* " .
		"FROM  " . $GLOBALS['ecs']->table('pintuan') . " AS pt  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods_activity') . " AS ga ON pt.act_id  = ga.act_id  " .
		"LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON ga.goods_id = g.goods_id " .
		"WHERE pt.pt_id=" . $pintuan_id . "  ";
	$pintuan = $GLOBALS['db']->getRow($sql);
	$ext_info = unserialize($pintuan['ext_info']);
	$pintuan = array_merge($pintuan, $ext_info);

	/* 格式化时间 */
	$pintuan['create_time'] = local_date($GLOBALS['_CFG']['time_format'], $pintuan['create_time']);
	$pintuan['price'] = price_format($pintuan['price'], false);

	/* 处理图片 */
	if (empty($pintuan['goods_thumb'])) {
		$pintuan['goods_thumb'] = get_image_path($pintuan['goods_id'], $pintuan['goods_thumb'], true);
	}
	/* 处理链接 */
	$pintuan['url'] = 'pintuan.php?act=view&act_id=' . $pintuan['act_id'] . '&u=' . $_SESSION['user_id'];
	/* 加入数组 */

	return $pintuan;
}

function get_lowest_price($price_ladder)
{

	if (is_array($price_ladder)) {

		$aa = array();
		foreach ($price_ladder as $key => $value) {

			$aa[] = $value['price'];


		}
		sort($aa);

		return $aa[0];

	}

}

function get_lowest_amount($price_ladder)
{

	if (is_array($price_ladder)) {

		$aa = array();
		foreach ($price_ladder as $key => $value) {

			$aa[] = $value['amount'];


		}
		sort($aa);

		return $aa[0];

	}

}

/**
 * @param   int $act_id 活动id
 * @return  array
 */
function get_new_pintuan($act_id)
{
	$new_pintuan = array();
	$sql = "SELECT a.* " .
		"FROM " . $GLOBALS['ecs']->table('pintuan') . " AS a " .
		"WHERE act_id = '$act_id' and status=0 and create_succeed=1 " .
		"ORDER BY a.create_time desc LIMIT 10";
	$res = $GLOBALS['db']->query($sql);
	while ($row = $GLOBALS['db']->fetchRow($res)) {
		$row['create_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['create_time']);
		$row['price'] = price_format($row['price'], false);
		$new_pintuan[] = $row;
	}

	return $new_pintuan;
}


/**
 * 返回活动详细列表
 *
 * @access  public
 *
 * @return array
 */
function get_pintuan()
{
	$filter['act_id'] = empty($_REQUEST['act_id']) ? 0 : intval($_REQUEST['act_id']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'create_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = empty($filter['act_id']) ? '' : " WHERE act_id='$filter[act_id]' ";

	/* 获得记录总数以及总页数 */
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('pintuan') . $where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	$filter = page_and_size($filter);

	/* 获得活动数据 */
	$sql = "SELECT * " .
		" FROM " . $GLOBALS['ecs']->table('pintuan') . $where .
		" ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'] .
		" LIMIT " . $filter['start'] . ", " . $filter['page_size'];
	$row = $GLOBALS['db']->getAll($sql);

	foreach ($row AS $key => $val) {
		$row[$key]['create_time'] = local_date('Y-m-d H:i', $val['create_time']);
		$row[$key]['end_time'] = local_date('Y-m-d H:i', $val['end_time']);

	}

	$arr = array('pintuan' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}


/**
 * 返回活动详细列表
 *
 * @access  public
 *
 * @return array
 */
function get_pintuan_detail()
{
	$filter['pt_id'] = empty($_REQUEST['pt_id']) ? 0 : intval($_REQUEST['pt_id']);
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'follow_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = empty($filter['pt_id']) ? '' : " WHERE pt_id='$filter[pt_id]' ";

	/* 获得记录总数以及总页数 */
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('pintuan_orders') . $where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	$filter = page_and_size($filter);

	/* 获得活动数据 */
	$sql = "SELECT s.* " .
		" FROM " . $GLOBALS['ecs']->table('pintuan_orders') . " AS s " .
		$where .
		" ORDER by " . $filter['sort_by'] . " " . $filter['sort_order'] .
		" LIMIT " . $filter['start'] . ", " . $filter['page_size'];
	$row = $GLOBALS['db']->getAll($sql);

	foreach ($row AS $key => $val) {
		$row[$key]['follow_time'] = local_date('Y-m-d H:i', $val['follow_time']);

	}

	$arr = array('pintuan' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}

/**
 * 获取指定id pintuan 的信息
 */
function get_pintuan_info($id)
{
	global $ecs, $db, $_CFG;

	$sql = "SELECT act_id, act_name AS cut_name, goods_id, product_id, goods_name, start_time, end_time, act_desc, ext_info" .
		" FROM " . $GLOBALS['ecs']->table('goods_activity') .
		" WHERE act_id='$id' AND act_type = " . GAT_PINTUAN;

	$cut = $db->GetRow($sql);


	/* 将时间转成可阅读格式 */
	$cut['start_time'] = local_date('Y-m-d H:i', $cut['start_time']);
	$cut['end_time'] = local_date('Y-m-d H:i', $cut['end_time']);
	$row = unserialize($cut['ext_info']);
	unset($cut['ext_info']);
	if ($row) {
		foreach ($row as $key => $val) {
			$cut[$key] = $val;
		}
	}

	return $cut;
}

//拼团
function get_pintuan_by_ptid($pt_id)
{
	$sql = "SELECT pt.* " .
		" FROM  " . $GLOBALS['ecs']->table('pintuan') . " AS pt  " .
		" WHERE pt.pt_id=" . $pt_id . "  ";
	return $GLOBALS['db']->getRow($sql);
}

