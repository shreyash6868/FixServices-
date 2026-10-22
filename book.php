<?php
require_once 'config.php';
require_role('customer');
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT s.*,c.name category,u.name provider FROM services s JOIN categories c ON c.id=s.category_id JOIN users u ON u.id=s.provider_id WHERE s.id=? AND s.active=1 AND u.provider_status='approved'");
$stmt->execute([$id]);$service=$stmt->fetch();
if(!$service){http_response_code(404);exit('Service not found or provider is not approved.');}
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $date=$_POST['booking_date']??'';$time=$_POST['booking_time']??'';$address=trim($_POST['address']??'');$desc=trim($_POST['problem_description']??'');
 if(!$date || $date<date('Y-m-d'))$errors[]='Choose today or a future date.';
 if(!$time)$errors[]='Choose a time.';
 if($address==='')$errors[]='Enter the service address.';
 if(!$errors){
  $stmt=$pdo->prepare("INSERT INTO bookings(customer_id,service_id,booking_date,booking_time,address,problem_description) VALUES(?,?,?,?,?,?)");
  $stmt->execute([current_user()['id'],$id,$date,$time,$address,$desc]);
  flash('Booking request submitted. You can track it in My Bookings.');redirect('my_bookings.php');
 }
}
$pageTitle='Book service';include 'includes/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card panel p-4 p-md-5"><span class="badge text-bg-light align-self-start"><?= e($service['category']) ?></span><h1 class="h3 fw-bold mt-2">Book <?= e($service['title']) ?></h1><p class="text-muted">Provider: <?= e($service['provider']) ?> · Starting at ₹<?= number_format((float)$service['price'],2) ?></p>
<?php foreach($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="row"><div class="col-md-6 mb-3"><label class="form-label">Preferred date</label><input class="form-control" type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required value="<?= e($_POST['booking_date']??'') ?>"></div><div class="col-md-6 mb-3"><label class="form-label">Preferred time</label><input class="form-control" type="time" name="booking_time" required value="<?= e($_POST['booking_time']??'') ?>"></div></div>
<div class="mb-3"><label class="form-label">Full service address</label><textarea class="form-control" name="address" rows="2" required><?= e($_POST['address']??current_user()['address']??'') ?></textarea></div>
<div class="mb-3"><label class="form-label">Describe the problem (optional)</label><textarea class="form-control" name="problem_description" rows="3" placeholder="Tell the provider what needs to be fixed"><?= e($_POST['problem_description']??'') ?></textarea></div>
<button class="btn btn-primary btn-lg">Confirm booking request</button> <a href="services.php" class="btn btn-outline-secondary btn-lg">Back</a></form></div></div></div>
<?php include 'includes/footer.php'; ?>
