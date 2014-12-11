<?php get_header()?>
	<!-- Column 1 /Content -->
	<div class="grid_8">
        <?php if (have_posts()) {
            while(have_posts()){
                the_post();
        ?>
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
		<?php } ?>
		<!-- Blog Navigation -->
		<p class="clearfix"> <a href="#" class="button float">&lt;&lt; Previous Posts</a> <a href="#" class="button float right">Newer Posts >></a> </p>
	</div>
	<!-- Column 2 / Sidebar -->
    <?php } else { ?>
        <h3 class="title"><a href="#" rel="bookmark">未找到</a></h3>
        <p>没有找到任何文章！</p>
    <?php } ?>
	<?php get_sidebar();?>
	<div class="hr grid_12 clearfix">&nbsp;</div>
	<!-- Footer -->
<?php get_footer();?>
