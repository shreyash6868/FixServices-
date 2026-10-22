<?php
require_once 'config.php'; require_role('customer');
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$id=(int)($_POST['booking_id']??0);
 $stmt=$pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND customer_id=? AND status='pending'");
 $stmt->execute([$id,current_user()['id']]);flash($stmt->rowCount()?'Booking cancelled.':'Only pending bookings can be cancelled.',$stmt->rowCount()?'success':'warning');redirect('my_bookings.php');
}
$stmt=$pdo->prepare("SELECT b.*,s.title service,s.price,u.name provider,c.name category,r.id review_id FROM bookings b JOIN services s ON s.id=b.service_id JOIN users u ON u.id=s.provider_id JOIN categories c ON c.id=s.category_id LEFT JOIN reviews r ON r.booking_id=b.id WHERE b.customer_id=? ORDER BY b.created_at DESC");
$stmt->execute([current_user()['id']]);$bookings=$stmt->fetchAll();
$pageTitle='My bookings';include 'includes/header.php';
?>
<h1 class="fw-bold">My bookings</h1><p class="text-muted">Track the progress of your service requests.</p>
<div class="card panel p-3"><div class="table-responsive"><table class="table"><thead><tr><th>Service</th><th>Provider</th><th>Date & time</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php if(!$bookings): ?><tr><td colspan="5" class="text-center py-4">No bookings yet. <a href="services.php">Browse services</a>.</td></tr><?php endif; ?>
<?php foreach($bookings as $b): ?><tr><td><strong><?= e($b['service']) ?></strong><div class="small text-muted"><?= e($b['category']) ?></div></td><td><?= e($b['provider']) ?></td><td><?= e($b['booking_date']) ?><br><?= e(substr($b['booking_time'],0,5)) ?></td><td><span class="badge text-bg-<?= in_array($b['status'],['completed'])?'success':(in_array($b['status'],['rejected','cancelled'])?'danger':'primary') ?>"><?= e(ucwords(str_replace('_',' ',$b['status']))) ?></span></td><td class="d-flex gap-2 flex-wrap">
<?php if($b['status']==='pending'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?')">Cancel</button></form><?php endif; ?>
<?php if($b['status']==='completed' && !$b['review_id']): ?><a class="btn btn-sm btn-outline-primary" href="review.php?id=<?= (int)$b['id'] ?>">Review</a><?php endif; ?>
</td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php include 'includes/footer.php'; ?>
