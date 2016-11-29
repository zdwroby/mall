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
<link rel="stylesheet" type="text/css" href="themes/tianxin100/css/index.css"/>
<script type="text/javascript" src="themes/tianxin100/js/jquery.js"></script>
<script type="text/javascript" src="themes/tianxin100/js/TouchSlide.1.1.js"></script>
	<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.json.js,transport_index.js')); ?>
	<script type="text/javascript" src="themes/tianxin100/js/touchslider.dev.js"></script>
    <script type="text/javascript" src="themes/tianxin100/js/jquery.more.js"></script>
	<?php echo $this->smarty_insert_scripts(array('files'=>'common.js')); ?>
</head>
<body>
<div id="page" class="showpage">
<div>

<header id="header"> <?php echo $this->fetch('library/page_header.lbi'); ?> </header>
 
<?php echo $this->fetch('library/index_ad.lbi'); ?> 


<div id="fake-search" class="index_search">
  <div class="index_search_mid">
  <span><img src="themes/tianxin100/images/xin/icosousuo.png"></span>
    <input  type="text" id="search_text" class="search_text" value="请输入您所搜索的商品"/>
  </div>
</div>
<?php echo $this->fetch('library/index_icon.lbi'); ?> 
	
	<?php echo $this->fetch('library/top_tianxin100_guanzhu.lbi'); ?>
	
	



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
 
 

<div class="floor_images">
  <dl>
    <dt> 
<?php $this->assign('ads_id','11'); ?><?php $this->assign('ads_num','0'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 </dt>
    <dd> 
    <span class="Edge"> 
<?php $this->assign('ads_id','9'); ?><?php $this->assign('ads_num','0'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 </span> 
<span> 

<?php $this->assign('ads_id','17'); ?><?php $this->assign('ads_num','0'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 </span> </dd>
  </dl>
<strong>
<?php $this->assign('ads_id','16'); ?><?php $this->assign('ads_num','0'); ?><?php echo $this->fetch('library/ad_position.lbi'); ?>
 </strong>
</div>
 

<?php echo $this->fetch('library/recommend_best.lbi'); ?>
<?php echo $this->fetch('library/recommend_new.lbi'); ?>
<?php echo $this->fetch('library/recommend_hot.lbi'); ?>
 

 

 

 
<div id="index_banner" class="index_banner">

				<div class="bd">
					<ul>
			<?php $_from = $this->_var['wap_index_img']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ad');$this->_foreach['wap_index_img'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['wap_index_img']['total'] > 0):
    foreach ($_from AS $this->_var['ad']):
        $this->_foreach['wap_index_img']['iteration']++;
?>
          <li><a href="<?php echo $this->_var['ad']['url']; ?>"><img src="<?php echo $this->_var['ad']['image']; ?>" width="100%" /></a></li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</ul>
				</div>

				<div class="hd">
					<ul></ul>
				</div>
			</div>
			<script type="text/javascript">
				TouchSlide({ 
					slideCell:"#index_banner",
					titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
					mainCell:".bd ul", 
					effect:"leftLoop", 
					autoPage:true,//自动分页
					autoPlay:true //自动播放
				});
			</script>
   


<footer> <?php echo $this->fetch('library/page_footer_tianxin.lbi'); ?> <?php echo $this->fetch('library/footer_nav.lbi'); ?> </footer>


<script>
function goTop(){
	$('html,body').animate({'scrollTop':0},600);
}
</script>
<a href="javascript:goTop();" class="gotop"><img src="themes/tianxin100/images/topup.png"></a> 

                    </div>
 
 

 <div id="search_hide" class="search_hide">
 <h2> <span class="close"><img src="themes/tianxin100/images/close.png"></span>关键搜索</h2>

 <div id="mallSearch" class="search_mid">
        <div id="search_tips" style="display:none;"></div>
          <ul class="search-type">
          	<li <?php if ($_REQUEST['type'] == 0): ?> class="cur" <?php endif; ?> num="0">宝贝</li>

          </ul>	
          <div class="searchdotm"> 
          <form class="mallSearch-form" method="get" name="searchForm" id="searchForm" action="search.php" onSubmit="return checkSearchForm()">
	   		<input type='hidden' name='type' id="searchtype" value="<?php echo empty($_REQUEST['type']) ? '0' : $_REQUEST['type']; ?>" >
              <div class="mallSearch-input">
                <div id="s-combobox-135">
                    <input aria-haspopup="true" role="combobox" class="s-combobox-input" name="keywords" id="keyword" tabindex="9" accesskey="s" onkeyup="STip(this.value, event);" autocomplete="off"  value="<?php if ($this->_var['search_keywords']): ?><?php echo htmlspecialchars($this->_var['search_keywords']); ?><?php else: ?>请输入关键词<?php endif; ?>" onFocus="if(this.value=='请输入关键词'){this.value='';}else{this.value=this.value;}" onBlur="if(this.value=='')this.value='请输入关键词'" type="text">
                </div>
                <input type="submit" value="" class="button"  >
              </div>
             
            
          </form>
         </div> 
        </div>

                            
                            <section class="mix_recently_search"><h3>热门搜索</h3>
   <?php if ($this->_var['searchkeywords']): ?>
  <ul>
    <?php $_from = $this->_var['searchkeywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
   <li>
    <a href="search.php?keywords=<?php echo urlencode($this->_var['val']); ?>"><?php echo $this->_var['val']; ?></a>
   </li>
   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
   </ul>

    <?php else: ?>
    <div class="recently_search_null">没有搜索记录</div>
    <?php endif; ?>
    </section>
                        </div>  
                        
                                              
</div>

<script type="Text/Javascript" language="JavaScript">


function selectPage(sel)
{
   sel.form.submit();
}


</script>
	<script type="text/javascript">
<?php $_from = $this->_var['lang']['compare_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['key'] != 'button_compare'): ?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php else: ?>
var button_compare = "";
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var compare_no_goods = "<?php echo $this->_var['lang']['compare_no_goods']; ?>";
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";
</script>

 
<script type="text/javascript">

$(function() {

    $('#search_text').click(function(){
        $(".showpage").children('div').hide();
        $("#search_hide").css('position','fixed').css('top','0px').css('width','100%').css('z-index','999').show();
    })
    $('#get_search_box').click(function(){
        $(".showpage").children('div').hide();
        $("#search_hide").css('position','fixed').css('top','0px').css('width','100%').css('z-index','999').show();
    })
    $("#search_hide .close").click(function(){
        $(".showpage").children('div').show();
        $("#search_hide").hide();
    })
});
</script>

<script>
$('.search-type li').click(function() {
    $(this).addClass('cur').siblings().removeClass('cur');
    $('#searchtype').val($(this).attr('num'));
});
$('#searchtype').val($(this).attr('0'));
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
      title: '<?php echo $this->_var['page_title']; ?>',
      desc: '<?php echo $this->_var['page_title']; ?>',
      link: '<?php echo $this->_var['url']; ?>',
      imgUrl: '<?php echo $this->_var['w_picurl']; ?>',
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
      title: '<?php echo $this->_var['page_title']; ?>',
      link: '<?php echo $this->_var['url']; ?>',
      imgUrl: '<?php echo $this->_var['w_picurl']; ?>',
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