
<div style="height:50px; line-height:50px; clear:both;"></div>
<div class="v_nav">
<div class="vf_nav">
<ul>
<li> <a href="./">
    <i class="vf_1"></i>
    <span>首页</span></a></li>

<!--
	作者：zdwroby@sina.com
	时间：2016-11-29
	描述：分页修改链接
<li><a href="catalog.php">
    <i class="vf_3"></i>
    <span>分类</span></a></li>
<li>	
-->
<li><a href="cat_all.php">
    <i class="vf_3"></i>
    <span>分类</span></a></li>
<li>
<a href="flow.php">
   <em class="global-nav__nav-shop-cart-num" id="ECS_CARTINFO" style="right:9px;"><?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></em>
   <i class="vf_4"></i>
   <span>购物车</span>

   </a></li>
<li><a href="user.php">
    <i class="vf_5"></i>
    <span>我的</span></a></li>
</ul>
</div>
</div>




    