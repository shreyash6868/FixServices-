<?php
require_once 'config.php';
if (current_user()) redirect('index.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();
 $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
 $stmt=$pdo->prepare('SELECT id,name,email,password,role,provider_status FROM users WHERE email=?'); $stmt->execute([$email]); $u=$stmt->fetch();
 if ($u && password_verify($password,$u['password'])) {
  session_regenerate_id(true); unset($u['password']); $_SESSION['user']=$u;
  flash('Welcome back, '.$u['name'].'!');
  if ($u['role']==='admin') redirect('admin.php');
  if ($u['role']==='provider') redirect('provider_dashboard.php');
  redirect('index.php');
 } else $error='Email or password is incorrect.';
}
$pageTitle='Login'; include 'includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card panel p-4 p-md-5"><h1 class="h3 fw-bold">Welcome back</h1><p class="text-muted">Log in to continue to FixServices.</p>
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
<button class="btn btn-primary btn-lg w-100">Login</button></form>
<p class="mt-3 mb-0">New to FixServices? <a href="register.php">Create an account</a></p></div></div></div>
<?php include 'includes/footer.php'; ?>
