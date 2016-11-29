<div class="filter" style="position:static; top:0px; width:100%;">
  <form method="GET" class="sort" name="listform">
    <ul class="filter-inner">
      <li class="<?php if ($this->_var['pager']['sort'] == 'goods_id' && $this->_var['pager']['order'] == 'DESC'): ?>filter-cur<?php endif; ?>"> <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=goods_id&order=DESC&keywords=<?php echo $this->_var['keywords']; ?>#goods_list&">综合</a> </li>
      <li class="<?php if ($this->_var['pager']['sort'] == 'click_count' && $this->_var['pager']['order'] == 'DESC'): ?>filter-cur<?php endif; ?>"> <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=click_count&order=DESC&keywords=<?php echo $this->_var['keywords']; ?>#goods_list">人气<i class="f-ico-arrow-d"></i></a> </li>
      <li class="<?php if ($this->_var['pager']['sort'] == 'sales_count' && $this->_var['pager']['order'] == 'DESC'): ?>filter-cur<?php endif; ?>"> <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=sales_count&order=DESC&keywords=<?php echo $this->_var['keywords']; ?>#goods_list">销量</a></li>
      <li class="<?php if ($this->_var['pager']['sort'] == 'shop_price'): ?>filter-cur<?php endif; ?>"> <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=shop_price&order=<?php if ($this->_var['pager']['sort'] == 'shop_price' && $this->_var['pager']['order'] == 'ASC'): ?>DESC<?php else: ?>ASC<?php endif; ?>&keywords=<?php echo $this->_var['keywords']; ?>#goods_list">价格 <span> <i class="f-ico-triangle-mt <?php if ($this->_var['pager']['sort'] == 'shop_price' && $this->_var['pager']['order'] == 'ASC'): ?> f-ico-triangle-slctd<?php endif; ?>"></i> <i class="f-ico-triangle-mb <?php if ($this->_var['pager']['sort'] == 'shop_price' && $this->_var['pager']['order'] == 'DESC'): ?>f-ico-triangle-slctd<?php endif; ?>"></i> </span> </a> </li>
      
    </ul>
    <input type="hidden" name="category" value="<?php echo $this->_var['category']; ?>" />
    <input type="hidden" name="display" value="<?php echo $this->_var['pager']['display']; ?>" id="display" />
    <input type="hidden" name="brand" value="<?php echo $this->_var['brand_id']; ?>" />
    <input type="hidden" name="price_min" value="<?php echo $this->_var['price_min']; ?>" />
    <input type="hidden" name="price_max" value="<?php echo $this->_var['price_max']; ?>" />
    <input type="hidden" name="filter_attr" value="<?php echo $this->_var['filter_attr']; ?>" />
    <input type="hidden" name="page" value="<?php echo $this->_var['pager']['page']; ?>" />
    <input type="hidden" name="sort" value="<?php echo $this->_var['pager']['sort']; ?>" />
    <input type="hidden" name="order" value="<?php echo $this->_var['pager']['order']; ?>" />
  </form>
</div>
   	<?php if ($this->_var['goods_list']): ?>
    <div id="J_ItemList" class="srp j_autoResponsive_container m-ks-autoResponsive-container m-animation list" style="opacity:1;">
      <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['goods_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods_list']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['goods_list']['iteration']++;
?> 
      <?php if ($this->_var['goods']['goods_id']): ?>
      <div id="more_element_1" class="product flex_in single_item">
        <div class="pro-inner">
          <div class="proImg-wrap"> <a href="<?php echo $this->_var['goods']['url']; ?>" > <img src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['goods_thumb']; ?>" alt="<?php echo $this->_var['goods']['goods_name']; ?>"> </a> </div>
          <div class="proInfo-wrap">
            <div class="proTitle"> <a href="<?php echo $this->_var['goods']['url']; ?>" ><?php echo $this->_var['goods']['goods_name']; ?></a> </div>
            <div class="proSKU"></div>
            <div class="proPrice"> 
              <?php if ($this->_var['goods']['promote_price'] != ""): ?> 
              <em><?php echo $this->_var['goods']['promote_price']; ?></em> 
              <?php else: ?> 
              <em><?php echo $this->_var['goods']['shop_price']; ?></em> 
              <?php endif; ?> 
            </div>
            <div class="proService"> <del><?php echo $this->_var['goods']['market_price']; ?></del></div>
            <div class="proSales">月销:<em><?php echo $this->_var['goods']['sales_count']; ?></em></div>
            <div class="proIcons"> 
			 <?php if ($this->_var['goods']['watermark_img']): ?> 
			<img width="55" height="16" src="<?php echo $this->_var['ectouch_themes']; ?>/images/<?php echo $this->_var['goods']['watermark_img']; ?>.png" alt="<?php echo $this->_var['goods']['goods_name']; ?>" />
			<?php endif; ?> 
			 </div>
          </div>
        </div>
      </div>
      <?php endif; ?> 
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    </div>
    <?php else: ?>
    <div id="J_ItemList" class="srp album flex-f-row" style="opacity:1;">
    <p>找不到匹配条件的商品哦~ ~</p>
    </div>
    <?php endif; ?>