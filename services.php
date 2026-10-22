<?php
require_once 'config.php';
$pageTitle='Services';
$q=trim($_GET['q']??''); $cat=(int)($_GET['category']??0);
$categories=$pdo->query('SELECT id,name FROM categories WHERE active=1 ORDER BY name')->fetchAll();
$sql="SELECT s.*,c.name category,u.name provider,u.address provider_address,
 (SELECT ROUND(AVG(r.rating),1) FROM reviews r WHERE r.service_id=s.id) avg_rating
 FROM services s JOIN categories c ON c.id=s.category_id JOIN users u ON u.id=s.provider_id
 WHERE s.active=1 AND u.provider_status='approved'";
$params=[];
if($q!==''){ $sql.=" AND (s.title LIKE ? OR s.description LIKE ? OR c.name LIKE ? OR u.name LIKE ? OR u.address LIKE ?)"; $like="%$q%"; array_push($params,$like,$like,$like,$like,$like); }
if($cat>0){$sql.=' AND c.id=?';$params[]=$cat;}
$sql.=' ORDER BY s.created_at DESC';
$stmt=$pdo->prepare($sql);$stmt->execute($params);$services=$stmt->fetchAll();
include 'includes/header.php';
?>
<div class="mb-4"><h1 class="fw-bold">Find a service</h1><p class="text-muted">Search local professionals for your home needs.</p>
<form class="row g-2"><div class="col-md-7"><input class="form-control form-control-lg" name="q" placeholder="Try plumbing, electrician, cleaning..." value="<?= e($q) ?>"></div><div class="col-md-3"><select class="form-select form-select-lg" name="category"><option value="0">All categories</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $cat===(int)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div><div class="col-md-2"><button class="btn btn-primary btn-lg w-100">Search</button></div></form></div>
<div class="row g-3">
<?php if(!$services): ?><div class="col-12"><div class="alert alert-info">No matching services found. Try another search or check back later.</div></div><?php endif; ?>
<?php foreach($services as $s): ?><div class="col-md-6 col-lg-4"><div class="card service-card h-100 p-3"><div class="card-body d-flex flex-column"><span class="badge text-bg-light align-self-start"><?= e($s['category']) ?></span><h4 class="mt-3"><?= e($s['title']) ?></h4><p class="text-muted"><?= e($s['description']) ?></p><p class="mb-1">Provider: <strong><?= e($s['provider']) ?></strong></p><p class="muted-small"><?= e($s['provider_address']?:'Local service provider') ?></p><p class="price">₹<?= number_format((float)$s['price'],2) ?> onwards</p><p class="small">Rating: <?= $s['avg_rating']!==null?'⭐ '.e($s['avg_rating']).'/5':'Not rated yet' ?></p><a class="btn btn-primary mt-auto" href="book.php?id=<?= (int)$s['id'] ?>">Book this service</a></div></div></div><?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
