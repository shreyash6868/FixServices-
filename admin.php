<?php
require_once 'config.php';require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$action=$_POST['action']??'';
 if($action==='provider'){
  $id=(int)($_POST['provider_id']??0);$status=$_POST['provider_status']??'';
  if(in_array($status,['approved','rejected'],true)){$stmt=$pdo->prepare("UPDATE users SET provider_status=? WHERE id=? AND role='provider'");$stmt->execute([$status,$id]);flash('Provider status updated.');}
  redirect('admin.php');
 }
 if($action==='category'){
  $name=trim($_POST['name']??'');$desc=trim($_POST['description']??'');
  if($name!==''){try{$stmt=$pdo->prepare('INSERT INTO categories(name,description) VALUES(?,?)');$stmt->execute([$name,$desc]);flash('Category added.');}catch(PDOException $e){flash('Category already exists or could not be added.','warning');}}
  redirect('admin.php');
 }
 if($action==='booking'){
  $id=(int)($_POST['booking_id']??0);$status=$_POST['status']??'';
  if(in_array($status,['cancelled','completed'],true)){$stmt=$pdo->prepare('UPDATE bookings SET status=? WHERE id=?');$stmt->execute([$status,$id]);flash('Booking updated.');}
  redirect('admin.php');
 }
}
$stats=[
 'Customers'=>(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
 'Providers'=>(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='provider'")->fetchColumn(),
 'Services'=>(int)$pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
 'Bookings'=>(int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn()
];
$providers=$pdo->query("SELECT id,name,email,phone,provider_status FROM users WHERE role='provider' ORDER BY created_at DESC")->fetchAll();
$categories=$pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$bookings=$pdo->query("SELECT b.id,b.booking_date,b.status,c.name customer,s.title service,p.name provider FROM bookings b JOIN users c ON c.id=b.customer_id JOIN services s ON s.id=b.service_id JOIN users p ON p.id=s.provider_id ORDER BY b.created_at DESC LIMIT 100")->fetchAll();
$complaints=$pdo->query("SELECT c.*,u.name user_name FROM complaints c JOIN users u ON u.id=c.user_id ORDER BY c.created_at DESC LIMIT 50")->fetchAll();
$pageTitle='Admin dashboard';include 'includes/header.php';
?>
<h1 class="fw-bold">Admin dashboard</h1><p class="text-muted">Manage FixServices from one place.</p>
<div class="row g-3 mb-4"><?php foreach($stats as $label=>$num): ?><div class="col-6 col-lg-3"><div class="card panel p-3"><div class="text-muted"><?= e($label) ?></div><div class="display-6 fw-bold"><?= $num ?></div></div></div><?php endforeach; ?></div>
<div class="row g-4"><div class="col-lg-7"><div class="card panel p-3"><h2 class="h5 fw-bold">Service providers</h2><div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Contact</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($providers as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= e($p['email']) ?><br><small><?= e($p['phone']) ?></small></td><td><?= e(ucfirst($p['provider_status']??'pending')) ?></td><td><?php if($p['provider_status']!=='approved'): ?><form method="post" class="d-inline"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="provider"><input type="hidden" name="provider_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="provider_status" value="approved"><button class="btn btn-sm btn-success">Approve</button></form><?php endif; ?> <?php if($p['provider_status']!=='rejected'): ?><form method="post" class="d-inline"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="provider"><input type="hidden" name="provider_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="provider_status" value="rejected"><button class="btn btn-sm btn-outline-danger">Reject</button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
<div class="col-lg-5"><div class="card panel p-3"><h2 class="h5 fw-bold">Add service category</h2><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="category"><div class="mb-2"><label class="form-label">Category name</label><input class="form-control" name="name" required></div><div class="mb-3"><label class="form-label">Description</label><input class="form-control" name="description"></div><button class="btn btn-primary">Add category</button></form><hr><h3 class="h6">Current categories</h3><ul class="mb-0"><?php foreach($categories as $c): ?><li><?= e($c['name']) ?></li><?php endforeach; ?></ul></div></div></div>
<div class="card panel p-3 mt-4"><h2 class="h5 fw-bold">Recent bookings</h2><div class="table-responsive"><table class="table"><thead><tr><th>ID</th><th>Customer</th><th>Service</th><th>Provider</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($bookings as $b): ?><tr><td>#<?= (int)$b['id'] ?></td><td><?= e($b['customer']) ?></td><td><?= e($b['service']) ?></td><td><?= e($b['provider']) ?></td><td><?= e($b['booking_date']) ?></td><td><?= e(ucwords(str_replace('_',' ',$b['status']))) ?></td><td><?php if(!in_array($b['status'],['cancelled','completed'],true)): ?><form method="post" class="d-flex gap-1"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="booking"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><select class="form-select form-select-sm" name="status"><option value="cancelled">Cancel</option><option value="completed">Complete</option></select><button class="btn btn-sm btn-outline-primary">Save</button></form><?php else: ?>—<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="card panel p-3 mt-4"><h2 class="h5 fw-bold">Customer complaints</h2><?php if(!$complaints): ?><p class="text-muted mb-0">No complaints have been submitted.</p><?php else: ?><div class="table-responsive"><table class="table"><thead><tr><th>User</th><th>Subject</th><th>Description</th><th>Status</th></tr></thead><tbody><?php foreach($complaints as $c): ?><tr><td><?= e($c['user_name']) ?></td><td><?= e($c['subject']) ?></td><td><?= e($c['description']) ?></td><td><?= e($c['status']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
<?php include 'includes/footer.php'; ?>
