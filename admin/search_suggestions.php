<?php
require_once 'pharma_config.php';
require_once 'db.php';
require_once 'SecureSession.php';

SecureSession::init();
SecureSession::requireLogin();

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = getDB();
    
    // Updated search query - removed dosage, prescription, stock fields
    // FIXED: Use unique parameter names for each LIKE clause
    $searchQuery = "SELECT p.id, p.title, p.company_name, p.sku, c.name as category_name, p.price, p.unit
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.is_active = 1 
                    AND (p.title LIKE :search1 
                         OR p.keywords LIKE :search2 
                         OR p.composition LIKE :search3 
                         OR p.company_name LIKE :search4 
                         OR p.sku LIKE :search5)
                    ORDER BY 
                        CASE 
                            WHEN p.title LIKE :exact THEN 1
                            WHEN p.title LIKE :starts THEN 2
                            ELSE 3
                        END,
                        p.title
                    LIMIT 10";
    
    $stmt = $db->prepare($searchQuery);
    $searchParam = '%' . $query . '%';
    $exactParam = $query;
    $startsParam = $query . '%';
    
    // Bind all parameters with unique names
    $stmt->execute([
        ':search1' => $searchParam,
        ':search2' => $searchParam,
        ':search3' => $searchParam,
        ':search4' => $searchParam,
        ':search5' => $searchParam,
        ':exact' => $exactParam,
        ':starts' => $startsParam
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format results for display
    foreach ($results as &$result) {
        // Format price in USD
        $result['formatted_price'] = '$' . number_format($result['price'], 2);
    }
    
    echo json_encode($results);
    
} catch (PDOException $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode(['error' => 'Search failed']);
}