<?php


function getAllCategories() {
    return getDB()->query("SELECT * FROM categories ORDER BY id")->fetchAll();
}

function getCategoryCounts() {
    return getDB()->query("SELECT category_id, COUNT(*) as cnt FROM products GROUP BY category_id")->fetchAll(PDO::FETCH_KEY_PAIR);
}


function _buildProductFilters(array $filters) {
    $where = ['1=1'];
    $params = [];

    if (!empty($filters['cat'])) { $where[] = 'c.slug = ?'; $params[] = (string)$filters['cat']; }
    if (!empty($filters['q'])) {
        $where[] = '(p.title LIKE ? OR p.author LIKE ? OR p.description LIKE ?)';
        $like = "%{$filters['q']}%";
        $params[] = $like; $params[] = $like; $params[] = $like;
    }
    if (!empty($filters['promo']))    $where[] = 'p.old_price IS NOT NULL';
    if (!empty($filters['featured'])) $where[] = 'p.featured = 1';
    if (!empty($filters['is_new']))   $where[] = 'p.is_new = 1';
    if (!empty($filters['author'])) { $where[] = 'p.author = ?'; $params[] = (string)$filters['author']; }
    if (!empty($filters['max_price'])) { $where[] = 'p.price <= ?'; $params[] = (float)$filters['max_price']; }

    return [implode(' AND ', $where), $params];
}


function getProducts(array $filters = [], string $orderBy = 'p.featured DESC, p.id ASC', int $limit = 16, int $offset = 0) {
    list($whereStr, $params) = _buildProductFilters($filters);

    $sql = "SELECT p.*, c.name as cat_name, c.slug as cat_slug
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE $whereStr
            ORDER BY $orderBy
            LIMIT $limit OFFSET $offset";

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countProducts(array $filters = []) {
    list($whereStr, $params) = _buildProductFilters($filters);
    $sql = "SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE $whereStr";

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getProductBySlug($slug) {
    $stmt = getDB()->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
    $stmt->execute([(string)$slug]);
    return $stmt->fetch() ?: null;
}

function getProductById($id) {
    $stmt = getDB()->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([(string)$id]);
    return $stmt->fetch() ?: null;
}

function getRelatedProducts($categoryId, $excludeId, $limit = 8) {
    $stmt = getDB()->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? ORDER BY RAND() LIMIT $limit");
    $stmt->execute([(int)$categoryId, (int)$excludeId]);
    return $stmt->fetchAll();
}


function getFilteredBundles(array $filters = [], string $sort = 'id DESC', int $limit = 100) {
    $where = ['1=1']; $params = [];
    if (!empty($filters['q'])) { $where[] = '(name LIKE ? OR description LIKE ?)'; $like = "%{$filters['q']}%"; $params[] = $like; $params[] = $like; }
    if (!empty($filters['promo']))    $where[] = 'old_price IS NOT NULL';
    if (!empty($filters['max_price'])) { $where[] = 'price <= ?'; $params[] = (float)$filters['max_price']; }

    $orderBy = ($sort === 'price_asc') ? 'price ASC' : (($sort === 'price_desc') ? 'price DESC' : 'id DESC');
    $sql = "SELECT * FROM bundles WHERE ".implode(' AND ', $where)." ORDER BY $orderBy LIMIT $limit";

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAllBundles($limit = 100) { return getFilteredBundles([], 'id DESC', $limit); }

function getBundleBySlug($slug) {
    $stmt = getDB()->prepare("SELECT * FROM bundles WHERE slug = ?");
    $stmt->execute([(string)$slug]);
    return $stmt->fetch() ?: null;
}

function getBundleById($id) {
    $stmt = getDB()->prepare("SELECT * FROM bundles WHERE id = ?");
    $stmt->execute([(string)$id]);
    return $stmt->fetch() ?: null;
}

function getBundleProducts($bundleId) {
    $stmt = getDB()->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN bundle_products bp ON bp.product_id = p.id JOIN categories c ON p.category_id = c.id WHERE bp.bundle_id = ?");
    $stmt->execute([(int)$bundleId]);
    return $stmt->fetchAll();
}

function getRandomBundles($limit = 4) {
    return getDB()->query("SELECT * FROM bundles ORDER BY RAND() LIMIT $limit")->fetchAll();
}


function createOrder(array $data) {
    $db = getDB();
    
    $db->beginTransaction();
    try {
        $stmt = $db->prepare("INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, customer_address, city, total, status, notes) VALUES ('',?,?,?,?,?,?, 'pending', ?)");
        $stmt->execute([ $data['name'], $data['email'], $data['phone'], $data['address'], $data['city'], $data['total'], $data['notes'] ]);
        $id = (int)$db->lastInsertId();
        $db->prepare("UPDATE orders SET order_number = ? WHERE id = ?")->execute([(string)$id, $id]);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    return $id;
}

function addOrderItem($orderId, $item) {
    
    $rawId = $item['id'] ?? null;
    $productId = null;
    if ($rawId !== null && strpos((string)$rawId, 'b_') !== 0) {
        $productId = (int)$rawId;
    }
    getDB()->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)")
           ->execute([$orderId, $productId, $item['qty'], $item['price']]);

    
    if ($productId) {
        getDB()->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?")
               ->execute([$item['qty'], $productId]);
    }
}


function checkStock(int $productId, int $qty): bool {
    $stmt = getDB()->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $row = $stmt->fetch();
    return $row && (int)$row['stock'] >= $qty;
}

function getOrdersByEmail($email, $limit = 20) {
    $stmt = getDB()->prepare("SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC LIMIT $limit");
    $stmt->execute([(string)$email]);
    return $stmt->fetchAll();
}

function createDevis(array $data) {
    return getDB()->prepare("INSERT INTO devis (name, email, format_type, pages, qty, cover_type, message) VALUES (?,?,?,?,?,?,?)")
              ->execute([ $data['name'], $data['email'], $data['format'], $data['pages'], $data['qty'], $data['cover'], $data['message'] ]);
}

function getWishlistItems($userId) {
    $stmt = getDB()->prepare("SELECT p.*, c.name as cat_name FROM wishlist w JOIN products p ON w.product_id = p.id JOIN categories c ON p.category_id = c.id WHERE w.user_id = ?");
    $stmt->execute([(int)$userId]);
    return $stmt->fetchAll();
}

