<?php //获得最新的6篇文章?>
<?php $posts = get_posts('numberposts=6&orderby=post_date');//最新文章 显示6篇按照发帖时间排序
?>
<td width="264" class="bg_01">
				<br><br>
				<table width="264" border="0" cellpadding="0" cellspacing="0">
                  <tr align="left" valign="top">
                    <td width="22">&nbsp;</td>
                    <td><img src="<?=get_template_directory_uri();?>/images/news.jpg" width="36" height="11">
					<br><br>
<?php 
foreach ($posts as $post) {
	 setup_postdata($post);
?>
	<span class="t_date"><?php the_time('Y年n月j日') ?></span><br>
	<a href="<?=get_permalink();?>" title="<?php the_title_attribute(); ?>" class="small_link"><?=get_the_title(); ?></a> 
	<br><br>
<?php }
?>
					</td>
                    <td width="14">&nbsp;</td>
                  </tr>
                </table>
				<br><br><br><br><br><br>
<?php //最新文章
	foreach ($posts as $item){?>
		<img src="<?=get_template_directory_uri();?>/images/bul_01.gif" hspace="6" align="absmiddle"><span class="t_list"><?=$item->post_title?> </span><br><img src="<?=get_template_directory_uri();?>/images/line.gif"><br>
<?php }?>
	</td>		  
	</tr>
			</table>
</td>
