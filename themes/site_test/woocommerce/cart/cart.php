<?php if(!defined('ABSPATH'))exit;?>

<!-- Header Navbar -->
<div class="container-fluid px-lg-6">
<div class="row">
<div class="col-12">
<!-- Header Closed -->
<!-- Cart Search bar -->
<div class="container-fluid px-lg-6 mt-4">
<div class="cart-header-bar bg-white rounded-4 shadow-sm d-flex justify-content-between align-items-center px-4 py-3">
<h5 class="fw-bold mb-0" style="color: #2b3445;">Cart Page</h5>
<nav aria-label="breadcrumb">
<ol class="breadcrumb mb-0">
<li class="breadcrumb-item"><a class="text-decoration-none text-dark" href="home.html">Home</a></li>
<li aria-current="page" class="breadcrumb-item active">
<span class="mx-2 text-muted">»</span>
<span style="color: #7d879c;">Cart Page</span>
</li>
</ol>
</nav>
</div>
</div>
<!-- Datatable  -->
<div class="container-fluid px-lg-6 mt-4">
<div class="row g-4">
<div class="col-lg-8">
<div class="bg-white rounded-4 shadow-sm p-4 h-100">
<div class="table-responsive">
<table class="table align-middle custom-cart-table">
<thead class="table-light">
<tr class="text-muted small">
<th class="ps-3 border-0">Product</th>
<th class="border-0">Price</th>
<th class="border-0">Quantity</th>
<th class="border-0">Total</th>
<th class="border-0"></th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center">
<div class="product-img-box me-3">
<img alt="Mantu smart watch" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/watch.jpg"/>
</div>
<span class="fw-medium">Mantu smart watch</span>
</div>
</td>
<td class="fw-bold" data-label="price">$516.00</td>
<td data-label="Quantity"><input class="form-control qty-input" type="number" value="1"/></td>
<td class="fw-bold" data-label="Total">$516.00</td>
<td class="text-end pe-3"><i class="bi bi-trash text-primary remove-icon"></i></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center">
<div class="product-img-box me-3">
<img alt="Leather bag" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/15.jpg"/>
</div>
<span class="fw-medium">Leather bag</span>
</div>
</td>
<td class="fw-bold" data-label="Price">$75.00</td>
<td data-label="Quantity"><input class="form-control qty-input" type="number" value="1"/></td>
<td class="fw-bold" data-label="Total">$75.00</td>
<td class="text-end pe-3"><i class="bi bi-trash text-primary remove-icon"></i></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center">
<div class="product-img-box me-3">
<img alt="Cotton fabric T-shirt" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/fshirt.jpg"/>
</div>
<span class="fw-medium">Cotton fabric T-shirt</span>
</div>
</td>
<td class="fw-bold" data-label="Price">$48.00</td>
<td data-label="Quantity"><input class="form-control qty-input" type="number" value="1"/></td>
<td class="fw-bold" data-label="Total">$48.00</td>
<td class="text-end pe-3"><i class="bi bi-trash text-primary remove-icon"></i></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center">
<div class="product-img-box me-3">
<img alt="Special sport shoes" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shoes2.jpg"/>
</div>
<span class="fw-medium">Special sport shoes</span>
</div>
</td>
<td class="fw-bold" data-label="Price">$95.00</td>
<td data-label="Quantity"><input class="form-control qty-input" type="number" value="1"/></td>
<td class="fw-bold" data-label="Total">$95.00</td>
<td class="text-end pe-3"><i class="bi bi-trash text-primary remove-icon"></i></td>
</tr>
</tbody>
</table>
</div>
<div class="d-flex justify-content-between align-items-center mt-4">
<a class="continue-link text-dark fw-bold text-decoration-none border-bottom border-2 border-dark" href="shop.html">Continue
                                        Shopping</a>
<a class="btn btn-checkout rounded-3 px-4 py-2 text-white text-decoration-none" href="checkout.html">
                                        Check Out
                                    </a>
</div>
</div>
</div>
<!-- Summaryy -->
<div class="col-lg-4">
<div class="summary-card bg-white rounded-4 shadow-sm p-4">
<h5 class="summary-title mb-3">Summary</h5>
<p class="summary-subtitle mb-4">Enter your destination to get a shipping estimate</p>
<div class="mb-3">
<label class="form-label summary-label">Country *</label>
<select class="form-select summary-custom-input">
<option selected="">United States</option>
<option value="1">Country 1</option>
<option value="2">Country 2</option>
<option value="3">Country 3</option>
</select>
</div>
<div class="mb-3">
<label class="form-label summary-label">State/Province</label>
<select class="form-select summary-custom-input">
<option selected="">Please Select a region, state</option>
<option value="1">Region/State 1</option>
<option value="2">Region/State 2</option>
</select>
</div>
<div class="mb-4">
<label class="form-label summary-label">Zip/Postal Code</label>
<input class="form-control summary-custom-input" placeholder="Zip/Postal Code" type="text"/>
</div>
<div class="d-flex justify-content-between mb-2">
<span class="pricing-text">Sub-Total</span>
<span class="pricing-price">$734.00</span>
</div>
<div class="d-flex justify-content-between mb-2">
<span class="pricing-text">Delivery Charges</span>
<span class="pricing-price">$80.00</span>
</div>
<div class="d-flex justify-content-between mb-4">
<span class="pricing-text">Coupon Discount</span>
<span class="apply-coupon">Apply Coupon</span>
</div>
<hr class="my-4" style="color: #f0f1f3; opacity: 1;"/>
<div class="d-flex justify-content-between align-items-center">
<h5 class="total-label mb-0">Total Amount</h5>
<h5 class="total-value mb-0">$814.00</h5>
</div>
</div>
</div>
</div></div></div></div></div>