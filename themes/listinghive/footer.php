				</div>
			</div>
		</div>
		<?php if ( ! is_singular( [ 'post', 'page' ] ) || ! get_post_meta( get_the_ID(), 'ht_hide_footer', true ) ) : ?>
			<footer class="site-footer">
				<div class="container">
					<div class="marketplace-footer">
						<div class="marketplace-footer__about">
							<h2 class="marketplace-footer__title"><?php esc_html_e( 'Маркетплейс спецтехники', 'listinghive' ); ?></h2>
							<p><?php esc_html_e( 'Площадка для покупки, продажи и аренды строительной, грузовой и сельскохозяйственной техники.', 'listinghive' ); ?></p>
						</div>
						<nav class="marketplace-footer__section" aria-label="<?php esc_attr_e( 'Навигация в подвале', 'listinghive' ); ?>">
							<h2 class="marketplace-footer__heading"><?php esc_html_e( 'Навигация', 'listinghive' ); ?></h2>
							<ul>
								<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Главная', 'listinghive' ); ?></a></li>
								<li><a href="<?php echo esc_url( home_url( '/listings/' ) ); ?>"><?php esc_html_e( 'Каталог спецтехники', 'listinghive' ); ?></a></li>
								<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Статьи о технике', 'listinghive' ); ?></a></li>
							</ul>
						</nav>
						<nav class="marketplace-footer__section" aria-label="<?php esc_attr_e( 'Разделы для участников', 'listinghive' ); ?>">
							<h2 class="marketplace-footer__heading"><?php esc_html_e( 'Участникам', 'listinghive' ); ?></h2>
							<ul>
								<li><a href="<?php echo esc_url( home_url( '/submit-listing/' ) ); ?>"><?php esc_html_e( 'Разместить объявление', 'listinghive' ); ?></a></li>
								<li><a href="<?php echo esc_url( home_url( '/account/' ) ); ?>"><?php esc_html_e( 'Личный кабинет', 'listinghive' ); ?></a></li>
								<li><a href="<?php echo esc_url( home_url( '/listings/' ) ); ?>"><?php esc_html_e( 'Найти технику', 'listinghive' ); ?></a></li>
							</ul>
						</nav>
					</div>
					<div class="footer-navbar">
						<div class="footer-navbar__start">
							<div class="footer-navbar__copyright">
								<?php
								printf(
									/* translators: %s: current year. */
									esc_html__( '© %s Маркетплейс спецтехники. Все права защищены.', 'listinghive' ),
									esc_html( wp_date( 'Y' ) )
								);
								?>
							</div>
						</div>
						<div class="footer-navbar__end">
							<p class="marketplace-footer__tagline"><?php esc_html_e( 'Надёжная техника для серьёзных задач', 'listinghive' ); ?></p>
						</div>
					</div>
				</div>
			</footer>
			<?php
		endif;

		wp_footer();
		?>
	</body>
</html>
