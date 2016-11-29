<section class="list-pagination">
    <div class="pagenav-wrapper" id="J_PageNavWrap" style="">
      <div class="pagenav-content">
        <div class="pagenav" id="J_PageNav" style="display:flex; text-align:center;">
          <div class="<?php if ($this->_var['pager']['page_first']): ?>p-first<?php else: ?>p-prev p-gray<?php endif; ?>"> 
              <?php if ($this->_var['pager']['page_first']): ?>
              <a href="<?php echo $this->_var['pager']['page_first']; ?>">首页</a>
              <?php else: ?>
               <a  class="no">首页</a> 
              <?php endif; ?>
          </div>
          <div class="<?php if ($this->_var['pager']['page_prev']): ?>p-prev<?php else: ?>p-prev p-gray<?php endif; ?>"> 
              <?php if ($this->_var['pager']['page_prev']): ?>
              <a href="<?php echo $this->_var['pager']['page_prev']; ?>" >上一页</a>
              <?php else: ?>
              <a class="no">上一页</a>   
              <?php endif; ?>
            </div>
          <div class="pagenav-cur">
            <div class="pagenav-text"> <span><?php echo $this->_var['pager']['page']; ?>/<?php echo $this->_var['pager']['page_count']; ?></span> <i></i> </div>
            <select name="page" class="pagenav-select" onchange="window.location.href=this.options[this.selectedIndex].value" >
            <?php if ($this->_var['pager']['page_number']): ?>
            <?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>    
              <option value="<?php echo $this->_var['item']; ?>"><?php echo $this->_var['key']; ?></option>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
            </select>
          </div>
          <div class="<?php if ($this->_var['pager']['page_next']): ?>p-next<?php else: ?>p-next p-gray<?php endif; ?>" > 
          	<?php if ($this->_var['pager']['page_next']): ?>
              <a  href="<?php echo $this->_var['pager']['page_next']; ?>" >下一页</a>
              <?php else: ?>
              <a class="no">下一页</a> 
              <?php endif; ?>
          </div>
          <div class="<?php if ($this->_var['pager']['page_last']): ?>p-end<?php else: ?>p-end p-gray<?php endif; ?>">
           <?php if ($this->_var['pager']['page_last']): ?>
           <a href="<?php echo $this->_var['pager']['page_last']; ?>"><?php echo $this->_var['lang']['page_last']; ?></a>
           <?php else: ?>
           <a class="no">末页</a> 
           <?php endif; ?>
           </div>
        </div>
      </div>
    </div>
</section>