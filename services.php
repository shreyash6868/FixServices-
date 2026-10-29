
<?php
require_once 'config.php';

$pageTitle = 'Find Services';

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['category'] ?? 0);
$district = trim($_GET['district'] ?? '');
$taluka = trim($_GET['taluka'] ?? '');
$town = trim($_GET['town'] ?? '');

$districts = [
    'Ahilyanagar', 'Akola', 'Amravati', 'Beed', 'Bhandara',
    'Buldhana', 'Chandrapur', 'Chhatrapati Sambhajinagar',
    'Dharashiv', 'Dhule', 'Gadchiroli', 'Gondia', 'Hingoli',
    'Jalgaon', 'Jalna', 'Kolhapur', 'Latur', 'Mumbai City',
    'Mumbai Suburban', 'Nagpur', 'Nanded', 'Nandurbar', 'Nashik',
    'Palghar', 'Parbhani', 'Pune', 'Raigad', 'Ratnagiri',
    'Sangli', 'Satara', 'Sindhudurg', 'Solapur', 'Thane',
    'Wardha', 'Washim', 'Yavatmal'
];

$categories = $pdo->query(
    'SELECT id, name FROM categories WHERE active = 1 ORDER BY name'
)->fetchAll();

$sql = "SELECT
            s.*,
            c.name AS category,
            u.name AS provider,
            u.phone AS provider_phone,
            u.address AS provider_address,
            u.state AS provider_state,
            u.district AS provider_district,
            u.taluka AS provider_taluka,
            u.town AS provider_town,
            (SELECT ROUND(AVG(r.rating), 1)
             FROM reviews r
             WHERE r.service_id = s.id) AS avg_rating
        FROM services s
        JOIN categories c ON c.id = s.category_id
        JOIN users u ON u.id = s.provider_id
        WHERE s.active = 1
          AND c.active = 1
          AND u.provider_status = 'approved'
          AND u.role = 'provider'";

$params = [];


if ($q !== '') {
    $sql .= " AND (
        s.title LIKE ?
        OR s.description LIKE ?
        OR c.name LIKE ?
        OR u.name LIKE ?
        OR u.address LIKE ?
        OR u.district LIKE ?
        OR u.taluka LIKE ?
        OR u.town LIKE ?
    )";

    $like = "%$q%";

    array_push(
        $params,
        $like, $like, $like, $like,
        $like, $like, $like, $like
    );
}


if ($cat > 0) {
    $sql .= ' AND c.id = ?';
    $params[] = $cat;
}


if ($district !== '') {
    $sql .= ' AND u.district = ?';
    $params[] = $district;
}


if ($taluka !== '') {
    $sql .= ' AND u.taluka LIKE ?';
    $params[] = "%$taluka%";
}



if ($town !== '') {
    $sql .= ' AND u.town LIKE ?';
    $params[] = "%$town%";
}



$sql .= " ORDER BY
    CASE WHEN u.town = ? AND ? <> '' THEN 0 ELSE 1 END,
    CASE WHEN u.taluka = ? AND ? <> '' THEN 0 ELSE 1 END,
    CASE WHEN u.district = ? AND ? <> '' THEN 0 ELSE 1 END,
    s.created_at DESC";

array_push(
    $params,
    $town, $town,
    $taluka, $taluka,
    $district, $district
);

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="mb-4">
    
    <h1 class="fw-bold">Find Local Service Providers</h1>
    <p class="text-muted">
        Search home services by district, taluka and town in Maharashtra.
    </p>

<script src="https://cdn.botpress.cloud/webchat/v5.0/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/10/28/10/20261028104204-F8OESBCU.js" defer></script>



    <form method="get" class="card panel p-3 mb-4">
        <div class="row g-3">

            

            <div class="col-md-6">
                <label class="form-label">Service category</label>
                <select class="form-select" name="category">
                    <option value="0">All categories</option>

                    <?php foreach ($categories as $c): ?>
                        <option
                            value="<?= (int)$c['id'] ?>"
                            <?= $cat === (int)$c['id'] ? 'selected' : '' ?>
                        >
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">District</label>
                <select class="form-select" name="district">
                    <option value="">All districts</option>

                    <?php foreach ($districts as $d): ?>
                        <option
                            value="<?= e($d) ?>"
                            <?= $district === $d ? 'selected' : '' ?>
                        >
                            <?= e($d) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Taluka</label>
                <input
                    class="form-control"
                    name="taluka"
                    placeholder="Enter taluka"
                    value="<?= e($taluka) ?>"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Town / Village</label>
                <input
                    class="form-control"
                    name="town"
                    placeholder="Enter town or village"
                    value="<?= e($town) ?>"
                >
            </div>

            <div class="col-12 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    Search Providers
                </button>

                <a href="services.php" class="btn btn-outline-secondary">
                    Clear Filters
                </a>
            </div>
        </div>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 mb-0">Available Services</h2>
    <span class="text-muted small">
        <?= count($services) ?> result(s) found
    </span>
</div>

<div class="row g-3">

    <?php if (!$services): ?>
        <div class="col-12">
            <div class="alert alert-info">
                No matching providers found in this location.
                Try another town, taluka, district or category.
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($services as $s): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card service-card h-100 p-3">
                <div class="card-body d-flex flex-column">

                    <span class="badge text-bg-light align-self-start">
                        <?= e($s['category']) ?>
                    </span>

                    <h4 class="mt-3"><?= e($s['title']) ?></h4>

                    <p class="text-muted">
                        <?= e($s['description']) ?>
                    </p>

                    <p class="mb-1">
                        Provider:
                        <strong><?= e($s['provider']) ?></strong>
                    </p>

                    <p class="small mb-1">
                        <strong>Town:</strong>
                        <?= e($s['provider_town'] ?: 'Not specified') ?>
                    </p>

                    <p class="small mb-1">
                        <strong>Taluka:</strong>
                        <?= e($s['provider_taluka'] ?: 'Not specified') ?>
                    </p>

                    <p class="small mb-2">
                        <strong>District:</strong>
                        <?= e($s['provider_district'] ?: 'Not specified') ?>
                    </p>

                    <p class="muted-small">
                        <?= e($s['provider_address'] ?: 'Address not provided') ?>
                    </p>

                    <p class="price">
                        ₹<?= number_format((float)$s['price'], 2) ?> onwards
                    </p>

                    <p class="small">
                        Rating:
                        <?php if ($s['avg_rating'] !== null): ?>
                            ⭐ <?= e($s['avg_rating']) ?>/5
                        <?php else: ?>
                            Not rated yet
                        <?php endif; ?>
                    </p>

                    <a
                        class="btn btn-primary mt-auto"
                        href="book.php?id=<?= (int)$s['id'] ?>"
                    >
                        Book This Service
                    </a>

                </div>
            </div>
        </div>


        
    <?php endforeach; ?>

</div>

<?php include 'includes/footer.php'; ?>
