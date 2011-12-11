<div class='span16'>
	<div class='page-header'>
		<h1>New layout</h1>
	</div>

	<ul>
		<?php foreach($errors as $field => $error): ?>
			<li><?php echo $error ?></li>
		<?php endforeach; ?>
	</ul>

	<form action="" method="post" class='form-stacked'>

		<div class='clearfix'>
			<label for="name">Layout name</label>
			<div class='input'><input type="text" placeholder="Layout name" name="name" value="" class='span16' /></div>
		</div>

		<div class='clearfix'>
			<label for="data">Code</label>
			<div class='input'><textarea name="data" class='span16' style='height: 600px; font: 12px monospace;' ></textarea></div>
		</div>

		<input type="hidden" name="token" value="<?php echo App::$user->original('csrf') ?>" />

		<div class='actions span16' >
			<a class='btn small' href='<?php echo $base ?>/layouts/'>Cancel</a>
			<button type="submit" class='btn primary pull-right'>Create layout</button>
		</div>
	</form>
</div>
