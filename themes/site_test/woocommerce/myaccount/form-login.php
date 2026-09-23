<?php if(!defined('ABSPATH'))exit;?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Mantu WooCommerce Template</title>
</head>
<body>
<!-- Header Navbar -->
<div class="container-fluid px-lg-6">
<div class="row">
<div class="col-12">
<!-- Login Bar -->
<div class="container-fluid px-lg-6 mt-4 mb-2">
<div class="cart-header-bar bg-white rounded-4 shadow-sm d-flex justify-content-between align-items-center px-4 py-3">
<h5 class="fw-bold mb-0" style="color: #2b3445;">Login page</h5>
<nav aria-label="breadcrumb">
<ol class="breadcrumb mb-0">
<li class="breadcrumb-item"><a class="text-decoration-none text-dark" href="index.html">Home</a></li>
<li aria-current="page" class="breadcrumb-item active">
<span class="mx-2 text-muted">»</span>
<span style="color: #7d879c;">Login Page</span>
</li>
</ol>
</nav>
</div>
</div>
<!-- Login FOrm -->
<section class="login-section py-5 bg-light mb-0">
<div class="container">
<div class="login-card bg-white rounded-5 border shadow-sm mx-auto overflow-hidden" style="max-width: 1400px;">
<div class="d-flex align-items-center p-3 p-md-4">
<div class="form-container flex-grow-1 px-4 px-md-5">
<form>
<div class="mb-4">
<label class="form-label fw-bold">Email Address*</label>
<input class="form-control border-light-subtle bg-body-tertiary py-3 rounded-3" placeholder="Enter your email address" required="" type="email"/>
</div>
<div class="mb-4">
<label class="form-label fw-bold">Password*</label>
<input class="form-control border-light-subtle bg-body-tertiary py-3 rounded-3" placeholder="Enter your password" required="" type="password"/>
</div>
<div class="d-flex justify-content-between align-items-center mb-5">
<div class="form-check">
<input class="form-check-input" id="remember" type="checkbox"/>
<label class="form-check-label text-muted small" for="remember">Remember</label>
</div>
<a class="text-muted text-decoration-none small" href="#">Forgot
                                                    Password?</a>
</div>
<div class="d-flex justify-content-between align-items-center">
<h6 class="mb-0 fw-bold">Create Account?</h6>
<button class="btn btn-login fw-bold px-4 py-2 rounded-3" type="submit">Login</button>
</div>
</form>
</div>
<div class="d-none d-lg-block ps-2" style="width: 550px;">
<img alt="Interior" class="img-fluid rounded-4" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/about-3.png" style="height: 400px; object-fit: cover;"/>
</div>
</div>
</div>
</div>
</section>
</div></div></div></body>
</html>
