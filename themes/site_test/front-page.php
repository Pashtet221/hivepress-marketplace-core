<?php get_header(); ?>

<!-- Header Navbar -->
<div class="container-fluid px-lg-6">
<div class="row">
<div class="col-12">
<!-- Carousel Slider -->
<section class="hero-slider-container mt-4">
<div class="carousel slide" data-bs-interval="3000" data-bs-ride="carousel" id="heroCarousel">
<div class="carousel-indicators">
<button class="active" data-bs-slide-to="0" data-bs-target="#heroCarousel" type="button"></button>
<button data-bs-slide-to="1" data-bs-target="#heroCarousel" type="button"></button>
<button data-bs-slide-to="2" data-bs-target="#heroCarousel" type="button"></button>
<button data-bs-slide-to="3" data-bs-target="#heroCarousel" type="button"></button>
</div>
<div class="carousel-inner">
<div class="carousel-item active">
<div class="slide-background" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/slider3.jpg');">
</div>
<div class="row align-items-center">
<div class="col-lg-5 offset-lg-1">
<div class="slide-content">
<div class="discount-badge">22%<br/>Off</div>
<h1 class="slide-title">Fashion sale<br/>for Children's</h1>
<p class="slide-subtitle">Wear the change. Fashion that feels good.</p>
<a class="btn-shop-now" href="shop.html">Shop Now</a>
</div>
</div>
</div>
</div>
<div class="carousel-item">
<div class="slide-background" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/slider2.jpg');">
</div>
<div class="row align-items-center">
<div class="col-lg-5 offset-lg-1">
<div class="slide-content">
<div class="discount-badge">35%<br/>Off</div>
<h1 class="slide-title">Fashion sale<br/>for Men's</h1>
<p class="slide-subtitle">Wear the change. Fashion that feels good.</p>
<a class="btn-shop-now" href="shop.html">Shop Now</a>
</div>
</div>
</div>
</div>
<div class="carousel-item">
<div class="slide-background" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/slider1.jpg');">
</div>
<div class="row align-items-center">
<div class="col-lg-5 offset-lg-1">
<div class="slide-content">
<div class="discount-badge">50%<br/>Off</div>
<h1 class="slide-title">Fashion sale<br/>for women's</h1>
<p class="slide-subtitle">Elevate your every day. Style that speaks volumes.
                                            </p>
<a class="btn-shop-now" href="shop.html">Shop Now</a>
</div>
</div>
</div>
</div>
<div class="carousel-item">
<div class="slide-background" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/slider4.jpg');">
</div>
<div class="row align-items-center">
<div class="col-lg-5 offset-lg-1">
<div class="slide-content">
<div class="discount-badge">22%<br/>Off</div>
<h1 class="slide-title">Cosmetics sale<br/>for Women's</h1>
<p class="slide-subtitle">Wear the change. Fashion that feels good.</p>
<a class="btn-shop-now" href="shop.html">Shop Now</a>
</div>
</div>
</div>
</div>
</div>
<button class="carousel-control-prev" data-bs-slide="prev" data-bs-target="#heroCarousel" type="button">
<span class="carousel-control-prev-icon"></span>
</button>
<button class="carousel-control-next" data-bs-slide="next" data-bs-target="#heroCarousel" type="button">
<span class="carousel-control-next-icon"></span>
</button>
</div>
</section>
<!-- Category Card -->
<section class="mn-category-section py-5">
<div class="container">
<div class="row g-4">
<div class="col-lg-3 col-md-3 col-sm-6 col-6">
<div class="mn-cat-card">
<p class="lbl"><span>35%</span></p>
<span class="bg">35%</span>
<h4>Fashion</h4>
<h3>Clothes</h3>
<p class="items-count">Items (16)</p>
<div class="cat-gallery">
<img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/clothes1.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/clothes2.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/clothes3.jpg"/>
</div>
</div>
</div>
<div class="col-xl-3 col-lg-4 col-md-6 col-6 mn-cat-item">
<div class="mn-cat-card">
<p class="lbl"><span>22%</span></p>
<span class="bg">22%</span>
<h4>Generic</h4>
<h3>Cosmetics</h3>
<p class="items-count">Items (45)</p>
<div class="cat-gallery">
<img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/cosmetics1.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/cosmetics2.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/cosmetics3.jpg"/>
</div>
</div>
</div>
<div class="col-xl-3 col-lg-4 col-md-6 col-6 mn-cat-item">
<div class="mn-cat-card">
<p class="lbl"><span>65%</span></p>
<span class="bg">65%</span>
<h4>Stylish</h4>
<h3>Shoes</h3>
<p class="items-count">Items (58)</p>
<div class="cat-gallery">
<img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shoes1.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shoes2.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/shoes3.jpg"/>
</div>
</div>
</div>
<div class="col-xl-3 col-lg-4 col-md-6 col-6 mn-cat-item">
<div class="mn-cat-card">
<p class="lbl"><span>45%</span></p>
<span class="bg">45%</span>
<h4>Digital</h4>
<h3>Watches</h3>
<p class="items-count">Items (64)</p>
<div class="cat-gallery">
<img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/10.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/11.jpg"/><img alt="img" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/12.jpg"/>
</div>
</div>
</div>
</div>
</div>
<!-- New Arrivals -->
<div class="d-flex justify-content-between align-items-center mt-5 mb-4">
<h2 class="section-title">New <span>Arrivals</span></h2>
<div class="mn-slider-controls">
<div class="slider-toggle">
<span class="active-pill"></span>
<span class="dot"></span>
</div>
</div>
</div>
<hr class="mn-divider"/>
<!-- New Arrivals Product Grid -->
<?php
$new_products = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => WC()->query->get_meta_query(),
		'tax_query'      => WC()->query->get_tax_query(),
	)
);
?>
<div class="row na-grid mt-2 mb-5">
<?php if ( $new_products->have_posts() ) : ?>
	<?php while ( $new_products->have_posts() ) : $new_products->the_post(); ?>
		<?php wc_get_template_part( 'content', 'product' ); ?>
	<?php endwhile; ?>
