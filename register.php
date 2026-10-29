
<?php
require_once 'config.php';

if (current_user()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name     = trim($_POST['name'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $state    = trim($_POST['state'] ?? 'Maharashtra');
    $district = trim($_POST['district'] ?? '');
    $taluka   = trim($_POST['taluka'] ?? '');
    $town     = trim($_POST['town'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'customer';

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        $errors[] = 'Enter a name, valid email, and password of at least 8 characters.';
    }

    if (!in_array($role, ['customer', 'provider'], true)) {
        $errors[] = 'Invalid account type.';
    }

    if (
        $state === '' ||
        $district === '' ||
        $taluka === '' ||
        $town === ''
    ) {
        $errors[] = 'Please select or enter your state, district, taluka, and town.';
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $errors[] = 'This email is already registered.';
    }

    if (!$errors) {
        $providerStatus = $role === 'provider' ? 'pending' : 'approved';

        $stmt = $pdo->prepare(
            'INSERT INTO users
            (name, email, phone, address, state, district, taluka, town,
             password, role, provider_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $name,
            $email,
            $phone,
            $address,
            $state,
            $district,
            $taluka,
            $town,
            password_hash($password, PASSWORD_DEFAULT),
            $role,
            $providerStatus
        ]);

        flash(
            $role === 'provider'
                ? 'Account created. Your provider profile needs admin approval before services appear.'
                : 'Account created. You can now log in.'
        );

        redirect('login.php');
    }
}

$pageTitle = 'Register';
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card panel p-4 p-md-5">

            <h1 class="h3 fw-bold">Create your account</h1>
            <p class="text-muted">
                Join FixServices as a customer or local service provider.
            </p>

            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger">
                    <?= e($err) ?>
                </div>
            <?php endforeach; ?>

            <form method="post">
                <input type="hidden" name="csrf_token"
                       value="<?= e(csrf_token()) ?>">

                <div class="mb-3">
                    <label class="form-label">Full name</label>
                    <input class="form-control" name="name"
                           required maxlength="100"
                           value="<?= e($_POST['name'] ?? '') ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email"
                               name="email" required
                               value="<?= e($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input class="form-control" name="phone"
                               maxlength="20"
                               value="<?= e($_POST['phone'] ?? '') ?>">
                    </div>
                </div>

                <h2 class="h5 mt-3">Location details</h2>

                <div class="mb-3">
                    <label class="form-label">State</label>
                    <select class="form-select" name="state" required>
                        <option value="Maharashtra">Maharashtra</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">District</label>
                    <select class="form-select" name="district" required>
                        <option value="">Select district</option>
                        <?php
                        $districts = [
                            'Ahilyanagar', 'Akola', 'Amravati',
                            'Beed', 'Bhandara', 'Buldhana',
                            'Chandrapur', 'Chhatrapati Sambhajinagar',
                            'Dharashiv', 'Dhule', 'Gadchiroli',
                            'Gondia', 'Hingoli', 'Jalgaon',
                            'Jalna', 'Kolhapur', 'Latur',
                            'Mumbai City', 'Mumbai Suburban',
                            'Nagpur', 'Nanded', 'Nandurbar',
                            'Nashik', 'Palghar', 'Parbhani',
                            'Pune', 'Raigad', 'Ratnagiri',
                            'Sangli', 'Satara', 'Sindhudurg',
                            'Solapur', 'Thane', 'Wardha',
                            'Washim', 'Yavatmal'
                        ];

                        foreach ($districts as $d):
                        ?>
                            <option value="<?= e($d) ?>"
                                <?= (($_POST['district'] ?? '') === $d)
                                    ? 'selected' : '' ?>>
                                <?= e($d) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Taluka</label>
                    <input class="form-control" name="taluka"
                           required maxlength="100"
                           placeholder="Enter your taluka"
                           value="<?= e($_POST['taluka'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Town / Village</label>
                    <input class="form-control" name="town"
                           required maxlength="100"
                           placeholder="Enter your town or village"
                           value="<?= e($_POST['town'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Full address / locality</label>
                    <textarea class="form-control" name="address"
                              rows="2"><?= e($_POST['address'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Account type</label>
                    <select class="form-select" name="role">
                        <option value="customer"
                            <?= (($_POST['role'] ?? 'customer') === 'customer')
                                ? 'selected' : '' ?>>
                            Customer
                        </option>
                        <option value="provider"
                            <?= (($_POST['role'] ?? '') === 'provider')
                                ? 'selected' : '' ?>>
                            Service provider
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Password (minimum 8 characters)
                    </label>
                    <input class="form-control" type="password"
                           name="password" minlength="8" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    Create account
                </button>

                <a class="btn btn-link" href="login.php">
                    Already registered?
                </a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
