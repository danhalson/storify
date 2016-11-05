<?php
	require 'vendor/autoload.php';
	require 'classes/ShopifyRequest.php';
	require 'main.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/storify.css" rel="stylesheet">

		<script src="bower_components/jquery/dist/jquery.min.js"></script>
		<script src="js/main.js"></script>

		<title>Storify</title>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<h1>Storify</h1>
			</div>

			<div class="row">
				<h4>Add a Product</h4>
			</div>

			<!-- Status message. -->
			<div class="row">
					<div id="status" class="alert <?= !empty($status['level']) ? 'alert-' . $status['level'] : '' ?>" role="alert"><?= !empty($status['message']) ? $status['message'] : '' ?></div>
			</div>

		  <div class="row">
				<!-- Product form. Ideally this would be generated dynamically. -->
				<form method="post">
					<div class="form-group">
						<label for="title" class="required">Title</label>
						<input name="title" class="form-control" type="text" value="<?= !empty($_POST['title']) ? $_POST['title'] : '' ?>">

						<label for="product_type" class="required">Type</label>
						<input name="product_type" class="form-control" type="text" value="<?= !empty($_POST['product_type']) ? $_POST['product_type'] : '' ?>">

						<label for="vendor" class="required">Vendor</label>
						<input name="vendor" class="form-control" type="text" value="<?= !empty($_POST['vendor']) ? $_POST['vendor'] : '' ?>">

						<label for="body_html">Description</label>
						<textarea name="body_html" class="form-control" rows="4"><?= !empty($_POST['body_html']) ? $_POST['body_html'] : '' ?></textarea>

						<label for="image_upload">Select an image to upload (.jpg, .png, .gif):</label>
						<input id="image_upload" type="file" accept="image/jpeg,image/x-png,image/png,image/gif" value="<?= !empty($_POST['image_upload']) ? $_POST['image_upload'] : '' ?>">
						<input id="image" name="images" type="hidden">

						<div class="panel panel-default drop_area">
							<div class="panel-body">
								Or drag and drop an image here.
							</div>
						</div>
					</div>
					<button type="submit" name="submit" value="add_product" class="btn btn-default">Add product</button>
				</form>
			</div>
		</div>
	</body>
</html>
