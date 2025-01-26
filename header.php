<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package JM-theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'jm-theme' ); ?></a>

	<header id="masthead" class="site-header" style="background-color: #f5f5f5; padding: 20px 0; border-bottom: 2px solid #ddd;">
		<div class="site-branding" style="text-align: center;">
			<?php
			the_custom_logo();
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title" style="font-size: 2.5rem; font-weight: bold; margin: 0;">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" style="text-decoration: none; color: #333;">
						<?php bloginfo( 'name' ); ?>
					</a>
				</h1>
				<?php
			else :
				?>
				<p class="site-title" style="font-size: 2rem; font-weight: bold; margin: 0;">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" style="text-decoration: none; color: #333;">
						<?php bloginfo( 'name' ); ?>
					</a>
				</p>
				<?php
			endif;
			$jm_theme_description = get_bloginfo( 'description', 'display' );
			if ( $jm_theme_description || is_customize_preview() ) :
				?>
				<p class="site-description" style="font-size: 1.2rem; color: #666; margin-top: 10px;">
					<?php echo $jm_theme_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</p>
			<?php endif; ?>
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation" style="margin-top: 20px; text-align: center;">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" style="padding: 10px 20px; background-color: #333; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
				<?php esc_html_e( 'Primary Menu', 'jm-theme' ); ?>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
					'container'      => 'div',
					'container_class'=> 'menu-container',
					'container_style'=> 'margin-top: 10px; display: inline-block;',
					'menu_class'     => 'menu-list',
					'echo'           => true,
					'before'         => '',
					'after'          => '',
					'link_before'    => '<span style="padding: 5px 10px; display: inline-block; color: #333; text-decoration: none;">',
					'link_after'     => '</span>',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->
