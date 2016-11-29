<?php if ($this->_var['group_buy_goods']): ?>
<style type="text/css">
.picScroll2{margin:10px auto; text-align:center;}
.picScroll2 .bd ul{width:100%;  float:left; padding-top:10px;}
.picScroll2 .bd li{width:50%; float:left; font-size:14px; text-align:center;}
.picScroll2 .bd li a{-webkit-tap-highlight-color:rgba(0, 0, 0, 0); /* 取消链接高亮 */}
.picScroll2 .bd li img{width:150px; height:150px;}
.picScroll2 .hd{display:None}



.picScroll2 .bd1 ul{width:100%;  float:left; padding-top:10px;}
.picScroll2 .bd1 li{width:50%; float:left; font-size:14px; text-align:center;}
.picScroll2 .bd1 li a{-webkit-tap-highlight-color:rgba(0, 0, 0, 0); /* 取消链接高亮 */}
.picScroll2 .bd1 li img{width:150px; height:150px;}
.picScroll2 .hd{display:None}
</style>
<div class="blank2"></div>
<div class="item_show_box2 box1 region" style="overflow:hidden">

    <div id="picScroll2" class="picScroll2">
        <div class="hd">
            <ul></ul>
        </div>
        <div class="bd1">
            <ul>
                <?php $_from = $this->_var['group_buy_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['hot_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['hot_goods']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['hot_goods']['iteration']++;
?>
                <li><a href="<?php echo $this->_var['goods']['url']; ?>"><img src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['thumb']; ?>" /></a>
                <br/>
                <?php if ($this->_var['goods']['promote_price'] != ""): ?> 
                <span class="price_s"> <?php echo $this->_var['goods']['promote_price']; ?> </span> 
                <?php else: ?> 
				<font style="font-size:8px">团购价</font><?php echo $this->_var['goods']['last_price']; ?>
                
                <?php endif; ?>
                <br><?php echo sub_str(htmlspecialchars($this->_var['goods']['goods_name']),12); ?>
                </li>
                <?php if ($this->_foreach['hot_goods']['iteration'] % 2 == 0 && $this->_foreach['hot_goods']['iteration'] != $this->_foreach['hot_goods']['total']): ?></ul><ul><?php endif; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
    </div>

    <div class="position_a_lt">
      <div> </div>
      <a href="search.php?intro=hot">
      <p> 团购 </p>
      <p class="ico_04"> </p>
      </a> </div>
    <div class="position_a_rb">
      <div> </div>
      <a href="group_buy.php">
      <p class="ico_04_b"> </p>
      <p> 更多 </p>
      </a> </div>
  </div>

<script type="text/javascript">
TouchSlide({
    slideCell:"#picScroll2",
    titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
    autoPage:true, //自动分页
    pnLoop:"false", // 前后按钮不循环
    //switchLoad:"_src" //切换加载，真实图片路径为"_src" 
});
</script>
<?php endif; ?>