<?php get_header() ?>
<!--内容页开始-->
			<td width="580" bgcolor="F4ECD4">
			  <!--这里是首页的内容页-->
			  <table width="580" height="100%" border="0" cellpadding="0" cellspacing="0">
			  <tr align="left" valign="top">
				<td width="316">
					<img src="<?=get_template_directory_uri();?>/images/projects.gif" width="66" height="10" hspace="6" vspace="4">
				<br>
				<br>
				<!--这个是相片墙-->
				<table width="316" border="0" cellspacing="0" cellpadding="0">
					<tr align="center" valign="top">
					<td><img src="<?=get_template_directory_uri();?>/images/pic_01.jpg" width="81" height="80"></td>
					<td><img src="<?=get_template_directory_uri();?>/images/pic_02.jpg" width="81" height="80" hspace="33"></td>
					<td><img src="<?=get_template_directory_uri();?>/images/pic_03.jpg" width="81" height="80"></td>
				</tr>
				<tr align="center" valign="top">
					<td><img src="<?=get_template_directory_uri();?>/images/pic_04.jpg" width="81" height="80" vspace="25"></td>
					<td><img src="<?=get_template_directory_uri();?>/images/pic_05.jpg" width="81" height="80" hspace="33" vspace="25"></td>
					<td><img src="<?=get_template_directory_uri();?>/images/pic_06.jpg" width="81" height="80" vspace="25"></td>
				</tr>
				</table>
		<br>
		<!--文章开始-->
		<img src="<?=get_template_directory_uri();?>/images/about.gif" vspace="5">
		<br>
		<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit. Praesent vestibulum molestie lacus. 
	Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi. Cum sociis natoque penatibus et 
magnis dis parturient montes, nascetur ridiculus mus. 
<br><br>
		Nulla dui. Fusce feugiat malesuada odio. <a href="#">Morbi nunc odio</a>, gravida at, cursus nec, luctus a, lorem. Maecenas 
tristique orci ac sem. Duis ultricies pharetra magna. Donec accumsan malesuada orci. Donec sit amet eros. Lorem ipsum 
dolor sit amet, consectetuer adipiscing elit. Mauris fermentum dictum magna. Sed laoreet aliquam leo. Ut tellus dolor, 
dapibus eget, elementum vel, cursus eleifend, elit. Aenean auctor wisi et urna. Aliquam erat volutpat. Duis ac turpis. 
Integer rutrum ante eu lacus.
<br><br>
Quisque nulla. Vestibulum libero nisl, porta vel, scelerisque eget, malesuada at, neque. 
<br>
	<!--阅读更多-->
		<a href="#"><img src="<?=get_template_directory_uri();?>/images/read.gif" border="0" align="right"></a>
	</td>
	<!--这里是侧边栏-->			
	<?php get_sidebar();?>
			<td width="93">
			<table width="93" height="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="468" align="left" valign="top"><img src="<?=get_template_directory_uri();?>/images/right_01.jpg" width="93" height="468"></td>
              </tr>
              <tr>
				<td align="left" valign="bottom" background="<?=get_template_directory_uri();?>/images/right_02.jpg"><img src="<?=get_template_directory_uri();?>/images/right_03.jpg" width="93" height="52"></td>
              </tr>
			</table>
		</td>
		  </tr>
		<!--循环文章开始-->
			<?php
			 if ( have_posts() ) {
                // Start the Loop.
                while ( have_posts() ) { 
					the_post();
                    get_template_part( 'content', get_post_format() );
                }
            } else {
                // If no content, include the "No posts found" template.
                get_template_part( 'content', 'none' );
			}
			?>		
		<!--文章循环结束-->
		</table>
	</td>
	  </tr>
<tr>
        <td height="118" align="left" valign="top"><table width="746" height="118" border="0" cellpadding="0" cellspacing="0">
          <tr align="left" valign="top">
            <td width="249"><img src="<?=get_template_directory_uri();?>/images/bottm_01.jpg" width="249" height="118"></td>
            <td width="248"><img src="<?=get_template_directory_uri();?>/images/bottm_02.jpg" width="248" height="118"></td>
            <td width="249"><img src="<?=get_template_directory_uri();?>/images/bottm_03.jpg" width="249" height="118"></td>
          </tr>
        </table></td>
      </tr>
<?php get_footer();?>
