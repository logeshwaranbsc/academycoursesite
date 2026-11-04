<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Digital Teenz Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body {
            background-color: var(--color-cream);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .login-card {
            background: #fff;
            border: 1px solid var(--color-gray-200);
            border-radius: 8px;
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 3.5rem; height: 3.5rem; background-color: var(--color-navy); color: #fff; font-size: 1.75rem; font-weight: bold; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
                🎓
            </div>
            <h1 style="font-size: 1.5rem; color: var(--color-navy); margin-bottom: 0.35rem;">Admin Portal</h1>
            <p style="font-size: 0.875rem; color: #6B7280; margin: 0;">Sign in to manage courses, batches, and enquiries</p>
        </div>

        <?php if (has_flash('error')): $f = get_flash('error'); ?>
            <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.875rem; margin-bottom: 1.5rem;">
                <?= e($f['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (has_flash('success')): $f = get_flash('success'); ?>
            <div style="background-color: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.875rem; margin-bottom: 1.5rem;">
                <?= e($f['message']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="POST" novalidate>
            <?= csrf_field() ?>

            <div style="margin-bottom: 1.25rem;">
                <label for="login-email" class="form-label">Email Address</label>
                <input type="email" id="login-email" name="email" class="form-input" placeholder="admin@gmail.com" value="<?= e(old('email')) ?>" required autofocus>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="login-password" class="form-label">Password</label>
                <input type="password" id="login-password" name="password" class="form-input" placeholder="Enter your admin password" required>
            </div>

            <button type="submit" class="btn-primary w-full" style="width: 100%; display: flex; justify-content: center; font-size: 1rem; padding: 0.85rem;">
                Sign In to Dashboard &rarr;
            </button>
        </form>

        <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid var(--color-gray-200); text-align: center; font-size: 0.8125rem;">
            <a href="<?= base_url() ?>" style="color: var(--color-navy); font-weight: 600;">&larr; Back to Public Website</a>
        </div>
    </div>
</body>
</html>
