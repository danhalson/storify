<?php
	require 'vendor/autoload.php';
	require 'classes/Authenticator.php';
	require 'classes/ShopifyRequest.php';

	// Some basic auth.
	$access = false;

	// Authenticate the user.
	try {
		$auth = new Authenticator();

		// First check if we have a valid session.
		$access = $auth->checkSession();

		// Failing that, authenticate as normal.
		if (!empty($_POST['submit']) && $_POST['submit'] === 'login' && !$access) {
			$user = !empty($_POST['user']) ? $_POST['user'] : '';
			$pass = !empty($_POST['pass']) ? $_POST['pass'] : '';

			$access = $auth->setCredentials($user, $pass)->checkCredentials();
		}
	} catch (Exception $e) {
		$status = ['level' => 'danger', 'message' => $e->getMessage()];
	}

	// ...We're authenticated
	if (!empty($_POST['submit']) && $_POST['submit'] === 'add_product') {
		try {
			$shopify = new ShopifyRequest();

			// Build the payload.
			$payload = ['product' => []];

			// Keep control over posted values.
			$allowable_values = [
				'title' => ['title' => 'Title', 'required' => true],
				'product_type' => ['title' => 'Type', 'required' => true],
				'vendor' => ['title' => 'Vendor', 'required' => true],
				'body_html' => ['title' => 'Description'],
				'images' => ['title' => 'Image']
			];

			foreach ($allowable_values as $field => $value) {
				if ((isset($value['required']) && $value['required']) && empty($_POST[$field])) {
					$status = ['level' => 'danger', 'message' => "{$value['title']} is a required field."];
					break;
				}

				// Images are stored as an array of arrays.
				if ($field === 'images') {
					$payload['product'][$field][]['attachment'] = !empty($_POST[$field]) ? $_POST[$field] : null;
				} else {
					$payload['product'][$field] = !empty($_POST[$field]) ? $_POST[$field] : null;
				}
			}

			// If nothing went awry, do the request.
			if (empty($status)) {
				$shopify->doRequest('POST', 'admin/products.json', $payload);
				$status = ['level' => 'success', 'message' => 'Product added successfully.'];
			}
		} catch (Exception $e) {
			$status = ['level' => 'danger', 'message' => $e->getMessage()];
		}
	}
?>
