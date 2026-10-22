<?php
require_once 'config.php';require_role('customer');
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT b.id,b.service_id,s.title FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.id=? AND b.customer_id=? AND b.status='completed'");
$stmt->execute([$id,current_user()['id']]);$booking=$stmt->fetch();
if(!$booking){http_response_code(404);exit('Completed booking not found.');}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$rating=(int)($_POST['rating']??0);$comment=trim($_POST['comment']??'');
 if($rating<1||$rating>5)$err='Choose a rating from 1 to 5.';
 else{
  try{$stmt=$pdo->prepare('INSERT INTO reviews(booking_id,customer_id,service_id,rating,comment) VALUES(?,?,?,?,?)');$stmt->execute([$id,current_user()['id'],$booking['service_id'],$rating,$comment]);flash('Thank you for your review.');redirect('my_bookings.php');}
  catch(PDOException $e){$err='A review may already exist for this booking.';}
 }
}
$pageTitle='Write a review';include 'includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-7"><div class="card panel p-4"><h1 class="h3">Review your service</h1><p><?= e($booking['title']) ?></p><?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="mb-3"><label class="form-label">Rating</label><select class="form-select" name="rating" required><option value="">Select rating</option><?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?> / 5</option><?php endfor; ?></select></div><div class="mb-3"><label class="form-label">Comment</label><textarea class="form-control" name="comment" rows="4"></textarea></div><button class="btn btn-primary">Submit review</button></form></div></div></div>
<?php include 'includes/footer.php'; ?>
