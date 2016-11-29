<!-- $Id: user_rank.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<!-- start ads list -->
<div class="list-div" id="listDiv">
<?php endif; ?>

<table cellspacing='1' id="list-table">
  <tr>
    
    <th><?php echo $this->_var['lang']['card_type_ct_id']; ?></th>
    <th><?php echo $this->_var['lang']['card_type_ct_name']; ?></th>
    <th><?php echo $this->_var['lang']['des']; ?></th>
     <th><?php echo $this->_var['lang']['handler']; ?></th>
  </tr>
  <?php $_from = $this->_var['card_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'card');if (count($_from)):
    foreach ($_from AS $this->_var['card']):
?>
  <tr>  
    <td class="first-cell"  align="center"><?php echo $this->_var['card']['ct_id']; ?></td>
    <td  align="center"><span onclick="listTable.edit(this, 'edit_ct_name', <?php echo $this->_var['card']['ct_id']; ?>)"><?php echo $this->_var['card']['ct_name']; ?></span></td>
     <td align="center"><span  onclick="listTable.edit(this, 'edit_user_money', <?php echo $this->_var['card']['id']; ?>)"><?php echo nl2br($this->_var['card']['des']); ?></span></td>
    <td align="center">
    <a href="user_card.php?act=send&ct_id=<?php echo $this->_var['card']['ct_id']; ?>" title="发卡">发卡</a> 
    &nbsp;
   <a href="user_card.php?act=list&ct_id=<?php echo $this->_var['card']['ct_id']; ?>"  title="查看">查看</a> 
   &nbsp; 
    <a href="user_card.php?act=ctedit&ct_id=<?php echo $this->_var['card']['ct_id']; ?>" title="编辑">编辑</a>&nbsp;
    <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['card']['ct_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>\n\r<?php echo $this->_var['lang']['ct_delete_confirm']; ?>','ctremove')" title="<?php echo $this->_var['lang']['remove']; ?>">删除</a>    &nbsp;
    <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['card']['ct_id']; ?>, '批量删除<?php echo $this->_var['card']['ct_name']; ?>所有卡','ctremovecard')" title="<?php echo $this->_var['lang']['remove']; ?>">批量删除<?php echo $this->_var['card']['ct_name']; ?>所有卡</a></td>
  </tr>
  <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="4"><?php echo $this->_var['lang']['no_user_card_type']; ?></td></tr>
  <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  <tr>
      <td align="right" nowrap="true" colspan="4">
      <?php echo $this->fetch('page.htm'); ?>
      </td>
  </tr>
  </table>

<?php if ($this->_var['full_page']): ?>
</div>
<!-- end user ranks list -->
<script type="text/javascript" language="JavaScript">
  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}
 /* 搜索文章 */
 function searchUserCard()
 {
    listTable.filter.card_no = Utils.trim(document.forms['searchForm'].elements['card_no'].value);
    listTable.filter.user_name = Utils.trim(document.forms['searchForm'].elements['user_name'].value);
    listTable.filter.page = 1;
    listTable.loadList();
 }
function confirm_bath()
{
  userItems = document.getElementsByName('checkboxes[]');

  cfm = '您确定要删除选定的会员卡吗？';

  return confirm(cfm);
}

//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>
