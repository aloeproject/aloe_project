<?php get_header()?>
	<!-- Column 1 /Content -->
	<div class="grid_8">
			<div class="post">
	    <!-- Post Title -->
	    
	    <h3 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
	    <!-- Post Data -->
		<p class="sub">
	    <?php the_tags('标签：', '', '', '');; ?> &bull; 发布时间 <?php the_time('Y年n月j日') ?> &bull;
		<?php comments_popup_link('0 条评论', '1 条评论', '% 条评论', '', '评论已关闭'); ?>
		<?php edit_post_link('编辑', ' &bull; ', ''); ?>
	     </p>
	    <div class="hr dotted clearfix">&nbsp;</div>
	    <!-- Post Image 文章的缩略图 -->
	    <img class="thumb" alt="" src="<?php bloginfo('template_url'); ?>/images/610x150.gif" />
	    <!-- Post Content -->
	    <!--显示文字摘要-->
	    <?php the_content('阅读全文...'); ?>
	    <!-- Read More Button -->
	    <p class="clearfix"><a href="<?php the_permalink(); ?>" class="button right">阅读全文</a></p>
	</div>
		<div class="hr clearfix">&nbsp;</div>
		<!-- Blog Post -->
		<div class="post">
			<!-- Post Title -->
			<h3 class="title"><a href="single.html">Loreum ipsium massa cras phasellus</a></h3>
			<!-- Post Data -->
			<p class="sub"><a href="#">News</a>, <a href="#">Products</a> &bull; 31st Sep, 09 &bull; <a href="#">1 Comment</a></p>
			<div class="hr dotted clearfix">&nbsp;</div>
			<!-- Post Image -->
			<img class="thumb" alt="" src="<?php bloginfo('template_url'); ?>/images/610x150.gif" />
			<!-- Post Content -->
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. <b>Mauris vel porta erat.</b> Quisque sit amet risus at odio pellentesque sollicitudin. Proin suscipit molestie facilisis. Aenean vel massa magna. Proin nec lacinia augue. Mauris venenatis libero nec odio viverra consequat. In hac habitasse platea dictumst.</p>
			<p>Cras vestibulum lorem et dui mollis sed posuere leo semper. Integer ac ultrices neque. Cras lacinia orci a augue tempor egestas. Sed cursus, sem ut vehicula vehicula, ipsum est mattis justo, at volutpat nibh arcu sit amet risus. Vestibulum tincidunt, eros ut commodo laoreet, arcu eros ultrices nibh, ac auctor est dui vel nibh.</p>
			<!-- Read More Button -->
			<p class="clearfix"><a href="single.html" class="button right"> Read More...</a></p>
		</div>
		<div class="hr clearfix">&nbsp;</div>
		<!-- Blog Post -->
		<div class="post">
			<!-- Post Title -->
			<h3 class="title"><a href="single.html">Loreum ipsium massa cras phasellus</a></h3>
			<!-- Post Data -->
			<p class="sub"><a href="#">News</a>, <a href="#">Products</a> &bull; 31st Sep, 09 &bull; <a href="#">1 Comment</a></p>
			<div class="hr dotted clearfix">&nbsp;</div>
			<!-- Post Image -->
			<img class="thumb" alt="" src="<?php bloginfo('template_url'); ?>/images/610x150.gif" />
			<!-- Post Content -->
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. <b>Mauris vel porta erat.</b> Quisque sit amet risus at odio pellentesque sollicitudin. Proin suscipit molestie facilisis. Aenean vel massa magna. Proin nec lacinia augue. Mauris venenatis libero nec odio viverra consequat. In hac habitasse platea dictumst.</p>
			<p>Cras vestibulum lorem et dui mollis sed posuere leo semper. Integer ac ultrices neque. Cras lacinia orci a augue tempor egestas. Sed cursus, sem ut vehicula vehicula, ipsum est mattis justo, at volutpat nibh arcu sit amet risus. Vestibulum tincidunt, eros ut commodo laoreet, arcu eros ultrices nibh, ac auctor est dui vel nibh.</p>
			<!-- Read More Button -->
			<p class="clearfix"><a href="single.html" class="button right"> Read More...</a></p>
		</div>
		<div class="hr clearfix">&nbsp;</div>
		<!-- Blog Navigation -->
		<p class="clearfix"> <a href="#" class="button float">&lt;&lt; Previous Posts</a> <a href="#" class="button float right">Newer Posts >></a> </p>
	</div>
	<!-- Column 2 / Sidebar -->
	<?php get_sidebar();?>
	<div class="hr grid_12 clearfix">&nbsp;</div>
	<!-- Footer -->
<?php get_footer();?>
