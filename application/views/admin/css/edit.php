<div class='span16'>
	<div class='page-header'>
		<h1>Edit layout <small><?php echo $data['name'] ?></small></h1>
	</div>

	<?php if ( ! empty($notices)): ?>
		<div class='alert-message success'>
			<p><?php echo $notices ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty($errors)): ?>
		<div class='alert-message block-message error'>
			<?php if (is_array($errors)): ?>
				<p><strong>Hey!</strong> Something wen't wrong when making your layout. Please fix the following errors: </p>
				<ul>
					<?php foreach($errors as $field => $error): ?>
						<li><?php echo $error ?></li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p><b><?php echo $errors; ?></b></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<form action="" method="post" class='form-stacked'>

		<div class='clearfix'>
			<label for="name">Layout name</label>
			<div class='input'><input type="text" placeholder="Layout name" name="name" value="<?php echo $data['name'] ?>" class='span16' /></div>
		</div>

		<div class='clearfix'>
			<label for="data">Code</label>
			<div class='input'><textarea name="data" class='span16' style='height: 600px; font: 12px monospace;' ><?php echo $data['data'] ?></textarea></div>
		</div>

		<input type='hidden' name='token' value='<?php echo Cookie::get('csrf') ?>' />

		<div class='actions span16' >
			<a class='btn small' href='<?php echo $base ?>/layouts/'>Cancel</a>
			<button type="submit" class='btn primary pull-right'>Edit layout</button>
		</div>
	</form>
</div>
