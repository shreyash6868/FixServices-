<?php
require_once 'config.php';require_login();
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$subject=trim($_POST['subject']??'');$description=trim($_POST['description']??'');$bid=(int)($_POST['booking_id']??0);$bid=$bid>0?$bid:null;
 if($subject===''||$description==='')$err='Enter a subject and description.';
 else{
  if($bid){$stmt=$pdo->prepare('SELECT id FROM bookings WHERE id=? AND customer_id=?');$stmt->execute([$bid,current_user()['id']]);if(!$stmt->fetch())$bid=null;}
  $stmt=$pdo->prepare('INSERT INTO complaints(user_id,booking_id,subject,description) VALUES(?,?,?,?)');$stmt->execute([current_user()['id'],$bid,$subject,$description]);flash('Your complaint has been submitted.');redirect('complaint.php');
 }
}
$stmt=$pdo->prepare('SELECT * FROM complaints WHERE user_id=? ORDER BY created_at DESC');$stmt->execute([current_user()['id']]);$rows=$stmt->fetchAll();
$pageTitle='Complaints';include 'includes/header.php';
?>
<div class="row g-4"><div class="col-lg-5"><div class="card panel p-4"><h1 class="h4">Submit a complaint</h1><?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="mb-3"><label class="form-label">Subject</label><input class="form-control" name="subject" required></div><div class="mb-3"><label class="form-label">Related booking ID (optional)</label><input class="form-control" type="number" name="booking_id" min="1"></div><div class="mb-3"><label class="form-label">Details</label><textarea class="form-control" name="description" rows="4" required></textarea></div><button class="btn btn-primary">Submit complaint</button></form></div></div><div class="col-lg-7"><div class="card panel p-4"><h2 class="h4">Your complaints</h2><?php if(!$rows): ?><p class="text-muted">No complaints yet.</p><?php endif; ?><?php foreach($rows as $r): ?><div class="border-bottom py-3"><strong><?= e($r['subject']) ?></strong><span class="badge text-bg-light ms-2"><?= e($r['status']) ?></span><p class="mb-1"><?= e($r['description']) ?></p><?php if($r['admin_response']): ?><p class="small text-success">Admin response: <?= e($r['admin_response']) ?></p><?php endif; ?></div><?php endforeach; ?></div></div></div>
<?php include 'includes/footer.php'; ?>
