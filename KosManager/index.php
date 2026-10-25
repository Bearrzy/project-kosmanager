<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KosManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card login-card">
                    <div class="row g-0">
                        <div class="col-md-5 login-left d-none d-md-flex">
                            <div>
                                <i class="bi bi-house-heart-fill fs-1 mb-3"></i>
                                <h2>KosManager</h2>
                                <p class="mb-4 opacity-75">Sistem Manajemen Kos - kamar, penghuni, dan pembayaran.</p>
                                <ul class="list-unstyled opacity-75">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2"></i> Manajemen Kamar</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill me-2"></i> Data Penghuni</li>
                                    <li><i class="bi bi-check-circle-fill me-2"></i> Pembayaran</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card-body p-5">
                                <h4 class="fw-bold mb-1">Selamat Datang</h4>
                                <p class="text-muted mb-4">Silakan login untuk melanjutkan</p>
                                <form action="dashboard.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Username</label>
                                        <input type="text" class="form-control" name="username" placeholder="admin" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Password</label>
                                        <input type="password" class="form-control" name="password" placeholder="******" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                    </button>
                                </form>
                                <div class="text-center mt-3">
                                    <small class="text-muted">Demo: isi username & password apa saja</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
