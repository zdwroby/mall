<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../data/static/js/utils.js,./js/listtable.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'./js/validator.js')); ?>
<div class="form-div">
<form method="post" action="affiliate.php">
<input type="radio" name="on" value="1" <?php if ($this->_var['config']['on'] == 1): ?> checked="true" <?php endif; ?> onClick="javascript:actDiv('separate','');actDiv('btnon','none');"><?php echo $this->_var['lang']['on']; ?>
<input type="radio" name="on" value="0" <?php if (! $this->_var['config']['on'] || $this->_var['config']['on'] == 0): ?> checked="true" <?php endif; ?> onClick="javascript:actDiv('separate','none');actDiv('btnon','');"><?php echo $this->_var['lang']['off']; ?>
<br><br>
<input type="hidden" name="act" value="on" />
<input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" id="btnon"/>
</form>
</div>
<div id="separate">
<div class="form-div">
<form method="post" action="affiliate.php">
            <table width="100%" border="0" cellspacing="0" cellpadding="4">
<!--
                <tr>
                    <td width="20%" align="right" class="label"><a href="javascript:showNotice('notice1');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>" /></a><?php echo $this->_var['lang']['expire']; ?> </td>
                    <td><input type="text" name="expire" maxlength="150" size="10" value="<?php echo $this->_var['config']['config']['expire']; ?>" />
                        <select name="expire_unit">
                            <?php echo $this->html_options(array('options'=>$this->_var['lang']['unit'],'selected'=>$this->_var['config']['config']['expire_unit'])); ?>
                        </select>
                        <br />
                        <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="notice1"><?php echo nl2br($this->_var['lang']['help_expire']); ?></span>                        
                        </td>
                </tr>
                <tr>
                    <td align="right" class="label"><a href="javascript:showNotice('notice2');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>" /></a><?php echo $this->_var['lang']['level_point_all']; ?> </td>
                    <td><input type="text" name="level_point_all" maxlength="150" size="10" value="<?php echo $this->_var['config']['config']['level_point_all']; ?>" />
                    <br />
                    <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="notice2"><?php echo nl2br($this->_var['lang']['help_lpa']); ?></span></td>
                </tr>

                <tr>
                    <td align="right" class="label"><a href="javascript:showNotice('notice4');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>" /></a><?php echo $this->_var['lang']['level_register_all']; ?></td>
                    <td><input type="text" name="level_register_all" maxlength="150" size="10" value="<?php echo $this->_var['config']['config']['level_register_all']; ?>" />
                    <br />
                    <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="notice4"><?php echo nl2br($this->_var['lang']['help_lra']); ?></span></td>
                </tr>
				
				-->
                <tr>
                    <td align="right" class="label"><a href="javascript:showNotice('notice5');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>" /></a>成为分销商积分标准</td>
                    <td><input type="text" name="level_register_up" maxlength="150" size="10" value="<?php echo $this->_var['config']['config']['level_register_up']; ?>" />
                    <br />
                    <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="notice5">等级积分大于此标准可以成为分销会员</span></td>

                </tr>
				<tr>
                    <td align="right" class="label">设置成为分销商模式</td>
                    <td> 
					<select name="ex_fenxiao_flag">
                            <?php echo $this->html_options(array('options'=>$this->_var['lang']['tianxin100'],'selected'=>$this->_var['config']['config']['ex_fenxiao_flag'])); ?>
                        </select>
                    <br />
                    <span class="notice-span" id="notice5">如果不是推荐关系会员，直接关注，默认上级为官方账号</span></td>

                </tr>
				 <tr>
                    <td align="right" class="label">设置官方推荐账号ID</td>
                    <td><input type="text" name="parent_id" maxlength="150" size="10" value="<?php echo $this->_var['config']['config']['parent_id']; ?>" />
                    <br />
                    <span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="notice5">如果不是推荐关系会员，直接关注，默认上级为官方账号</span></td>
				</tr>
                <tr>
                    <td align="right" class="label">顾客购买有提成</td>
                    <td> 
                    <input type="radio" name="ex_fenxiao_personal" value="0" <?php if ($this->_var['config']['config']['ex_fenxiao_personal'] == 0): ?>checked<?php endif; ?>>关闭
                    <input type="radio" name="ex_fenxiao_personal" value="1" <?php if ($this->_var['config']['config']['ex_fenxiao_personal'] == 1): ?>checked<?php endif; ?>>开启&nbsp&nbsp&nbsp&nbsp
                                                 双击改变金额比例：<span onclick="listTable.edit(this, 'edit_personal_money'); return false;"><?php if ($this->_var['config']['config']['level_money_personal'] == ''): ?>0<?php else: ?><?php echo $this->_var['config']['config']['level_money_personal']; ?><?php endif; ?></span>%&nbsp&nbsp&nbsp&nbsp
					双击改变积分比例：<span onclick="listTable.edit(this, 'edit_personal_point'); return false;"><?php if ($this->_var['config']['config']['level_point_personal'] == ''): ?>0<?php else: ?><?php echo $this->_var['config']['config']['level_point_personal']; ?><?php endif; ?></span>%&nbsp&nbsp&nbsp&nbsp
					双击改变最低购买金额：<span onclick="listTable.edit(this, 'edit_personal_lever_money'); return false;"><?php if ($this->_var['config']['config']['personal_lever_money'] == ''): ?>0<?php else: ?><?php echo $this->_var['config']['config']['personal_lever_money']; ?><?php endif; ?></span>元
					<br/>
                    <span class="notice-span" id="notice5">设置购买自己有提成</span></td>
                </tr>
				  <tr><td></td>
                    <td><input type="hidden" name="act" value="updata" /><input type="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" /></td>
                </tr>
            </table>
    </form>
