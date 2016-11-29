<!DOCTYPE html >
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title><?php echo $this->_var['page_title']; ?></title>
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<link rel="stylesheet" type="text/css" href="themes/tianxin100/css/public.css"/>
<link rel="stylesheet" type="text/css" href="themes/tianxin100/css/goods.css"/>  
<script type="text/javascript" src="themes/tianxin100/js/jquery.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.json.js,transport_index.js')); ?>
<script type="text/javascript" src="themes/tianxin100/js/touchslider.dev.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'common.js')); ?>

</head>
<body>
<script type="text/javascript">
var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
</script>
<script language="javascript"> 
<!--
/*屏蔽所有的js错误*/
function killerrors() { 
return true; 
} 
window.onerror = killerrors; 
//-->
function tiaozhuan()
{ 
//var thisurl = window.location.href;
document.getElementById("share_form").submit();
}
</script>
<script type="text/javascript">
				/*第一种形式 第二种形式 更换显示样式*/
				function setGoodsTab(name,cursel,n){
					$('html,body').animate({'scrollTop':0},600);
				for(i=1;i<=n;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("user_"+name+"_"+i);
				menu.className=i==cursel?"on":"";
				con.style.display=i==cursel?"block":"none";
				}
				}
				</script>
<div class="main"> 
      
      <div class="tab_nav">
        <div class="header">
          <div class="h-left"><a class="sb-back" href="javascript:history.back(-1)" title="返回"></a></div>
          <div class="h-mid">
            <ul>
              <li><a href="javascript:;" class="tab_head on"   id="goods_ka1" onClick="setGoodsTab('goods_ka',1,3)">商品</a></li>
              <li><a href="javascript:;" class="tab_head" id="goods_ka2" onClick="setGoodsTab('goods_ka',2,3)">详情</a></li>
			  <li><a href="goods_pinglun.php?id=<?php echo $this->_var['goods_id']; ?>" class="tab_head" id="goods_ka3" >评价</a></li>
            </ul>
          </div>
          <div class="h-right">
            <aside class="top_bar">
            <div onClick="show_menu();$('#close_btn').addClass('hid');" id="show_more"><a href="javascript:;"></a> </div>
            <a href="flow.php" class="show_cart"><em class="global-nav__nav-shop-cart-num" id="ECS_CARTINFO"><?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></em></a>
            
            
            </aside>
          </div>
        </div>
      </div>
       	<?php echo $this->fetch('library/up_menu.lbi'); ?> 
     <form action="share_goods.php" method="post" id="share_form">
            <input type="hidden" name="content" value="<?php echo $this->_var['goods']['goods_style_name']; ?>">
            <input type="hidden" name="pics" value="<?php echo $this->_var['goods']['goods_img']; ?>">
            <input type="hidden" name="gid" value="<?php echo $this->_var['goods']['goods_id']; ?>">
            <input type="hidden" name="url" value="http://<?php echo $_SERVER['SERVER_NAME']; ?><?php echo $_SERVER['REQUEST_URI']; ?>">
     </form>
      
      <div class="main" id="user_goods_ka_1" style="display:block;">
         
            <?php echo $this->fetch('library/goods_gallery.lbi'); ?> 
          <form action="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" method="post" id="purchase_form" name="ECS_FORMBUY" >
            <div class="product_info">
              <div class="info_dottm">
                <h3 class="name"><?php echo sub_str($this->_var['goods']['goods_name'],30); ?></h3>
                <div class="right"><a onClick="tiaozhuan()"><div id="pro_share" class="share"></div></a></div>
              </div>
              
              <dl class="goods_price">
               <?php echo $this->smarty_insert_scripts(array('files'=>'lefttime.js')); ?>
                  
                  <?php if ($this->_var['goods']['is_promote'] && $this->_var['goods']['gmt_end_time']): ?>
              <dt>
              <span id="ECS_GOODS_AMOUNT"><?php echo $this->_var['goods']['promote_price']; ?></span><?php if ($this->_var['goods']['is_shipping']): ?>
              <em>包邮</em> <?php endif; ?><em>限时促销</em><strong id="leftTime"><?php echo $this->_var['lang']['please_waiting']; ?></strong>  </dt>
              <dd><font>价格：<?php echo $this->_var['goods']['shop_price_formated']; ?></font>
               <?php if ($this->_var['goods']['give_integral_2'] == '-1'): ?>
                  <p>购买此商品赠送<?php echo $this->_var['goods']['give_integral']; ?>积分</p>
                  <?php elseif ($this->_var['goods']['give_integral_2'] > 0): ?>
                  <p>购买此商品赠送<?php echo $this->_var['goods']['give_integral']; ?>积分</p>
                <?php endif; ?>
                </dd>
              <?php else: ?> 
              <dt> <span id="ECS_GOODS_AMOUNT"><?php echo $this->_var['goods']['shop_price_formated']; ?></span><font>价格：<?php echo $this->_var['goods']['market_price']; ?></font><?php if ($this->_var['goods']['is_shipping']): ?><em>包邮</em><?php endif; ?>
                <?php if ($this->_var['goods']['give_integral_2'] == '-1'): ?>
                  <p>购买此商品赠送<?php echo $this->_var['goods']['give_integral']; ?>积分</p>
                  <?php elseif ($this->_var['goods']['give_integral_2'] > 0): ?>
                  <p>购买此商品赠送<?php echo $this->_var['goods']['give_integral']; ?>积分</p>
                <?php endif; ?>
                </dt>
              <?php endif; ?>
              </dl>
              <ul class="price_dottm">
               <li><?php echo $this->_var['review_count']; ?>人评价</li>
               <li style=" text-align:right"><?php echo $this->_var['order_num']; ?>人已付款</li> 
              </ul>
              </div>             

 <?php if ($this->_var['promotion'] || $this->_var['volume_price_list'] || $this->_var['cfg']['use_integral'] || $this->_var['goods']['give_integral'] > 0 || $this->_var['goods']['bonus_money']): ?>