<?php else : ?>
	<p class="woocommerce-info"><?php esc_html_e( 'No products were found.', 'mantu' ); ?></p>
<?php endif; ?>
<?php wp_reset_postdata(); ?>

</div><!-- end na-grid -->
<!-- Service Card Integration -->
<section class="info-service-section mt-1 mb-2">
<div class="container">
<div class="row g-4">
<div class="col-lg-3 col-md-6 col-6">
<div class="info-service-card">
<div class="info-icon">
<i class="bi bi-truck"></i>
</div>
<h5>Free Shipping</h5>
<p>Free shipping on all US order or order above $200</p>
</div>
</div>
<div class="col-lg-3 col-md-6 col-6">
<div class="info-service-card">
<div class="info-icon">
<i class="bi bi-headset"></i>
</div>
<h5>24X7 Support</h5>
<p>Contact us 24 hours live support, 7 days in a week</p>
</div>
</div>
<div class="col-lg-3 col-md-6 col-6">
<div class="info-service-card">
<div class="info-icon">
<i class="bi bi-arrow-repeat"></i>
</div>
<h5>30 Days Return</h5>
<p>Simply return it within 30 days for an exchange</p>
</div>
</div>
<div class="col-lg-3 col-md-6 col-6">
<div class="info-service-card">
<div class="info-icon">
<i class="bi bi-shield-check"></i>
</div>
<h5>Payment Secure</h5>
<p>Contact us 24 hours live support, 7 days in a week</p>
</div>
</div>
</div>
</div>
</section>
<!-- Day of Deals -->
<div class="d-flex justify-content-between align-items-center container-fluid mt-5 mb-4">
<div class="d-flex align-items-center">
<h2 class="section-title mb-0">Day of the <span>Deals</span></h2>
<div class="mtu-timer-pill d-flex align-items-center gap-2">
<span class="timer-value">241</span> <span class="timer-label">Days</span>
<span class="timer-value">18</span> <span class="timer-sep">:</span>
<span class="timer-value">58</span> <span class="timer-sep">:</span>
<span class="timer-value">12</span>
</div>
</div>
<div class="mn-slider-controls">
<div class="slider-toggle">
<span class="active-pill"></span>
<span class="dot"></span>
</div>
</div>
</div>
<hr class="mn-divider"/>
<!-- Day of Deals Product Grid -->
<?php
$sale_product_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
$deal_products    = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'post__in'       => $sale_product_ids ?: array( 0 ),
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => WC()->query->get_meta_query(),
		'tax_query'      => WC()->query->get_tax_query(),
	)
);
?>
<div class="row na-grid mt-2 mb-5">
<?php if ( $deal_products->have_posts() ) : ?>
	<?php while ( $deal_products->have_posts() ) : $deal_products->the_post(); ?>
		<?php wc_get_template_part( 'content', 'product' ); ?>
	<?php endwhile; ?>