</div>
<div class="list-div" id="listDiv">
<?php endif; ?>
<table cellspacing='1' cellpadding='3'>
	<tr>
		<th name="levels" ReadOnly="true" width="10%"><?php echo $this->_var['lang']['levels']; ?></th>
		<th name="level_point" Type="TextBox"><?php echo $this->_var['lang']['level_point']; ?></th>
		<th name="level_money" Type="TextBox"><?php echo $this->_var['lang']['level_money']; ?></th>
		<th Type="Button"><?php echo $this->_var['lang']['handler']; ?></th>
	</tr>
<?php $_from = $this->_var['config']['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');$this->_foreach['nav'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav']['total'] > 0):
    foreach ($_from AS $this->_var['val']):
        $this->_foreach['nav']['iteration']++;
?>
<tr align="center">
	<td><?php echo $this->_foreach['nav']['iteration']; ?></td>
	<td><span onclick="listTable.edit(this, 'edit_point', '<?php echo $this->_foreach['nav']['iteration']; ?>'); return false;"><?php echo $this->_var['val']['level_point']; ?></span></td>
	<td><span onclick="listTable.edit(this, 'edit_money', '<?php echo $this->_foreach['nav']['iteration']; ?>'); return false;"><?php echo $this->_var['val']['level_money']; ?></span></td>
	<td ><a href="javascript:confirm_redirect(lang_removeconfirm, 'affiliate.php?act=del&id=<?php echo $this->_foreach['nav']['iteration']; ?>')"><img style="border:0px;" src="images/no.gif" /></a></td>
</tr>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
<?php if ($this->_var['full_page']): ?>
</div>
</div>
<script type="Text/Javascript" language="JavaScript">
<!--
<?php if (! $this->_var['config']['on'] || $this->_var['config']['on'] == 0): ?>
actDiv('separate','none');
<?php else: ?>
actDiv('btnon','none');
<?php endif; ?>
<?php if ($this->_var['config']['config']['separate_by'] == 1): ?>
actDiv('listDiv','none');
<?php endif; ?>

var all_null = '<?php echo $this->_var['lang']['all_null']; ?>';

onload = function()
{
  // 开始检查订单
  startCheckOrder();
  cleanWhitespace(document.getElementById("listDiv"));
  if (document.getElementById("listDiv").childNodes[0].rows.length<6)
  {
    listTable.addRow(check);
  }
  
}
function check(frm)
{
  if (frm['level_point'].value == "" && frm['level_money'].value == "")
  {
     frm['level_point'].focus();
     alert(all_null);
     return false;  
  }
  
  return true;
}
function actDiv(divname, flag)
{
    document.getElementById(divname).style.display = flag;
}

//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>