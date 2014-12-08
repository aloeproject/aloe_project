<tr align="left" valign="top">
            <td width="73"><table width="73" height="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
			  <td height="468" align="left" valign="top"><img src="<?=get_template_directory_uri();?>/images/left_01.jpg" width="73" height="468"></td>
              </tr>
              <tr>
			  <td align="left" valign="bottom" background="<?=get_template_directory_uri();?>/images/left_02.jpg"><img src="<?=get_template_directory_uri();?>/images/left_03.jpg" width="73" height="52"></td>
              </tr>
            </table>
			</td>
			<td width="580" bgcolor="F4ECD4">
			  <!--这里是首页的内容页-->
			  <table width="580" height="100%" border="0" cellpadding="0" cellspacing="0">
			  <tr align="left" valign="top">
				<td width="316">
				<img src="<?=get_template_directory_uri();?>/images/projects.gif" width="66" height="10" hspace="6" vspace="4">
				<br>
				<br><br>
		<!--文章开始-->
		<img src="<?=get_template_directory_uri();?>/images/about.gif" vspace="5">
		<br>
		<!--文章内容-->
		<?php 
		//the_excerpt();
		the_content();
		 ?>
<!--阅读更多-->
	<a href="<?php the_permalink(); ?>"><img src="<?=get_template_directory_uri();?>/images/read.gif" border="0" align="right"></a>
	</td>
	<!--这里是侧边栏-->
	<td width="264">
	</td>
	</tr>
			</table>

</td>
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
