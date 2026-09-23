<?php
/**
 * Product card used by WooCommerce loops and the product sections on the home page.
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$product_id        = $product->get_id();
$product_url       = get_permalink( $product_id );
$categories        = wc_get_product_category_list( $product_id, ', ' );
$variation_values  = array();
$colour_values     = array();

if ( $product->is_type( 'variable' ) ) {
	foreach ( $product->get_variation_attributes() as $attribute_name => $options ) {
		$attribute_label = wc_attribute_label( $attribute_name, $product );
		$is_colour       = false !== stripos( $attribute_name, 'color' ) || false !== stripos( $attribute_name, 'colour' ) || false !== stripos( $attribute_label, 'цвет' );

		if ( $is_colour ) {
			$colour_values = array_merge( $colour_values, $options );
		} else {
			$variation_values = array_merge( $variation_values, $options );
		}
	}
}

$compare_url = apply_filters(
	'mantu_product_compare_url',
	add_query_arg(
		array(
			'action' => 'yith-woocompare-add-product',
			'id'     => $product_id,
		),
		home_url( '/' )
	),
	$product
);

$badge       = '';
$badge_class = '';
if ( $product->is_on_sale() ) {
	$badge       = __( 'Sale', 'mantu' );
	$badge_class = 'na-badge--sale';
} elseif ( ( time() - get_post_timestamp( $product_id ) ) < MONTH_IN_SECONDS ) {
	$badge       = __( 'New', 'mantu' );
	$badge_class = 'na-badge--new';
}
?>
<div <?php wc_product_class( 'na-col', $product ); ?>>
	<div class="na-card">
		<div class="na-imgbox">
			<?php if ( $badge ) : ?>
				<span class="na-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<a href="<?php echo esc_url( $product_url ); ?>">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'na-img' ) ) ); ?>
			</a>
			<div class="na-actions">
				<a class="na-act-btn na-act-btn--qv" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'mantu' ), $product->get_name() ) ); ?>" title="<?php esc_attr_e( 'View product', 'mantu' ); ?>">
					<i class="bi bi-eye" aria-hidden="true"></i>
				</a>
				<a class="na-act-btn na-act-btn--cmp compare" href="<?php echo esc_url( $compare_url ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" rel="nofollow" aria-label="<?php echo esc_attr( sprintf( __( 'Compare %s', 'mantu' ), $product->get_name() ) ); ?>" title="<?php esc_attr_e( 'Compare', 'mantu' ); ?>">
					<i class="bi bi-shuffle" aria-hidden="true"></i>
				</a>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" class="na-act-btn na-act-btn--cart <?php echo esc_attr( implode( ' ', array_filter( array( 'button', $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '', 'product_type_' . $product->get_type() ) ) ) ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" rel="nofollow" aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>" title="<?php echo esc_attr( $product->add_to_cart_text() ); ?>">
					<i class="bi bi-bag" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<div class="na-info">
			<div class="na-meta">
				<span class="na-type"><?php echo wp_kses_post( $categories ?: __( 'Product', 'mantu' ) ); ?></span>
				<?php if ( $variation_values ) : ?>
					<span class="na-sizes"><?php echo esc_html( implode( ' ', array_map( 'urldecode', array_unique( $variation_values ) ) ) ); ?></span>
				<?php endif; ?>
			</div>
			<h4 class="na-title"><a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h4>
			<div class="na-prices"><span class="na-price-now"><?php echo wp_kses_post( $product->get_price_html() ); ?></span></div>
			<div class="na-swatches">
				<?php foreach ( array_unique( $colour_values ) as $colour ) :
					$colour_name = urldecode( $colour );
					$css_colour  = sanitize_hex_color( $colour_name );
					if ( ! $css_colour && preg_match( '/^[a-zA-Z]+$/', $colour_name ) ) {
						$css_colour = strtolower( $colour_name );
					}
				?>
					<span class="na-swatch" title="<?php echo esc_attr( $colour_name ); ?>"<?php echo $css_colour ? ' style="background:' . esc_attr( $css_colour ) . ';"' : ''; ?>></span>
				<?php endforeach; ?>
				<a class="na-heart" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'mantu' ), $product->get_name() ) ); ?>"><i class="bi bi-heart" aria-hidden="true"></i></a>
			</div>
		</div>
	</div>
</div>
