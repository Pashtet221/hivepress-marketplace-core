<?php if(!defined('ABSPATH'))exit;?>

<!-- Header Navbar -->
<div class="container-fluid px-lg-6">
<div class="row">
<div class="col-12" col-lg-8="">
<!-- Checkout Bar -->
<div class="container-fluid px-lg-6 mt-4 mb-5">
<div class="cart-header-bar bg-white rounded-4 shadow-sm d-flex justify-content-between align-items-center px-4 py-3">
<h5 class="fw-bold mb-0" style="color: #2b3445;">Checkout page</h5>
<nav aria-label="breadcrumb">
<ol class="breadcrumb mb-0">
<li class="breadcrumb-item"><a class="text-decoration-none text-dark" href="home.html">Home</a></li>
<li aria-current="page" class="breadcrumb-item active">
<span class="mx-2 text-muted">»</span>
<span style="color: #7d879c;">Checkout Page</span>
</li>
</ol>
</nav>
</div>
</div>
<div class="container my-5">
<div class="row g-4 align-items-start">
<!-- New Customer -->
<div class="col-12 col-lg-7">
<div class="card border-0 shadow-sm p-4 mb-4">
<h4 class="fw-bold mb-4">New Customer</h4>
<p class="text-muted fw-bold small">Checkout Options</p>
<div class="form-check form-check-inline mb-3">
<input checked="" class="form-check-input" id="register" name="customerType" type="radio"/>
<label class="form-check-label" for="register">Register Account</label>
</div>
<div class="form-check form-check-inline mb-3">
<input class="form-check-input" id="guest" name="customerType" type="radio"/>
<label class="form-check-label" for="guest">Guest Account</label>
</div>
<p class="text-secondary medium">By creating an account you will be able to shop faster,
                                    be up to date on an order's status, and keep track of the orders you have previously
                                    made.</p>
<div class="d-flex justify-content-start">
<button class="btn custom-continue-btn mt-2 mb-5" type="button">Continue</button>
</div>
<!-- Returning customer -->
<h4 class="fw-bold mb-4">Returning Customer</h4>
<div class="mb-3">
<label class="form-label small fw-bold">Email Address</label>
<input class="form-control form-control-lg fs-6" placeholder="Enter your email address" type="email"/>
</div>
<div class="mb-3">
<label class="form-label small fw-bold">Password</label>
<input class="form-control form-control-lg fs-6" placeholder="Enter your password" type="password"/>
</div>
<div class="d-flex align-items-center gap-3 mt-4">
<div class="d-flex justify-content-start">
<a class="btn btn-checkout rounded-3 px-4 py-2 text-white background-color: #435ebe text-decoration-none" href="login.html" style="border-radius: 8px; background-color: #435ebe; 
                                    border:none;">
                                            Login
                                        </a>
</div>
</div>
</div>
<!-- Billing Details -->
<div class="card border-0 shadow-sm p-4">
<h4 class="fw-bold mb-4">Billing Details</h4>
<div class="mb-4">
<p class="small fw-bold text-muted mb-2">Checkout Options</p>
<div class="d-flex gap-3">
<div class="form-check">
<input checked="" class="form-check-input" id="existing" name="billingAddr" type="radio"/>
<label class="form-check-label small" for="existing">I want to use an
                                                existing address</label>
</div>
<div class="form-check">
<input class="form-check-input" id="new" name="billingAddr" type="radio"/>
<label class="form-check-label small" for="new">I want to use a new
                                                address</label>
</div>
</div>
</div>
<div class="row g-3">
<div class="col-md-6">
<label class="form-label small fw-bold">First Name*</label>
<input class="form-control form-control-sm" placeholder="Enter your first name" type="text"/>
</div>
<div class="col-md-6">
<label class="form-label small fw-bold">Last Name*</label>
<input class="form-control form-control-sm" placeholder="Enter your last name" type="text"/>
</div>
<div class="col-12">
<label class="form-label small fw-bold">Address</label>
<input class="form-control form-control-sm" placeholder="Address Line 1" type="text"/>
</div>
<div class="col-md-6">
<label class="form-label small fw-bold">City*</label>
<select class="form-select form-select-sm">
<option>City</option>
</select>
</div>
<div class="col-md-6">
<label class="form-label small fw-bold">Post Code</label>
<input class="form-control form-control-sm" placeholder="Post Code" type="text"/>
</div>
<div class="col-md-6">
<label class="form-label small fw-bold">Country*</label>
<select class="form-select form-select-sm">
<option>Country</option>
</select>
</div>
<div class="col-md-6">
<label class="form-label small fw-bold">Region State</label>
<select class="form-select form-select-sm">
<option>Region/State</option>
</select>
</div>
</div>
</div>
<div class="d-flex justify-content-end mt-2">
<a class="btn btn-checkout rounded-3 px-4 py-2 text-white background-color: #435ebe text-decoration-none" href="thankyou.html" style="border-radius: 8px; background-color: #435ebe; border:none;">
                                    place Order
                                </a>
