<?php
require_once 'pharma_config.php';
require_once 'db.php';
require_once 'SecureSession.php';
require_once 'ImageConverter.php';

SecureSession::init();
SecureSession::requireLogin();

header('Content-Type: application/json');

$product_id = intval($_GET['id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$db = getDB();

// Get product details
$query = "SELECT p.*, c.name as category_name,
          (SELECT image_name FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.id = :id";

$stmt = $db->prepare($query);
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // Format image path
    if ($product['primary_image']) {
        $product['primary_image'] = ImageConverter::getImagePath($product['primary_image']);
    }
    
    // Format price
    $product['price'] = number_format($product['price'], 2);
    
    echo json_encode([
        'success' => true,
        'product' => $product
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
}