<?php else : ?>
	<p class="woocommerce-info"><?php esc_html_e( 'There are no sale products right now.', 'mantu' ); ?></p>
<?php endif; ?>
<?php wp_reset_postdata(); ?>

</div><!-- end na-grid -->
<!-- Testimonail Section -->
<section class="mtu-testimonial-area py-4">
<div class="container mt-0">
<div class="carousel slide" data-bs-ride="carousel" id="mtuMantuSlider">
<div class="carousel-inner">
<div class="carousel-item active">
<div class="mtu-tst-card mx-auto">
<img alt="quote" class="mtu-quote t-quote" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/bottom-quotes.svg"/>
<div class="mtu-pfp-wrap">
<img alt="John Doe" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/testimonial3.jpg"/>
</div>
<p class="mtu-tst-text">Standard dummy text ever since the 1500s, when an unknown printer took a galley of type and this is the lorem and scrambled it to make a type specimen.</p>
<div class="mtu-tst-author">John Doe</div>
<div class="mtu-tst-role">(CFO)</div>
<img alt="quote" class="mtu-quote b-quote" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/bottom-quotes.svg"/>
</div>
</div>
<div class="carousel-item">
<div class="mtu-tst-card mx-auto">
<img alt="quote" class="mtu-quote t-quote" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/bottom-quotes.svg"/>
<div class="mtu-pfp-wrap">
<img alt="Mariya Klinton" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/testimonial2.jpg"/>
</div>
<p class="mtu-tst-text">Lorem Ipsum is simply dummy text of the printing and industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
<div class="mtu-tst-author">Mariya Klinton</div>
<div class="mtu-tst-role">(CEO)</div>
<img alt="quote" class="mtu-quote b-quote" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/bottom-quotes.svg"/>
</div>
</div>
<div class="carousel-item">
<div class="mtu-tst-card mx-auto">
<img alt="quote" class="mtu-quote t-quote" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/bottom-quotes.svg"/>
<div class="mtu-pfp-wrap">
<img alt="Nency Lykra" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/testimonial1.jpg"/>
</div>
<p class="mtu-tst-text">When an unknown printer took a galley of type and scrambled it to make a type specimen Lorem Ipsum has been the industry's and ever since to the 1500s,</p>
<div class="mtu-tst-author">Nency Lykra</div>
<div class="mtu-tst-role">(MANAGER)</div>
<img alt="quote" class="mtu-quote b-quote" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/bottom-quotes.svg"/>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Our Blogs  -->
<section class="mtu-blog-section pb-5">
<div class="container">
<div class="d-flex justify-content-between align-items-center mb-4">
<h2 class="mtu-blog-title mb-0">Our <span>Blogs</span></h2>
<div class="mtu-slider-controls">
<div class="carousel-indicators mtu-inline-dots position-relative m-0">
<button class="active" data-bs-slide-to="0" data-bs-target="#mtuMantuSlider" type="button"></button>
<button data-bs-slide-to="1" data-bs-target="#mtuMantuSlider" type="button"></button>
<button data-bs-slide-to="2" data-bs-target="#mtuMantuSlider" type="button"></button>
</div>
</div>
</div>
<hr class="mtu-blog-divider mb-4"/>
<div class="row g-4">
<div class="col-lg-3 col-md-6">
<div class="mtu-blog-card">
<div class="mtu-blog-img">
<img alt="Marketing Guide" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/blogs1.jpg"/>
</div>
<div class="mtu-blog-info">
<span class="mtu-blog-date">June 30, 2025 -<span class="mtu-cat"> Fashion</span></span>
<h3 class="mtu-blog-name">Marketing Guide: 5 Steps to Success to way.</h3>
<a class="mtu-read-more" href="#">Read More »</a>
</div>
</div>
</div>
<div class="col-lg-3 col-md-6">
<div class="mtu-blog-card">
<div class="mtu-blog-img">
<img alt="Business Issue" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/blogs2.jpg"/>
</div>
<div class="mtu-blog-info">
<span class="mtu-blog-date">April 02, 2025 -<span class="mtu-cat"> Shoes</span></span>
<h3 class="mtu-blog-name">Best way to solve business issue in market.</h3>
<a class="mtu-read-more" href="#">Read More »</a>
</div>
</div>
</div>
<div class="col-lg-3 col-md-6">
<div class="mtu-blog-card">
<div class="mtu-blog-img">
<img alt="Grocery Stats" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/blogs3.jpg"/>
</div>
<div class="mtu-blog-info">
<span class="mtu-blog-date">Mar 09, 2025 - <span class="mtu-cat">Grocery</span></span>
<h3 class="mtu-blog-name">31 grocery customer service stats know in...</h3>
<a class="mtu-read-more" href="#">Read More »</a>
</div>
</div>
</div>
<div class="col-lg-3 col-md-6">
<div class="mtu-blog-card">
<div class="mtu-blog-img">
<img alt="Business Traffic" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/blogs4.jpg"/>
</div>
<div class="mtu-blog-info">
<span class="mtu-blog-date">January 25, 2025 -<span class="mtu-cat"> Bags</span></span>
<h3 class="mtu-blog-name">Business ideas to grow your business traffic.</h3>
<a class="mtu-read-more" href="#">Read More »</a>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Instagram feed Section -->
<section class="mtu-insta-section pb-5">
<div class="container-fluid px-0">
<div class="mtu-insta-wrapper d-flex flex-nowrap">
<div class="mtu-insta-item">
<div class="mtu-insta-inner">
<img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta2.jpg"/>
<div class="mtu-insta-overlay">
<i class="fab fa-instagram"></i>
</div>
</div>
</div>
<div class="mtu-insta-item">
<div class="mtu-insta-inner">
<img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta1.jpg"/>
<div class="mtu-insta-overlay">
<i class="fab fa-instagram"></i>
</div>
</div>
</div>
<div class="mtu-insta-item">
<div class="mtu-insta-inner">
<img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta3.jpg"/>
<div class="mtu-insta-overlay">
<i class="fab fa-instagram"></i>
</div>
</div>
</div>
<div class="mtu-insta-item">
<div class="mtu-insta-inner">
<img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta4.jpg"/>
<div class="mtu-insta-overlay">
<i class="fab fa-instagram"></i>
</div>
</div>
</div>
<div class="mtu-insta-item">
<div class="mtu-insta-inner">
<img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta5.jpg"/>
<div class="mtu-insta-overlay">
<i class="fab fa-instagram"></i>
</div>
</div>
</div>
<div class="mtu-insta-item"><div class="mtu-insta-inner"><img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta1.jpg"/><div class="mtu-insta-overlay"><i class="fab fa-instagram"></i></div></div></div>
<div class="mtu-insta-item"><div class="mtu-insta-inner"><img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta2.jpg"/><div class="mtu-insta-overlay"><i class="fab fa-instagram"></i></div></div></div>
<div class="mtu-insta-item"><div class="mtu-insta-inner"><img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta3.jpg"/><div class="mtu-insta-overlay"><i class="fab fa-instagram"></i></div></div></div>
<div class="mtu-insta-item"><div class="mtu-insta-inner"><img alt="Insta" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/insta4.jpg"/><div class="mtu-insta-overlay"><i class="fab fa-instagram"></i></div></div></div>
</div>
</div>
</section>
</section></div>
<!-- Modal Quick view -->
<div aria-hidden="true" class="modal fade" id="quickViewModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered qv-modal-custom">
<div class="modal-content border-0 position-relative">
<button aria-label="Close" class="qv-red-dot-close" data-bs-dismiss="modal" type="button">
<i class="bi bi-x"></i>
</button>
<div class="modal-body p-0">
<div class="row g-0">
<div class="col-md-5">
<div class="qv-img-container">
<img alt="Product" class="img-fluid rounded-4" src="<?php echo esc_url( get_template_directory_uri() . '/static' ); ?>/images/fshirt.jpg"/>
</div>
</div>
<div class="col-md-7">
<div class="qv-content-box">
<h5 class="qv-title">Best cotton fabric women's half sleeve T-shirt white color.</h5>
<div class="qv-rating mb-2">
<i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill text-muted"></i>
</div>
<p class="qv-description">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1900s.</p>
<div class="qv-price-box">
<span class="qv-current-price">$50.00</span>
<span class="qv-old-price">$62.00</span>
</div>
<div class="d-flex gap-2 mb-4">
<span class="qv-size active">S</span>
<span class="qv-size">M</span>
<span class="qv-size">L</span>
<span class="qv-size">XL</span>
</div>
<div class="d-flex align-items-center gap-3">
<div class="qv-qty-selector">
<button>-</button>
<input readonly="" type="text" value="1"/>
<button>+</button>
</div>
<a class="btn qv-cart-btn" href="cart.html">
<i class="bi bi-bag me-2"></i>Add To Cart
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Footer -->

<?php get_footer(); ?>
