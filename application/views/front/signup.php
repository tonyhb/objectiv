<header>
</header>
<section id="main">
	<div class="container">
		<?php if ( ! empty($errors) ): ?>
			<section class="errors">
				<h1>The following errors occured:</h1>
				<ul>
				<?php foreach ($errors as $field => $error): ?>
					<li><?php echo $error ?></li>
				<?php endforeach ?>
				</ul>
			</section>
		<?php endif ?>
		
		<form action="" method="post">
			<div class="register_account">
				<label for="contact_name">Your name</label>
				<div class="input"><input type="text" name="contact_name" value="<?php if(isset($data['contact_name'])) echo $data['contact_name'] ?>" id="contact_name" /></div>

				<label for="contact_email">Your email address</label>
				<div class="input"><input type="email" name="contact_email" value="<?php if(isset($data['contact_email'])) echo $data['contact_email'] ?>" id="contact_email" /></div>
		
				<label for="company_name">Company name <small>(optional)</small></label>
				<div class="input"><input type="text" name="company_name" value="<?php if(isset($data['company_name'])) echo $data['company_name'] ?>" id="company_name" /></div>
		
				<label for="password">Password</label>
				<div class="input"><input type="password" name="password" value="" id="password" /></div>
			</div>
			
			<div class="register_main">						
				<label for="site_name">Site name</label>
				<div class="input"><input type="text" name="site_name" value="<?php if(isset($data['site_name'])) echo $data['site_name'] ?>" id="site_name" /></div>

				<label for="site_address">Site address</label>
				<div class="input"><span class="http">http://</span><input type="text" name="site_address" value="<?php if(isset($data['site_address'])) echo $data['site_address'] ?>" id="site_address" /><span class="site_address">.hotboxx.net</span></div>
				
				<label for="domain_name">Domain name <small>(optional)</small></label>
				<div class="input" style="margin-bottom: 40px"><span class="http">http://</span><input type="text" name="domain_name" value="<?php if(isset($data['domain_name'])) echo $data['domain_name'] ?>" id="domain_name" onblur="if(this.value.search(/www./) == 0) document.getElementById('use_www').checked = 'checked'; else document.getElementById('use_www').checked = ''; " /><input type="checkbox" name="use_www" value="true" id="use_www" onchange="var t = document.getElementById('domain_name'); if(this.checked == true && t.value.search(/www/) != 0 ) t.value = 'www.' + t.value; else if(t.value.search(/www/) == 0 ) t.value = t.value.replace(/^www./, ''); " <?php if(isset($data['use_www'])) echo 'checked="checked"'; ?>/><span class="use_www">use www</span></div>
				
				<div class="terms"><input type="checkbox" name="agree" value="agree" id="agree" checked="checked" />Legal stuff! <a href="/terms-and-conditions">Read our terms and conditions</a> and check to agree.</div>
				
				<button type="submit" class="sign_up">Sign up</button><br class="clear" />
			</div>
		
		</form>
	</div>
</section>
