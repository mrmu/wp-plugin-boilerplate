<form method='post' action='' class="needs-validation" novalidate>
	<?php wp_nonce_field( 'wppb_nonce_action', 'wppb_nonce_name' ); ?>
	<div class="form-row">
		<div class="col-md-6 mb-3">
			<label>Plugin Name</label>
			<input type="text" class="form-control" name="plugin_name" placeholder="Plugin Name" required>
		</div>
		<div class="col-md-6 mb-3">
			<label>Plugin Slug</label>
			<input type="text" class="form-control" name="plugin_slug" placeholder="plugin-slug" required>
		</div>
		<div class="col-md-12 mb-3">
			<label>Plugin URI</label>
			<input type="text" class="form-control" name="plugin_uri" placeholder="Plugin URI" required>
		</div>
		<div class="col-md-6 mb-3">
			<label>Author Name</label>
			<input type="text" class="form-control" name="au_name" placeholder="Author Name" required>
		</div>
		<div class="col-md-6 mb-3">
			<label>Author Email</label>
			<input type="text" class="form-control" name="au_email" placeholder="Author EMail" required>
		</div>
		<div class="col-md-12 mb-3">
			<label>Author URI</label>
			<input type="text" class="form-control" name="au_uri" placeholder="Author URI" required>
		</div>
	</div>
	<input type='submit' name='create' class="btn btn-primary" value='Build Plugin' />
</form>