<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$image_count = count( (array) $listing->get_images__id() );
$images      = array_values( (array) $listing->get_images() );
$listing_url = hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] );
?>
<div class="hp-listing__image hp-listing__image--preview" data-image-count="<?php echo esc_attr( $image_count ); ?>">
	<a class="hp-listing__image-link" href="<?php echo esc_url( $listing_url ); ?>" aria-label="<?php echo esc_attr( $listing->get_title() ); ?>">
		<?php if ( $image_count ) : ?>
			<?php foreach ( $images as $image_index => $image ) : ?>
				<?php if ( strpos( $image->get_mime_type(), 'video' ) === 0 ) : ?>
					<video class="hp-listing__image-slide<?php echo 0 === $image_index ? ' hp-listing__image-slide--active' : ''; ?>" muted playsinline preload="metadata" aria-hidden="<?php echo 0 === $image_index ? 'false' : 'true'; ?>">
						<source src="<?php echo esc_url( $image->get_url() ); ?>#t=0.001" type="<?php echo esc_attr( $image->get_mime_type() ); ?>">
					</video>
				<?php else : ?>
					<img class="hp-listing__image-slide<?php echo 0 === $image_index ? ' hp-listing__image-slide--active' : ''; ?>" src="<?php echo esc_url( $image->get_url( 'hp_landscape_small' ) ); ?>" alt="<?php echo esc_attr( $listing->get_title() ); ?>" loading="lazy" aria-hidden="<?php echo 0 === $image_index ? 'false' : 'true'; ?>">
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<img class="hp-listing__image-slide hp-listing__image-slide--active" src="<?php echo esc_url( hivepress()->asset->get_image_url( get_option( 'hp_listing_placeholder_image' ), 'hp_landscape_small', hivepress()->get_url() . '/assets/images/placeholders/image-landscape.svg' ) ); ?>" alt="<?php echo esc_attr( $listing->get_title() ); ?>" loading="lazy">
		<?php endif; ?>

		<?php if ( $image_count > 1 ) : ?>
			<span class="hp-listing__image-pagination" aria-hidden="true">
				<?php for ( $image_index = 0; $image_index < $image_count; $image_index++ ) : ?>
					<span class="hp-listing__image-dot<?php echo 0 === $image_index ? ' hp-listing__image-dot--active' : ''; ?>"></span>
				<?php endfor; ?>
			</span>
		<?php endif; ?>
	</a>
</div>
