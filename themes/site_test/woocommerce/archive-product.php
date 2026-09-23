<?php if ( ! defined( 'ABSPATH' ) ) exit; get_header('shop'); ?>

<!-- Header Navbar -->
<!-- Shop Bar -->
<div class="container-fluid px-lg-6 mt-4 mb-5">
<div class="cart-header-bar bg-white rounded-4 shadow-sm d-flex justify-content-between align-items-center px-4 py-3">
<h5 class="fw-bold mb-0" style="color: #2b3445;">Shop Page</h5>
<nav aria-label="breadcrumb">
<ol class="breadcrumb mb-0">
<li class="breadcrumb-item"><a class="text-decoration-none text-dark" href="index.html">Home</a></li>
<li aria-current="page" class="breadcrumb-item active">
<span class="mx-2 text-muted">»</span>
<span style="color: #7d879c;">Shop Page</span>
</li>
</ol>
</nav>
</div>
</div>
<!-- Main Shop Section -->
<div class="container-fluid px-3 px-lg-5 mb-5">
<div class="row align-items-start">
<!-- Filter Stickybar -->
<aside class="col-lg-3 order-lg-1 order-lg-2 align-items-end">
<div class="filters-sidebar sticky-sidebar shadow-sm bg-white p-4 rounded-3 sticky-top" style="top: 100px;">
<div class="mn-sb-title mb-3 pb-2 border-bottom d-flex align-items-center">
<h5 class="fw-bold m-0" style="font-size: 16px; color: rgb(33, 37, 41);">Filters
                        </h5>
</div>
<div class="mn-sidebar-block-drop mb-4">
<ul class="list-unstyled">
<li class="mb-3">
<div class="d-flex justify-content-between align-items-center fw-medium" style="font-size: 15px; color: rgb(49, 59, 80);"><b> Clothes</b>
<span class="text-muted">+</span>
</div>
<ul class="list-unstyled ps-3 mt-2 text-muted small">
<li class="d-flex justify-content-between py-1">Men <span>-25</span>
</li>
<li class="d-flex justify-content-between py-1">Women
                                        <span>-52</span>
