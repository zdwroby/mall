<style>
  .dialog_guid {
    color: #ffffff;
    font-size: 14px;
    line-height: 18px;
}
.dialog_guid .widget_wrap {
    height: 0;
    position: static;
    width: 0;
}
.dialog_guid ul {
    background: rgba(0, 0, 0, 0.6) none repeat scroll 0 0;
    height: 50px;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 50;
}
.dialog_guid ul li {
    height: 50px;
    vertical-align: middle;
}
.dialog_guid p {
    padding: 2px 0;
}
.dialog_guid a {
    background: #06bd04 none repeat scroll 0 0;
    border-radius: 3px;
    color: #ffffff;
    display: inline-block;
    font-size: 12px;
    height: 30px;
    line-height: 30px;
    margin: 0 0 0 1px;
    position: relative;
    text-align: center;
    width: 75px;
}
.dialog_guid a.close {
    background: rgba(0, 0, 0, 0) url("imgs/w_7.png") no-repeat scroll center -513px;
    margin: 0;
    width: 25px;
}
.dialog_guid .img_wrap {
    border-radius: 100px;
    display: block;
    height: 30px;
    margin: 0 10px;
    width: 30px;
}
.dialog_guid .img_wrap img {
    border-radius: 100px;
    display: block;
    height: 100%;
    width: 100%;
}
.dialog_guid .img_wrap img[src=""] {
    background: #ffffff none repeat scroll 0 0;
}
.dialog_guid_follow_authentication ul {
    height: 65px;
}
.dialog_guid_follow_authentication ul li {
    height: 65px;
	float:left;
	margin-right:5px;
	padding-top:12px;
}
.dialog_guid_follow_authentication .img_wrap {
    height: 42px;
    width: 42px;
}

  </style>
  
    <div data-role="widget" data-widget="widget_40" id="widget_dialog_1500" style="z-index: 1500;" class="dialog_guid  dialog_guid_follow_authentication on">
	<div ontouchmove="event.preventDefault();" style="z-index:1500;" class="widget_wrap">
	<ul style="z-index:1550;" class="tbox">
	<li style="width:15%;margin-right:10px !important;"><span class="img_wrap">
	<?php if ($this->_var['share_info']['headimgurl'] != ''): ?>
		<img src="<?php echo $this->_var['share_info']['headimgurl']; ?>"><?php else: ?><img src="<?php echo $this->_var['ectouch_themes']; ?>/images/get_avatar.png"><?php endif; ?></span>
	</li>
	<li style="width:54%">
	<p><?php if ($this->_var['share_info']['nickname'] != ''): ?>来自&nbsp; <?php echo $this->_var['share_info']['nickname']; ?>&nbsp;的分享<?php endif; ?>
	<?php if ($this->_var['url']): ?>马上分享商品给好朋友<br/>获取高额提成！<?php else: ?>购买产品 马上成为东家<?php endif; ?></p>
	</li>						
	<li id="share_1" style="width:15%;padding-right:15px;padding-top:15px;">
	<?php if ($this->_var['userid'] == 0): ?>

	<a href="<?php echo $this->_var['tianxin_url']; ?>">马上关注</a>
	<?php endif; ?>
	
	</li>
	</ul>
	</div>
	</div>
	