</div>
</div>
<!-- Summary -->
<div class="col-lg-5">
<div class="card border-0 shadow-sm p-3 summary-card mb-4">
<h3 class="fw-bold mb-4" style="color: #2b3e51;">Summary</h3>
<div class="d-flex justify-content-between mb-2">
<span class="text-muted">Sub-Total</span>
<span class="fw-bold text-dark">$295.00</span>
</div>
<div class="d-flex justify-content-between mb-2">
<span class="text-muted">Delivery Charges</span>
<span class="fw-bold text-dark">$80.00</span>
</div>
<div class="d-flex justify-content-between mb-3">
<span class="text-muted">Coupan Discount</span>
<a class="text-decoration-none text-dark fw-bold" href="#">Apply Coupan</a>
</div>
<hr class="my-4" style="opacity: 0.1;"/>
<div class="d-flex justify-content-between mb-5">
<h5 class="fw-bold">Total Amount</h5>
<h5 class="fw-bold">$375.00</h5>
</div>
<div class="d-flex align-items-start mb-4">
<div class="product-img-container me-3">
<img alt="t-shirt" class="img-fluid" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shirt.jpg"/>
</div>
<div>
<h6 class="mb-1 fw-bold" style="color: #333;">Round neck cotton t-shirt</h6>
<div class="rating-stars mb-1">
<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star text-muted"></i>
</div>
<div><span class="price-original small">$58.00</span> <span class="price-current">$45.00</span></div>
</div>
</div>
<div class="d-flex align-items-start mb-2">
<div class="product-img-container me-3">
<img alt="watch" class="img-fluid" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/watch.jpg"/>
</div>
<div>
<h6 class="mb-1 fw-bold" style="color: #333;">Digital smart watch</h6>
<div class="rating-stars mb-1">
<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
</div>
<div><span class="price-original small">$265.00</span> <span class="price-current">$250.00</span></div>
</div>
</div>
</div>
<!-- Delievery Method -->
<div class="card border-0 shadow-sm p-4 mb-4">
<h5 class="fw-bold mb-3">Delivery Method</h5>
<p class="text-muted small">Please select the preferred shipping method to use on this
                                    order.</p>
<div class="d-flex justify-content-between border rounded p-2">
<div class="form-check">
<input checked="" class="form-check-input" id="free" name="delivery" type="radio"/>
<label class="form-check-label small fw-bold" for="free">Free Shipping</label>
</div>
<span class="small text-muted">Rate $0.00</span>
</div>
</div>
<!-- Payment Method -->
<div class="card border-0 shadow-sm p-4 mb-4">
<h5 class="fw-bold mb-3">Payment Method</h5>
<div class="form-check mb-3">
<input checked="" class="form-check-input" id="cod" name="payment" type="radio"/>
<label class="form-check-label small fw-bold" for="cod">Cash On Delivery</label>
</div>
<label class="form-label small fw-bold">Add extra note</label>
<textarea class="form-control mb-3" placeholder="Comments" rows="3"></textarea>
<div class="form-check mb-3">
<input class="form-check-input" id="terms" type="checkbox"/>
<label class="form-check-label small" for="terms">I have agree with <a href="#">Terms &amp; Conditions</a>.</label>
</div>
</div>
<!-- Payment images -->
<div class="row-lg-4">
<div class="card border-0 shadow-sm p-4 mt-4">
<h5 class="fw-bold mb-3">Payment Method</h5>
<div class="d-flex justify-content-center">
<img alt="Payment Methods" class="img-fluid" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/payment.png" style="max-width: 100%; height: auto;"/>
</div>
</div>
</div>
</div>
</div>
</div></div></div></div>