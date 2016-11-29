<!-- $Id: payment_edit.htm 14401 2008-04-15 02:41:32Z zhuwenyuan $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../data/static/js/utils.js,./js/validator.js')); ?>
<form action="payment.php" method="post">
<div class="main-div">
<table cellspacing="1" cellpadding="3" width="100%">
  <tr>
    <td class="label"><?php echo $this->_var['lang']['payment_name']; ?></td>
    <td><input name="pay_name" type="text" value="<?php echo htmlspecialchars($this->_var['pay']['pay_name']); ?>" size="40" /></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['payment_desc']; ?></td>
    <td><textarea name="pay_desc" cols="60" rows="8"><?php echo htmlspecialchars($this->_var['pay']['pay_desc']); ?></textarea></td>
  </tr>
  <?php $_from = $this->_var['pay']['pay_config']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'config');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['config']):
?>
  <tr>
    <td class="label">
      <?php if ($this->_var['config']['desc']): ?>
      <a href="javascript:showNotice('notice<?php echo $this->_var['config']['name']; ?>');" title="<?php echo $this->_var['lang']['form_notice']; ?>"><img src="images/notice.gif" width="16" height="16" border="0" alt="<?php echo $this->_var['lang']['form_notice']; ?>"></a>
      <?php endif; ?>
      <span class="label"><?php echo $this->_var['config']['label']; ?>
    </td>
    <td>
      <!-- <?php if ($this->_var['config']['type'] == "text"): ?> -->
      <input name="cfg_value[]" type="<?php echo $this->_var['config']['type']; ?>" value="<?php echo $this->_var['config']['value']; ?>" size="40" />
      <?php if ($this->_var['config']['name'] == "alipay_partner"): ?>
      <a href="javascript:void(0)" style="font-weight:bold; color:#F00;"  onclick="show();return false;"> &nbsp; <?php echo $this->_var['lang']['getPid']; ?></a>

      <?php endif; ?>
      <!-- <?php elseif ($this->_var['config']['type'] == "textarea"): ?> -->
      <textarea name="cfg_value[]" cols="80" rows="5"><?php echo $this->_var['config']['value']; ?></textarea>
      <!-- <?php elseif ($this->_var['config']['type'] == "select"): ?> -->
      <select name="cfg_value[]"><?php echo $this->html_options(array('options'=>$this->_var['config']['range'],'selected'=>$this->_var['config']['value'])); ?></select>
      <!-- <?php endif; ?> -->
      <input name="cfg_name[]" type="hidden" value="<?php echo $this->_var['config']['name']; ?>" />
      <input name="cfg_type[]" type="hidden" value="<?php echo $this->_var['config']['type']; ?>" />
      <input name="cfg_lang[]" type="hidden" value="<?php echo $this->_var['config']['lang']; ?>" />
      <?php if ($this->_var['config']['desc']): ?>
      <br /><span class="notice-span" <?php if ($this->_var['help_open']): ?>style="display:block" <?php else: ?> style="display:none" <?php endif; ?> id="notice<?php echo $this->_var['config']['name']; ?>"><?php echo $this->_var['config']['desc']; ?></span>
      <?php endif; ?>
      <!--the tenpay code -->
      <?php if ($this->_var['key'] == "0"): ?>
      <?php if ($_GET['code'] == "tenpay"): ?><input align=""type="button" value="<?php echo $this->_var['lang']['ctenpay']; ?>" onclick="javascript:window.open('<?php echo $this->_var['lang']['ctenpay_url']; ?>')"/>
      <?php elseif ($_GET['code'] == "tenpayc2c"): ?> <input align=""type="button" value="<?php echo $this->_var['lang']['ctenpay']; ?>" onclick="javascript:window.open('<?php echo $this->_var['lang']['ctenpayc2c_url']; ?>')"/>
      <?php endif; ?>
      
      <?php endif; ?>
      <!--the tenpay code -->
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['pay_fee']; ?></td>
    <td><?php if ($this->_var['pay']['is_cod']): ?><input name="pay_fee" type="hidden" value="<?php echo empty($this->_var['pay']['pay_fee']) ? '0' : $this->_var['pay']['pay_fee']; ?>" /><?php echo $this->_var['lang']['decide_by_ship']; ?>
        <?php else: ?><input name="pay_fee" type="text" value="<?php echo empty($this->_var['pay']['pay_fee']) ? '0' : $this->_var['pay']['pay_fee']; ?>" /><?php endif; ?>
    </td>

  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['payment_is_cod']; ?></td>
    <td><?php if ($this->_var['pay']['is_cod'] == "1"): ?><?php echo $this->_var['lang']['yes']; ?><?php else: ?><?php echo $this->_var['lang']['no']; ?><?php endif; ?></td>
  </tr>
  <tr>
    <td class="label"><?php echo $this->_var['lang']['payment_is_online']; ?></td>
    <td><?php if ($this->_var['pay']['is_online'] == "1"): ?><?php echo $this->_var['lang']['yes']; ?><?php else: ?><?php echo $this->_var['lang']['no']; ?><?php endif; ?></td>
  </tr>
  <tr align="center">
    <td colspan="2">
      <input type="hidden"  name="pay_id"       value="<?php echo $this->_var['pay']['pay_id']; ?>" />
      <input type="hidden"  name="pay_code"     value="<?php echo $this->_var['pay']['pay_code']; ?>" />
      <input type="hidden"  name="is_cod"       value="<?php echo $this->_var['pay']['is_cod']; ?>" />
      <input type="hidden"  name="is_online"    value="<?php echo $this->_var['pay']['is_online']; ?>" />
      <input type="submit" class="button" name="Submit"       value="<?php echo $this->_var['lang']['button_submit']; ?>" />
      <input type="reset" class="button"  name="Reset"        value="<?php echo $this->_var['lang']['button_reset']; ?>" />
    </td>
  </tr>
</table>
</div>
</form>
<script type="text/javascript">
<!--

function show()
{
    document.getElementById('alipay_get_pidkey_box').style.display='block';
}
function close_t()
{
    document.getElementById('alipay_get_pidkey_box').style.display='none';
}

//-->
</script>
<?php if ($_GET['code'] == "alipay"): ?>
<div id="alipay_get_pidkey_box" class="alipay_get_pidkey_box" style="display:none; left: 663.5px; top: 330px;">
    <h2><?php echo $this->_var['lang']['alipay_pay_method']; ?></h2>
    <a href="javascript:void(0);" class="close_acp_info" onclick="close_t();return false;"><img alt="close" src="images/close.gif" border="0"></a>
    <table width="" cellspacing="0" cellpadding="0">
        <tbody>
        <tr><td align="left" height="26"><a target="_blank" href="https://b.alipay.com/order/pidKey.htm?pid=2088101540396621&product=dualpay"><b class="cWhite"><?php echo $this->_var['lang']['dualpay']; ?></b></a></td></tr>
        <tr><td align="left" height="26"><a target="_blank" href="https://b.alipay.com/order/pidKey.htm?pid=2088101540396621&product=fastpay"><b class="cWhite"><?php echo $this->_var['lang']['fastpay']; ?></b></a></td></tr>
        <tr><td align="left" height="26"><a target="_blank" href="https://b.alipay.com/order/pidKey.htm?pid=2088101540396621&product=escrow"><b class="cWhite"><?php echo $this->_var['lang']['escrow']; ?></b></a></td></tr>
  </tbody></table>
</div>
<?php endif; ?>
<script type="Text/Javascript" language="JavaScript">
<!--

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>