<section id="search_ka" class="huodong">
	<div class="subNav"> 
     <div class="att_title">
     <?php if ($this->_var['promotion']): ?>
      <?php $_from = $this->_var['promotion']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['promotion'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['promotion']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['promotion']['iteration']++;
?>       
      <?php if ($this->_foreach['promotion']['iteration'] < 2): ?>    
     <span>惠</span>  
      <p><?php echo $this->_var['item']['act_name']; ?></p>
      <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php elseif ($this->_var['volume_price_list']): ?>
      <?php $_from = $this->_var['volume_price_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('price_key', 'price_list');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['price_key'] => $this->_var['price_list']):
        $this->_foreach['name']['iteration']++;
?>
        <?php if ($this->_foreach['name']['iteration'] < 2): ?>
           <span>惠</span>
     <p>购买<?php echo $this->_var['price_list']['number']; ?>件&nbsp;优惠价：<?php echo $this->_var['price_list']['format_price']; ?></p>
      <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php elseif ($this->_var['cfg']['use_integral']): ?>
       
         <span>惠</span>
     <p><?php echo $this->_var['lang']['goods_integral']; ?><?php echo $this->_var['goods']['integral']; ?><?php echo $this->_var['points_name']; ?></p>
 <?php elseif ($this->_var['goods']['give_integral'] > 0): ?>
       
            <span>惠</span>
     <p> <?php echo $this->_var['lang']['goods_give_integral']; ?> <?php echo $this->_var['goods']['give_integral']; ?><?php echo $this->_var['points_name']; ?></p>
  <?php elseif ($this->_var['goods']['bonus_money']): ?>
   
       <span>惠</span>
     <p><?php echo $this->_var['lang']['goods_bonus']; ?><?php echo $this->_var['goods']['bonus_money']; ?></p>

       <?php endif; ?>
       
      </div>
      </div>
  
    <div class="navContent"> 
   <ul class="youhui_list1">
   <?php $_from = $this->_var['promotion']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['promotion'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['promotion']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['promotion']['iteration']++;
?> 
             <li>
      <?php if ($this->_var['item']['type'] == "favourable"): ?> 
    <a href="activity.php" title="<?php echo $this->_var['lang']['favourable']; ?>"><img src="themes/tianxin100/images/hui.png"></a>
      <?php endif; ?>
      <a href="<?php echo $this->_var['item']['url']; ?>" title="<?php echo $this->_var['lang'][$this->_var['item']['type']]; ?> <?php echo $this->_var['item']['act_name']; ?><?php echo $this->_var['item']['time']; ?>"><?php echo $this->_var['item']['act_name']; ?></a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>  
        </li>
      </ul>

      
    
    <?php if ($this->_var['volume_price_list']): ?>
     <ul class="youhui_list1" style="margin-top:0px;">  
           <?php $_from = $this->_var['volume_price_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('price_key', 'price_list');if (count($_from)):
    foreach ($_from AS $this->_var['price_key'] => $this->_var['price_list']):
?>
          
          <li><img src="themes/tianxin100/images/hui.png">购买<?php echo $this->_var['price_list']['number']; ?>件&nbsp;优惠价：<?php echo $this->_var['price_list']['format_price']; ?></li> 
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </ul>
     <?php endif; ?>
     
      
       <?php if ($this->_var['cfg']['use_integral'] || $this->_var['goods']['give_integral'] > 0 || $this->_var['goods']['bonus_money']): ?>
        <ul class="youhui_list1" style="margin-top:0px;"> 
        <?php if ($this->_var['cfg']['use_integral']): ?>
      <li><img src="themes/tianxin100/images/hui.png"><?php echo $this->_var['lang']['goods_integral']; ?><?php echo $this->_var['goods']['integral']; ?><?php echo $this->_var['points_name']; ?></li> 
      <?php endif; ?>
         <?php if ($this->_var['goods']['give_integral'] > 0): ?>
         <li><img src="themes/tianxin100/images/hui.png"><?php echo $this->_var['lang']['goods_give_integral']; ?> <?php echo $this->_var['goods']['give_integral']; ?><?php echo $this->_var['points_name']; ?></li> 
        <?php endif; ?>
           <?php if ($this->_var['goods']['bonus_money']): ?>
   <li><img src="themes/tianxin100/images/hui.png"><?php echo $this->_var['lang']['goods_bonus']; ?><?php echo $this->_var['goods']['bonus_money']; ?></li>
      </li>
      <?php endif; ?>
      
        
       <?php endif; ?>
       
       
   <div class="blank10"></div>
       </div>      
</section>
<?php endif; ?>


<?php if ($this->_var['specification']): ?>  
<section id="search_ka">

<div class="ui-sx bian1"> 
<div class="subNavBox"> 
	<div class="subNav"><strong>选择商品属性</strong></div>
    <ul class="navContent"> 
    <?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');$this->_foreach['specification'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['specification']['total'] > 0):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
        $this->_foreach['specification']['iteration']++;
?>
    <li>   
          <div class="title"><?php echo $this->_var['spec']['name']; ?></div>
          <div class="item">
          <?php if ($this->_var['spec']['attr_type'] == 1): ?>
          <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
          <a <?php if ($this->_var['key'] == 0): ?>class="hover"<?php endif; ?> href="javascript:;" name="<?php echo $this->_var['value']['id']; ?>" onclick="changeAtt(this);" for="spec_value_<?php echo $this->_var['value']['id']; ?>" title="<?php if ($this->_var['value']['price'] > 0): ?><?php echo $this->_var['lang']['plus']; ?><?php echo $this->_var['value']['format_price']; ?><?php elseif ($this->_var['value']['price'] < 0): ?><?php echo $this->_var['lang']['minus']; ?><?php echo $this->_var['value']['format_price']; ?><?php endif; ?>"><input style="display:none" id="spec_value_<?php echo $this->_var['value']['id']; ?>" type="radio" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?> />
          <?php echo $this->_var['value']['label']; ?>  <?php if ($this->_var['value']['price'] > 0): ?><font>+ <?php echo $this->_var['value']['format_price']; ?></font><?php elseif ($this->_var['value']['price'] < 0): ?><font>- <?php echo $this->_var['value']['format_price']; ?></font><?php endif; ?>
          
          </a>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
           <?php else: ?>
            <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
          <a <?php if ($this->_var['key'] == 0): ?>class="hover"<?php endif; ?> href="javascript:;" name="<?php echo $this->_var['value']['id']; ?>" onclick="changeAtt1(this)" for="spec_value_<?php echo $this->_var['value']['id']; ?>" title="<?php if ($this->_var['value']['price'] > 0): ?><?php echo $this->_var['lang']['plus']; ?><?php echo $this->_var['value']['format_price']; ?><?php elseif ($this->_var['value']['price'] < 0): ?><?php echo $this->_var['lang']['minus']; ?><?php echo $this->_var['value']['format_price']; ?><?php endif; ?>"><input type="checkbox" style=" display:none" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" id="spec_value_<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?>/>
							<?php echo $this->_var['value']['label']; ?> <?php if ($this->_var['value']['price'] > 0): ?><font>+ <?php echo $this->_var['value']['format_price']; ?></font><?php elseif ($this->_var['value']['price'] < 0): ?><font>- <?php echo $this->_var['value']['format_price']; ?></font><?php endif; ?>
           </a>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
          </div>                    
    </li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>  
      
    <li style=" border-bottom:1px solid #eeeeee">
        <div class="title1">购买数量</div>
        <div class="item1">
         <script language="javascript" type="text/javascript">  function goods_cut(){var num_val=document.getElementById('number');  var new_num=num_val.value;  var Num = parseInt(new_num);  if(Num>1)Num=Num-1;  num_val.value=Num;}  function goods_add(){var num_val=document.getElementById('number');  var new_num=num_val.value;  var Num = parseInt(new_num);  Num=Num+1;  num_val.value=Num;} </script>
         <span class="ui-number">
          <button type="button" class="decrease" onclick="goods_cut();changePrice();"></button>
          <input type="number" class="num" id="number" onblur="changePrice();" name="number" value="1" min="1" max="<?php echo $this->_var['goods']['goods_number']; ?>" style=" text-align:center"/>
          <button type="button" class="increase" onclick="goods_add();changePrice();"></button>
          </span>
      </div>
    </li>

       </ul>  
    </div>
    </div>    
</section> 

<?php else: ?>
<section id="search_ka">
<div class="ui-sx bian1"> 
<div class="subNavBox"> 
	<div class="subNav on"><strong>购买数量</strong></div>
    <ul class="navContent" style="display: block;"> 
	<li style=" border-bottom:1px solid #eeeeee">
        <div class="item1">
         <script language="javascript" type="text/javascript">  function goods_cut(){var num_val=document.getElementById('number');  var new_num=num_val.value;  var Num = parseInt(new_num);  if(Num>1)Num=Num-1;  num_val.value=Num;}  function goods_add(){var num_val=document.getElementById('number');  var new_num=num_val.value;  var Num = parseInt(new_num);  Num=Num+1;  num_val.value=Num;} </script>
         <span class="ui-number">
          <button type="button" class="decrease" onclick="goods_cut();changePrice();">-</button>
          <input type="text" class="num" id="number" onblur="changePrice();" name="number" value="1" min="1" max="<?php echo $this->_var['goods']['goods_number']; ?>"/>
          <button type="button" class="increase" onclick="goods_add();changePrice();">+</button>
          </span>
      </div>    
    </li>
       </ul>  
    </div>
    </div>    
</section> 

<?php endif; ?>


<?php if ($this->_var['rank_prices']): ?>

<section id="search_ka">

<div class="ui-sx bian1"> 
<div class="subNavBox" > 
	<div class="subNav"><strong>会员专享价</strong></a></div>
    <ul class="navContent" > 
    <li  class="user_price">			
		<?php $_from = $this->_var['rank_prices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'rank_price');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['rank_price']):
?>
        <p>
         <span class="key"><?php echo $this->_var['rank_price']['rank_name']; ?>：</span>
         <b class="p-price-v"><?php echo $this->_var['rank_price']['price']; ?></b>
         </p>
     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

    </li>
       </ul> 
    </div>
    </div> 

  
</section>
    <?php endif; ?>
    <script type="text/javascript">
$(function(){
	$(".subNav").click(function(){
		
		$(this).next(".navContent").slideToggle(300).siblings(".navContent").slideUp(500);
		$(this).toggleClass("on").siblings(".subNav").removeClass("on");
		if($(".is_scroll").length <= 0)
		{
		$('html,body').animate({'scrollTop':$('body')[0].scrollHeight},600);
		}
	})	
})
</script>
<script type="text/jscript">
			  function click_search (){
				  var search_ka = document.getElementById("search_ka");
				  if (search_ka.className == "s-buy open ui-section-box"){
					  search_ka.className = "s-buy ui-section-box";
					  }else {
						  search_ka.className = "s-buy open ui-section-box";
						  }
				  }
function changeAtt(t) {
t.lastChild.checked='checked';
for (var i = 0; i<t.parentNode.childNodes.length;i++) {
        if (t.parentNode.childNodes[i].className == 'hover') {
            t.parentNode.childNodes[i].className = '';
			t.childNodes[0].checked="checked";
		}
    }
t.className = "hover";
changePrice();
}
function changeAtt1(t) {
t.lastChild.checked='checked';
for (var i = 0; i<t.parentNode.childNodes.length;i++) {
        if (t.className == 'hover') {
            t.className = '';
			t.childNodes[0].checked = false;
		}
		else{
			t.className="hover";
			t.childNodes[0].checked = true;
		}
		
	
}

changePrice();
}
</script> 





          </form>

      </div>
          
      
      <div class="main" id="user_goods_ka_2" style="display:none">
      <div class="product_main" style=" margin-top:40px;"> 
          <div class="product_images product_desc" id="product_desc"> <?php echo $this->_var['goods']['goods_desc']; ?> </div>
        </div>
        <?php if ($this->_var['properties'] || $this->_var['cfg']['show_goodssn'] || ( $this->_var['goods']['goods_brand'] != "" && $this->_var['cfg']['show_brand'] ) || $this->_var['cfg']['show_goodsweight'] || $this->_var['cfg']['show_addtime']): ?>
      <section class="index_floor">
    <h2 style=" border-bottom:1px solid #ddd ">
      <span></span>
      <?php echo $this->_var['lang']['xinxi']; ?>
    </h2>
    
      <ul class="xiangq">
         <?php $_from = $this->_var['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'property_group');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['property_group']):
?>
        <?php $_from = $this->_var['property_group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'property');if (count($_from)):
    foreach ($_from AS $this->_var['property']):
?>
      <li><p><?php echo htmlspecialchars($this->_var['property']['name']); ?>:</p><span><?php echo $this->_var['property']['value']; ?></span></li>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
       <?php if ($this->_var['cfg']['show_goodssn']): ?>
       <li><p><?php echo $this->_var['lang']['goods_sn']; ?></p><span><?php echo $this->_var['goods']['goods_sn']; ?> </span><li>
       <?php endif; ?>
       <?php if ($this->_var['goods']['goods_brand'] != "" && $this->_var['cfg']['show_brand']): ?>
       <li><p><?php echo $this->_var['lang']['goods_brand']; ?></p><span><a href="<?php echo $this->_var['goods']['goods_brand_url']; ?>" ><?php echo $this->_var['goods']['goods_brand']; ?></a></span><li>
       <?php endif; ?>
        <?php if ($this->_var['cfg']['show_goodsweight']): ?>
       <li><p><?php echo $this->_var['lang']['goods_weight']; ?></p><span><?php echo $this->_var['goods']['goods_weight']; ?></span><li>
       <?php endif; ?>
      <?php if ($this->_var['cfg']['show_addtime']): ?>
       <li><p><?php echo $this->_var['lang']['add_time']; ?></p><span><?php echo $this->_var['goods']['add_time']; ?></span><li>
      <?php endif; ?>
    
      </ul>
       </section>
       <?php endif; ?>
      </div> 
      
      <div class="tab_attrs tab_item hide" id="user_goods_ka_3" style="display:none;">
        <?php echo $this->fetch('library/comments.lbi'); ?> 
        <script language="javascript"> ShowMyComments(<?php echo $this->_var['goods']['goods_id']; ?>,0,1);</script>
      </div>
      
    </div>
    
    
    
    <?php if ($this->_var['package_goods_list_120']): ?>
    <div class="is_scroll">
    <div style=" height:8px; background:#eeeeee; margin-top:-1px;"></div>
    <section class="index_taocan">
    <a href="goods.php?act=taocan&goods_id=<?php echo $this->_var['goods']['goods_id']; ?>">
    <h2><span></span>优惠套餐</h2>
        <div class="tc_goods">
        <?php $_from = $this->_var['package_goods_list_120']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pa_item');$this->_foreach['pa_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pa_list']['total'] > 0):
    foreach ($_from AS $this->_var['pa_item']):
        $this->_foreach['pa_list']['iteration']++;
?>
        <?php if (($this->_foreach['pa_list']['iteration'] <= 1)): ?>
         <?php $_from = $this->_var['pa_item']['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pa_goods');$this->_foreach['pa_list_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pa_list_goods']['total'] > 0):
    foreach ($_from AS $this->_var['pa_goods']):
        $this->_foreach['pa_list_goods']['iteration']++;
?>
         <?php if ($this->_foreach['pa_list_goods']['iteration'] < 4): ?>
         <?php if ($this->_foreach['pa_list_goods']['iteration'] == 3): ?>
          <dl class="t_goods">
          <dt><img src="<?php echo $this->_var['pa_goods']['goods_thumb']; ?>" class="B_eee" ></dt>
          <dd><?php echo $this->_var['pa_goods']['rank_price_zk_format']; ?></dd>
           </dl>
           <?php else: ?>
               <dl class="t_goods">
          <dt><img src="<?php echo $this->_var['pa_goods']['goods_thumb']; ?>" class="B_eee" ></dt>
          <dd><?php echo $this->_var['pa_goods']['rank_price_zk_format']; ?></dd>
           </dl>
           <div class="jia"><img src="themes/tianxin100/images/jia.png" class="B_eee" ></div>
           <?php endif; ?>
          <?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
</a>
  </section>
</div>
<?php endif; ?>

 

    <?php if ($this->_var['related_goods']): ?>
     <div style=" height:8px; background:#eeeeee;"></div>
  <section class="index_floor is_scroll">
    <h2>
      <span></span>
      <?php echo $this->_var['lang']['goods_botoomtitle']; ?>
    </h2>
      <div class="bd">
        <ul>
          <?php $_from = $this->_var['related_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'releated_goods_data');$this->_foreach['releated_goods_data'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['releated_goods_data']['total'] > 0):
    foreach ($_from AS $this->_var['releated_goods_data']):
        $this->_foreach['releated_goods_data']['iteration']++;
?>
          <li>
            <a href="<?php echo $this->_var['releated_goods_data']['url']; ?>">
              <div class="products_kuang">
                <img src="<?php echo $this->_var['releated_goods_data']['goods_thumb']; ?>"></div>
              <div class="goods_name"><?php echo $this->_var['releated_goods_data']['short_name']; ?></div>
              <div class="price" >
              <p href="<?php echo $this->_var['goods']['url']; ?>">
               <?php if ($this->_var['releated_goods_data']['promote_price'] != 0): ?> 
              <?php echo $this->_var['releated_goods_data']['formated_promote_price']; ?> 
              <?php else: ?>
              <?php echo $this->_var['releated_goods_data']['shop_price']; ?> <?php endif; ?></p>
                 <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>);" class="car">
                    <img src="themes/tianxin100/images/xin/cutp.png">
                </a>
              </div>
            </a>
          </li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
</div>
  </section>
    <?php endif; ?> 
    
<?php if ($this->_var['fittings']): ?>
 <div style=" height:8px; background:#eeeeee;"></div>
<section class="index_floor is_scroll">
    <h2>
      <span></span>
      <?php echo $this->_var['lang']['goods_botoomtitle_two']; ?>
    </h2>
      <div class="bd">
        <ul>
           <?php $_from = $this->_var['fittings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_45775300_1480415311');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_45775300_1480415311']):
?>
          <li>
            <a href="<?php echo $this->_var['goods_0_45775300_1480415311']['url']; ?>">
              <div class="products_kuang">
                <img src="<?php echo $this->_var['goods_0_45775300_1480415311']['goods_thumb']; ?>"></div>
              <div class="goods_name"><?php echo htmlspecialchars($this->_var['goods_0_45775300_1480415311']['short_name']); ?></div>
              <div class="price" >
              <p href="<?php echo $this->_var['goods_0_45775300_1480415311']['url']; ?>"><?php echo $this->_var['goods_0_45775300_1480415311']['fittings_price']; ?> </p>
                 <a href="javascript:addToCart(<?php echo $this->_var['goods_0_45775300_1480415311']['goods_id']; ?>);" class="car">
                    <img src="themes/tianxin100/images/xin/cutp.png">
                </a>
              </div>
            </a>
          </li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>
</div>
  </section>
<?php endif; ?>



<script>
function goTop(){
	$('html,body').animate({'scrollTop':0},600);
}
</script>
<a href="javascript:goTop();" class="gotop"><img src="themes/tianxin100/images/topup.png"></a>
<div style=" height:60px;"></div>
<div class="footer_nav">
 <ul> 
 <li class="bian"><a href="index.php"><em class="goods_nav1"></em><span>首页</span></a> </li>
 <li class="bian"><a href="tel:<?php echo $this->_var['service_phone']; ?>"><em class="goods_nav2"></em><span>客服</span></a> </li>
 <li><a href="javascript:collect(<?php echo $this->_var['goods']['goods_id']; ?>)" id="favorite_add"><em class="goods_nav3"></em><span>收藏</span></a></li>
 </ul>
 <dl>
 <dd class="flow"><a class="button active_button" href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>);">加入购物车</a> </dd>
 <dd class="goumai"><a style="display:block;" href="javascript:addToCart1(<?php echo $this->_var['goods']['goods_id']; ?>)">立即购买</a> </dd>
 </dl>                
</div>                
<script type="text/javascript">
var goods_id = <?php echo $this->_var['goods_id']; ?>;
var goodsattr_style = <?php echo empty($this->_var['cfg']['goodsattr_style']) ? '1' : $this->_var['cfg']['goodsattr_style']; ?>;
var gmt_end_time = <?php echo empty($this->_var['promote_end_time']) ? '0' : $this->_var['promote_end_time']; ?>;
<?php $_from = $this->_var['lang']['goods_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var goodsId = <?php echo $this->_var['goods_id']; ?>;
var now_time = <?php echo $this->_var['now_time']; ?>;


onload = function(){ 
  changePrice();

  try {onload_leftTime();}
  catch (e) {}
}

/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function changePrice()
{
  var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);
  var qty = document.forms['ECS_FORMBUY'].elements['number'].value;
if(qty <=0 ){
 document.forms['ECS_FORMBUY'].elements['number'].value = 1;
 qty = 1;
}
  Ajax.call('goods.php', 'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty, changePriceResponse, 'GET', 'JSON');
}

/**
 * 接收返回的信息
 */
function changePriceResponse(res)
{
  if (res.err_msg.length > 0)
  {
    alert(res.err_msg);
  }
  else
  {
    document.forms['ECS_FORMBUY'].elements['number'].value = res.qty;

    if (document.getElementById('ECS_GOODS_AMOUNT'))
      document.getElementById('ECS_GOODS_AMOUNT').innerHTML = res.result;
  }
}

</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>

  wx.config({
    debug: false,
    appId: '<?php echo $this->_var['signPackage']['appId']; ?>',
    timestamp: <?php echo $this->_var['signPackage']['timestamp']; ?>,
    nonceStr: '<?php echo $this->_var['signPackage']['nonceStr']; ?>',
    signature: '<?php echo $this->_var['signPackage']['signature']; ?>',
    jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage' 
    ]
  });
 wx.ready(function () {
	//甜心100监听“分享给朋友”
    wx.onMenuShareAppMessage({
      title: '<?php echo $this->_var['goods']['goods_style_name']; ?>',
      desc: '<?php echo $this->_var['goods']['goods_style_name']; ?>',
      link: '<?php echo $this->_var['url']; ?>',
      imgUrl: '<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['original_img']; ?>',
      trigger: function (res) {
		
		<?php if ($this->_var['url']): ?>
        alert('恭喜！分享可以获取提成哦！');
		<?php else: ?>
		alert('糟糕，需要分销商登录才能获得提成哦！');
		<?php endif; ?>
		
      },
      success: function (res) {
		<?php if ($this->_var['dourl']): ?>
        window.location.href="<?php echo $this->_var['dourl']; ?>&type=1"; 
		<?php endif; ?>
      },
      cancel: function (res) {
        alert('很遗憾，您已取消分享');
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });

	//分享到朋友圈甜心100
    wx.onMenuShareTimeline({
      title: '<?php echo $this->_var['goods']['goods_style_name']; ?>',
      link: '<?php echo $this->_var['url']; ?>',
      imgUrl: '<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['original_img']; ?>',
      trigger: function (res) {
			
        <?php if ($this->_var['url']): ?>
			alert('恭喜！分享可以获取提成哦！');
		<?php else: ?>
			alert('糟糕，需要分销商登录才能获得提成哦！');
		<?php endif; ?>
      },
      success: function (res) {
       	<?php if ($this->_var['dourl']): ?>
        window.location.href="<?php echo $this->_var['dourl']; ?>&type=2"; 
		<?php endif; ?>
      },
      cancel: function (res) {
         alert('很遗憾，您已取消分享');
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });


});

</script>
</body>
</html>