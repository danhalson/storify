<?php
	try {
		$shopify = new ShopifyRequest();

		// Keep control over posted values.
		$allowable_values = [
			'title' => ['title' => 'Title', 'required' => true],
			'product_type' => ['title' => 'Type', 'required' => true],
			'vendor' => ['title' => 'Vendor', 'required' => true],
			'body_html' => ['title' => 'Description'],
			'images' => ['title' => 'Image']
		];

		// Handle the form.
		if (!empty($_POST['submit']) && $_POST['submit'] === 'add_product') {
			// Build the payload.
			$payload = ['product' => []];

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
		}
	} catch (Exception $e) {
		$status = ['level' => 'danger', 'message' => $e->getMessage()];
	}
?>
