<?php
require_once 'config.php';require_role('provider');
$uid=current_user()['id'];
$stmt=$pdo->prepare("SELECT s.*,c.name category FROM services s JOIN categories c ON c.id=s.category_id WHERE s.provider_id=? ORDER BY s.created_at DESC");$stmt->execute([$uid]);$services=$stmt->fetchAll();
$stmt=$pdo->prepare("SELECT b.*,s.title service,u.name customer,u.phone customer_phone FROM bookings b JOIN services s ON s.id=b.service_id JOIN users u ON u.id=b.customer_id WHERE s.provider_id=? ORDER BY b.created_at DESC");$stmt->execute([$uid]);$bookings=$stmt->fetchAll();
$categories=$pdo->query('SELECT id,name FROM categories WHERE active=1 ORDER BY name')->fetchAll();
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$action=$_POST['action']??'';
 if($action==='add_service'){
  $title=trim($_POST['title']??'');$cat=(int)($_POST['category_id']??0);$price=$_POST['price']??'';$desc=trim($_POST['description']??'');
  if(current_user()['provider_status']!=='approved'){$err='Your provider account must be approved by an admin before adding services.';}
  elseif($title===''||$cat<1||!is_numeric($price)||(float)$price<0){$err='Enter a service title, category, and valid price.';}
  else{$stmt=$pdo->prepare('INSERT INTO services(provider_id,category_id,title,description,price) VALUES(?,?,?,?,?)');$stmt->execute([$uid,$cat,$title,$desc,(float)$price]);flash('Service added.');redirect('provider_dashboard.php');}
 } elseif($action==='status'){
  $bid=(int)($_POST['booking_id']??0);$new=$_POST['status']??'';
  $allowed=['accepted'=>['pending'],'rejected'=>['pending'],'in_progress'=>['accepted'],'completed'=>['in_progress']];
  if(!isset($allowed[$new])){$err='Invalid status change.';}
  else{
   $stmt=$pdo->prepare('SELECT b.status FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.id=? AND s.provider_id=?');$stmt->execute([$bid,$uid]);$row=$stmt->fetch();
   if(!$row||!in_array($row['status'],$allowed[$new],true))$err='That booking status cannot be changed to the selected value.';
   else{$stmt=$pdo->prepare('UPDATE bookings SET status=? WHERE id=?');$stmt->execute([$new,$bid]);flash('Booking status updated.');redirect('provider_dashboard.php');}
  }
 }
}
$pageTitle='Provider dashboard';include 'includes/header.php';
?>
<div class="d-flex justify-content-between flex-wrap align-items-center mb-3"><div><h1 class="fw-bold">Provider dashboard</h1><p class="text-muted mb-0">Manage your services and incoming requests.</p></div><span class="badge text-bg-<?= current_user()['provider_status']==='approved'?'success':'warning' ?> fs-6"><?= e(ucfirst(current_user()['provider_status']??'pending')) ?> account</span></div>
<?php if(current_user()['provider_status']!=='approved'): ?><div class="alert alert-warning">Your account is waiting for admin approval. You can view this dashboard, but your services cannot be listed until approved.</div><?php endif; ?>
<?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<div class="row g-4"><div class="col-lg-4"><div class="card panel p-4"><h2 class="h5 fw-bold">Add a service</h2><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add_service"><div class="mb-3"><label class="form-label">Service title</label><input class="form-control" name="title" required placeholder="e.g. Fan installation"></div><div class="mb-3"><label class="form-label">Category</label><select class="form-select" name="category_id" required><option value="">Choose category</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div><div class="mb-3"><label class="form-label">Starting price (₹)</label><input class="form-control" type="number" name="price" min="0" step="0.01" required></div><div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"></textarea></div><button class="btn btn-primary" <?= current_user()['provider_status']!=='approved'?'disabled':'' ?>>Add service</button></form></div></div>
<div class="col-lg-8"><div class="card panel p-3"><h2 class="h5 fw-bold">Your services</h2><div class="table-responsive"><table class="table"><thead><tr><th>Service</th><th>Category</th><th>Price</th></tr></thead><tbody><?php if(!$services): ?><tr><td colspan="3">No services added.</td></tr><?php endif; ?><?php foreach($services as $s): ?><tr><td><?= e($s['title']) ?></td><td><?= e($s['category']) ?></td><td>₹<?= number_format((float)$s['price'],2) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<div class="card panel p-3 mt-4"><h2 class="h5 fw-bold">Booking requests</h2><div class="table-responsive"><table class="table"><thead><tr><th>Service / Customer</th><th>Date</th><th>Address / Problem</th><th>Status</th><th>Update</th></tr></thead><tbody>
<?php if(!$bookings): ?><tr><td colspan="5">No booking requests yet.</td></tr><?php endif; ?>
<?php foreach($bookings as $b): ?><tr><td><strong><?= e($b['service']) ?></strong><br><?= e($b['customer']) ?><br><small><?= e($b['customer_phone']) ?></small></td><td><?= e($b['booking_date']) ?><br><?= e(substr($b['booking_time'],0,5)) ?></td><td><?= e($b['address']) ?><br><small><?= e($b['problem_description']) ?></small></td><td><?= e(ucwords(str_replace('_',' ',$b['status']))) ?></td><td>
<?php $next=['pending'=>['accepted'=>'Accept','rejected'=>'Reject'],'accepted'=>['in_progress'=>'Start'],'in_progress'=>['completed'=>'Complete']]; if(isset($next[$b['status']])): ?><div class="d-flex gap-1 flex-wrap"><?php foreach($next[$b['status']] as $status=>$label): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="status" value="<?= e($status) ?>"><button class="btn btn-sm <?= $status==='rejected'?'btn-outline-danger':'btn-outline-primary' ?>"><?= e($label) ?></button></form><?php endforeach; ?></div><?php else: ?>—<?php endif; ?>
</td></tr><?php endforeach; ?></tbody></table></div></div>
<?php include 'includes/footer.php'; ?>
