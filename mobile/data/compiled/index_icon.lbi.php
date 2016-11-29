<div class="entry-list clearfix">
	<nav>
		<ul>
			<?php $_from = $this->_var['navigator_list']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['nav_middle_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_middle_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['nav_middle_list']['iteration']++;
?>
			<li>
				<a href="<?php echo $this->_var['nav']['url']; ?>">
					<img alt="<?php echo $this->_var['nav']['name']; ?>" src="<?php echo $this->_var['nav']['pic']; ?>" />
					<span><?php echo $this->_var['nav']['name']; ?></span>
				</a>
			</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
	</nav>
</div>