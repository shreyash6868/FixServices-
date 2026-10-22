<?php
require_once 'config.php';
$pageTitle='Home';
$counts=$pdo->query("SELECT COUNT(*) FROM categories WHERE active=1")->fetchColumn();
$services=$pdo->query("SELECT s.id,s.title,s.description,s.price,c.name category,u.name provider FROM services s JOIN categories c ON c.id=s.category_id JOIN users u ON u.id=s.provider_id WHERE s.active=1 AND u.provider_status='approved' ORDER BY s.created_at DESC LIMIT 6")->fetchAll();
include 'includes/header.php';
?>
<section class="hero mb-5">
 <span class="badge text-bg-warning mb-3">Local help, made simple</span>
 <h1 class="display-5 fw-bold">Home services, without the hassle.</h1>
 <p class="lead mt-3">Find local professionals for plumbing, electrical work, cleaning, repairs and more. Choose a service, send a booking request, and track it online.</p>
 <a class="btn btn-warning btn-lg mt-2 me-2" href="services.php">Explore services</a>
 <?php if (!current_user()): ?><a class="btn btn-outline-light btn-lg mt-2" href="register.php">Create account</a><?php endif; ?>
</section>
<div class="row g-3 mb-5">
 <?php foreach ([['🏠','Local services','Find help for everyday home needs.'],['📅','Easy booking','Choose a date and time that suits you.'],['⭐','Reviews','Share feedback after a completed job.']] as $item): ?>
 <div class="col-md-4"><div class="card service-card h-100 p-4"><div class="service-icon"><?= $item[0] ?></div><h5 class="mt-2"><?= e($item[1]) ?></h5><p class="text-muted mb-0"><?= e($item[2]) ?></p></div></div>
 <?php endforeach; ?>
</div>
<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="fw-bold">Recently added services</h2><p class="text-muted mb-0">Available services from approved providers.</p></div><a href="services.php">View all →</a></div>
<div class="row g-3">
<?php if (!$services): ?><div class="col-12"><div class="alert alert-info">No services have been listed yet. A provider can register, wait for admin approval, and add a service.</div></div><?php endif; ?>
<?php foreach($services as $s): ?><div class="col-md-4"><div class="card service-card p-3 h-100"><div class="card-body"><span class="badge text-bg-light"><?= e($s['category']) ?></span><h5 class="mt-3"><?= e($s['title']) ?></h5><p class="text-muted"><?= e($s['description']) ?></p><p class="price">₹<?= number_format((float)$s['price'],2) ?> onwards</p><p class="small">Provider: <?= e($s['provider']) ?></p><a class="btn btn-primary" href="book.php?id=<?= (int)$s['id'] ?>">Book service</a></div></div></div><?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