</li>
<li class="d-flex justify-content-between py-1">Boy <span>-40</span>
</li>
</ul>
</li>
<li class="d-flex justify-content-between py-2 border-top">
<b>Cosmetics</b><span>+</span>
</li>
<li class="d-flex justify-content-between py-2 border-top"><b>Shoes</b>
<span>-15</span>
</li>
<li class="d-flex justify-content-between py-2 border-top"><b>Bag</b>
<span>-27</span>
</li>
<li class="d-flex justify-content-between py-2 border-top">
<b>Electronics</b> <span>+</span>
</li>
</ul>
</div>
<div class="mn-sidebar-block mb-4 pt-3 border-top">
<h6 class="fw-bold mb-3" style="font-size: 15px;">Brand</h6>
<div class="form-check mb-2">
<input checked="" class="form-check-input" id="b1" name="brand" type="radio"/>
<label class="form-check-label small" for="b1">Zencart Mart</label>
</div>
<div class="form-check mb-2">
<input class="form-check-input" id="b2" name="brand" type="radio"/>
<label class="form-check-label small" for="b2">Xeta Store</label>
</div>
<div class="form-check mb-2">
<input class="form-check-input" id="b2" name="brand" type="radio"/>
<label class="form-check-label small" for="b2">Pili Market</label>
</div>
<div class="form-check mb-2">
<input class="form-check-input" id="b2" name="brand" type="radio"/>
<label class="form-check-label small" for="b2">Indiana Store</label>
</div>
</div>
<div class="mn-sidebar-block mb-4 pt-3 border-top">
<h6 class="fw-bold mb-3" style="font-size: 15px;">Size</h6>
<div class="form-check mb-2">
<input checked="" class="form-check-input" id="s1" name="size" type="radio"/>
<label class="form-check-label small" for="s1">S - Size</label>
</div>
<div class="form-check mb-2">
<input class="form-check-input" id="s2" name="size" type="radio"/>
<label class="form-check-label small" for="s2">M - Size</label>
</div>
<div class="form-check mb-2">
<input class="form-check-input" id="s3" name="size" type="radio"/>
<label class="form-check-label small" for="s3">L - Size</label>
</div>
<div class="form-check mb-2">
<input class="form-check-input" id="s4" name="size" type="radio"/>
<label class="form-check-label small" for="s4">Xl - Size</label>
</div>
</div>
<div class="mn-sidebar-block mb-4 pt-3 border-top">
<h6 class="fw-bold mb-3" style="font-size: 15px;">Color</h6>
<div class="d-flex gap-2 flex-wrap">
<div class="color-circle" style="background: rgb(70, 65, 65);" title="Black"></div>
<div class="color-circle" style="background: rgb(184, 143, 143);" title="Red"></div>
<div class="color-circle" style="background: rgb(151, 151, 167);" title="Blue"></div>
<div class="color-circle" style="background: rgb(167, 233, 167);" title="Green"></div>
<div class="color-circle" style="background: pink;" title="Pink"></div>
<div class="color-circle" style="background: #ea7bea;" title="Purple"></div>
<div class="color-circle" style="background: #ffa500;" title="Orange"></div>
<div class="color-circle" style="background: #808080;" title="Grey"></div>
<div class="color-circle" style="background: rgb(219, 108, 108);" title="Red"></div>
<div class="color-circle" style="background: rgb(123, 123, 178);" title="Blue"></div>
<div class="color-circle" style="background: rgb(105, 123, 105);" title="Green"></div>
</div>
</div>
<div class="mn-sidebar-block mb-4 pt-3 border-top">
<h6 class="fw-bold mb-3" style="font-size: 15px;">Price</h6>
<div class="d-flex justify-content-between small text-muted mb-2">
<span>From <br/> 0</span>
<span class="align-self-center">—</span>
<span>To <br/> 250</span>
</div>
<input class="form-range custom-range" max="250" min="0" type="range" value="250"/>
</div>
<div class="mn-sidebar-block mb-4 pt-3 border-top">
<h6 class="fw-bold mb-3" style="font-size: 15px;">Tags</h6>
<div class="d-flex flex-wrap gap-2">
<a class="tag-pill" href="#">Clothes</a>
<a class="tag-pill" href="#">Fruits</a>
<a class="tag-pill" href="#">Snacks</a>
<a class="tag-pill" href="#">Dairy</a>
<a class="tag-pill" href="#">Seafood</a>
<a class="tag-pill" href="#">Fastfood</a>
<a class="tag-pill" href="#">Toys</a>
</div>
</div>
</div>
</aside>
<!-- Products Section (LEFT) -->
<div class="col-lg-9 order-2 order-lg-1">
<!-- Toolbar -->
<div class="toolbar d-flex justify-content-between align-items-center flex-wrap gap-3">
<div class="view-toggle">
<div class="view-btn active">
<i class="bi bi-grid-3x3-gap-fill"></i>
</div>
<div class="view-btn">
<i class="bi bi-list-ul"></i>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="text-muted small">Sort by</span>
<select class="form-select form-select-sm" style="width: auto; border-color: #e3e9ef;">
<option>Relevance</option>
<option>Price: Low to High</option>
<option>Price: High to Low</option>
<option>Newest</option>
</select>
</div>
</div>
<!-- Filter Pills -->
<div class="filter-pills">
<span class="filter-pill">Clothes <span class="remove-icon">×</span></span>
<span class="filter-pill">Fruits <span class="remove-icon">×</span></span>
<span class="filter-pill">Snacks <span class="remove-icon">×</span></span>
<span class="filter-pill">Dairy <span class="remove-icon">×</span></span>
<span class="filter-pill">Perfume <span class="remove-icon">×</span></span>
<span class="filter-pill">Jewelry <span class="remove-icon">×</span></span>
<span class="filter-pill clear-all">Clear All</span>
</div>
<!-- Products Section -->
<!-- Products Grid Section -->
<div class="row na-grid g-4 mb-4">
<!-- Product 1 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<span class="na-badge na-badge--trending">NEW</span>
<a href="Product1.html">
<img alt="Cotton fabric T-shirt" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shirt.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">T-SHIRT</span>
<span class="na-sizes">S M XL</span>
</div>
<h4 class="na-title"><a href="Product1.html">Cotton fabric T-shirt</a></h4>
<div class="na-prices">
<span class="na-price-now">$120</span>
<span class="na-price-old">$130</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#c9a0dc;"></span>
<span class="na-swatch" style="background:#5C6BC0;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 2 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<span class="na-badge na-badge--sale">SALE</span>
<a href="Product1.html">
<img alt="Leather purse" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/purse.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">T-SHIRT</span>
<span class="na-sizes">S L</span>
</div>
<h4 class="na-title"><a href="Product1.html">Leather purse</a></h4>
<div class="na-prices">
<span class="na-price-now">$90</span>
<span class="na-price-old">$95</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#D4A373;"></span>
<span class="na-swatch" style="background:#5C6BC0;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 3 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<span class="na-badge na-badge--trending">TRENDING</span>
<a href="Product1.html">
<img alt="Cotton fabric T-shirt" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/product-main.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">T-SHIRT</span>
<span class="na-sizes">S M XL</span>
</div>
<h4 class="na-title"><a href="Product1.html">Cotton fabric T-shirt</a></h4>
<div class="na-prices">
<span class="na-price-now">$120</span>
<span class="na-price-old">$130</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#90A4AE;"></span>
<span class="na-swatch" style="background:#5C6BC0;"></span>
<span class="na-swatch" style="background:#E91E63;"></span>
<span class="na-swatch" style="background:#66BB6A;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 4 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<a href="Product1.html">
<img alt="Special sport shoes" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shoes2.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">SHOES</span>
<span class="na-sizes">7 8 10</span>
</div>
<h4 class="na-title"><a href="Product1.html">Special sport shoes</a></h4>
<div class="na-prices">
<span class="na-price-now">$55</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#000;"></span>
<span class="na-swatch" style="background:#E91E63;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 5 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<span class="na-badge na-badge--new">NEW</span>
<a href="Product1.html">
<img alt="Cotton fabric Top" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shirt.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">TOP</span>
<span class="na-sizes">S M</span>
</div>
<h4 class="na-title"><a href="product-detail.html">Cotton fabric Top</a></h4>
<div class="na-prices">
<span class="na-price-now">$120</span>
<span class="na-price-old">$130</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#FFF;border:1px solid #ddd;"></span>
<span class="na-swatch" style="background:#E91E63;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 6 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<span class="na-badge na-badge--sale">SALE</span>
<a href="Product1.html">
<img alt="Mantu smart watch" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/watch.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">WATCHES</span>
</div>
<h4 class="na-title"><a href="product-detail.html">Mantu smart watch</a></h4>
<div class="na-prices">
<span class="na-price-now">$955</span>
<span class="na-price-old">$999</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#000;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 7 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<span class="na-badge na-badge--off">20% OFF</span>
<a href="Product1.html">
<img alt="Mantu leather belt" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/leatherbelt.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">BELT</span>
</div>
<h4 class="na-title"><a href="product-detail.html">Mantu leather belt</a></h4>
<div class="na-prices">
<span class="na-price-now">$10</span>
<span class="na-price-old">$12</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#D4A373;"></span>
<span class="na-swatch" style="background:#000;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 8 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<a href="Product1.html">
<img alt="Leather bag" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/leatherbag.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">BAG</span>
<span class="na-sizes">M L</span>
</div>
<h4 class="na-title"><a href="product1.html">Leather bag</a></h4>
<div class="na-prices">
<span class="na-price-now">$86</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#8B4513;"></span>
<span class="na-swatch" style="background:#000;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 9 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<a href="Product1.html">
<img alt="Cotton coat for women" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/longcoat.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">COATS</span>
<span class="na-sizes">S M</span>
</div>
<h4 class="na-title"><a href="product1.html">Cotton coat for women</a></h4>
<div class="na-prices">
<span class="na-price-now">$220</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#D4A373;"></span>
<span class="na-swatch" style="background:#8B4513;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 10 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<a href="Product1.html">
<img alt="T-shirt for womens" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/fshirt.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">T-SHIRT</span>
<span class="na-sizes">XL XXL</span>
</div>
<h4 class="na-title"><a href="product-detail.html">T-shirt for womens</a></h4>
<div class="na-prices">
<span class="na-price-now">$66</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#1A237E;"></span>
<span class="na-swatch" style="background:#000;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 11 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<a href="Product1.html">
<img alt="Official Men's Suits" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/official.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">SUIT</span>
<span class="na-sizes">S M</span>
</div>
<h4 class="na-title"><a href="product-detail.html">Official Men's Suits</a></h4>
<div class="na-prices">
<span class="na-price-now">$524</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#4CAF50;"></span>
<span class="na-swatch" style="background:#66BB6A;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
<!-- Product 12 -->
<div class="na-col">
<div class="na-card">
<div class="na-imgbox">
<a href="Product1.html">
<img alt="T-shirt with jacket for boy" class="na-img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/jacket.jpg"/>
</a>
<div class="na-actions">
<button class="na-act-btn na-act-btn--qv" title="Quick View">
<i class="bi bi-eye"></i>
</button>
<button class="na-act-btn na-act-btn--cmp" title="Compare">
<i class="bi bi-shuffle"></i>
</button>
<button class="na-act-btn na-act-btn--cart" title="Add to Cart">
<i class="bi bi-bag"></i>
</button>
</div>
</div>
<div class="na-info">
<div class="na-meta">
<span class="na-type">JACKET</span>
<span class="na-sizes">M L</span>
</div>
<h4 class="na-title"><a href="product1.html">T-shirt with jacket for boy</a></h4>
<div class="na-prices">
<span class="na-price-now">$866</span>
</div>
<div class="na-swatches">
<span class="na-swatch" style="background:#546E7A;"></span>
<span class="na-swatch" style="background:#00695C;"></span>
<button class="na-heart"><i class="bi bi-heart"></i></button>
</div>
</div>
</div>
</div>
</div>
<hr class="mn-divider"/>
<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-4 mb-5">
<div class="text-muted small">
                        Showing 1-12 of 21 item(s)
                    </div>
<nav aria-label="Product pagination">
<ul class="pagination mb-0">
<li class="page-item active">
<a class="page-link" href="#" style="background: #ffd333; border-color: #ffd333; color: #2b3445;">1</a>
</li>
<li class="page-item">
<a class="page-link" href="#" style="color: #2b3445; border-color: #e3e9ef;">2</a>
</li>
<li class="page-item">
<a class="page-link" href="#" style="color: #2b3445; border-color: #e3e9ef;">3</a>
</li>
<li class="page-item">
<a class="page-link" href="#" style="color: #7d879c; border-color: #e3e9ef;">...</a>
</li>
<li class="page-item">
<a class="page-link" href="#" style="color: #2b3445; border-color: #e3e9ef;">8</a>
</li>
<li class="page-item">
<a class="page-link" href="#" style="color: #2b3445; border-color: #e3e9ef;">
                                    Next <i class="bi bi-chevron-right"></i>
</a>
</li>
</ul>
</nav>
</div>
</div></div></div>
<?php get_footer('shop'); ?>