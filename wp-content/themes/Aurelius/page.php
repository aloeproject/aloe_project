<?php get_header()?>
<div id="wrapper" class="container_12 clearfix">
	<!-- Text Logo -->
	<h1 id="logo" class="grid_4">Aurelius</h1>
	<!-- Navigation Menu -->
	<ul id="navigation" class="grid_8">
		<li><a href="contact.html"><span class="meta">Get in touch</span><br />
			Contact Us</a></li>
		<li><a href="blog.html" class="current"><span class="meta">Latest news</span><br />
			Blog</a></li>
		<li><a href="index.html"><span class="meta">Homepage</span><br />
			Home</a></li>
	</ul>
	<div class="hr grid_12 clearfix">&nbsp;</div>
	<!-- Caption Line -->
	<h2 class="grid_12 caption clearfix">Enjoyed looking at our work? Don't hesitate to <span>contact us</span>!</h2>
	<div class="hr grid_12 clearfix">&nbsp;</div>
	<!-- Column 1 / Content -->
	<div class="grid_8">
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vel porta erat. Quisque sit amet risus at odio pellentesque sollicitudin. Proin suscipit molestie facilisis. Aenean vel massa magna. Proin nec lacinia augue. Mauris venenatis libero nec odio viverra consequat. In hac habitasse platea dictumst.</p>
		<!-- Contact Form -->
		<form id="comment_form" action="" method="post">
			<h3>Add a comment</h3>
			<div class="hr dotted clearfix">&nbsp;</div>
			<ul>
				<li class="clearfix">
					<label for="name">Your Name</label>
					<input id="name" name="name" type="text" />
				</li>
				<li class="clearfix">
					<label for="email">Your Email</label>
					<input id="email" name="email" type="text" />
				</li>
				<li class="clearfix">
					<label for="email">Your Website</label>
					<input id="website" name="website" type="text" />
				</li>
				<li class="clearfix">
					<label for="message">Comment</label>
					<textarea id="message" name="message" rows="3" cols="40"></textarea>
				</li>
				<li class="clearfix">
					<!-- Add Comment Button -->
					<a type="submit" class="button medium black right">Add comment</a> </li>
			</ul>
		</form>
	</div>
	<!-- Column 2 / Sidebar -->
	<div class="grid_4 contact">
		<!-- Adress and Phone Details -->
		<h4>Address and Phone</h4>
		<div class="hr dotted clearfix">&nbsp;</div>
		<ul>
			<li> <strong>Your Company Name</strong><br />
				1458 Sample Road, Redvalley<br />
				City (State) H4Q 1J7<br />
				Country<br />
				<br />
			</li>
			<li>USA - (888) 888-8888</li>
			<li>Worldwide - (888) 888-8888</li>
		</ul>
		<!-- Email Addresses -->
		<h4>Emails</h4>
		<div class="hr dotted clearfix">&nbsp;</div>
		<ul>
			<li>General - <a href="mailto:info@yourcompany.com">info@yourcompany.com</a></li>
			<li>Sales - <a href="mailto:sales@yourcompany.com">sales@yourcompany.com</a></li>
		</ul>
		<!-- Social Profile Links -->
		<h4>Social Profiles</h4>
		<div class="hr dotted clearfix">&nbsp;</div>
		<ul>
			<li class="float"><a href="#"><img alt="" src="images/twitter.png" title="Twitter" /></a></li>
			<li class="float"><a href="#"><img alt="" src="images/facebook.png" title="Facebook" /></a></li>
			<li class="float"><a href="#"><img alt="" src="images/stumbleupon.png" title="StumbleUpon" /></a></li>
			<li class="float"><a href="#"><img alt="" src="images/flickr.png" title="Flickr" /></a></li>
			<li class="float"><a href="#"><img alt="" src="images/delicious.png" title="Delicious" /></a></li>
		</ul>
	</div>
	<div class="hr grid_12 clearfix">&nbsp;</div>
	<!-- Footer -->
<?php get_footer();?>