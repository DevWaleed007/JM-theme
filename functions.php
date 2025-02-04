<?php
/**
 * JM-theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package JM-theme
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function jm_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on JM-theme, use a find and replace
		* to change 'jm-theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'jm-theme', get_template_directory() . '/languages' );
	
    // Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	
	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );
	
	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'jm-theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'jm_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'jm_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function jm_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'jm_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'jm_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function jm_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'jm-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'jm-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'jm_theme_widgets_init' );
// Add a meta box for "Rating"
function jm_add_page_rating_meta_box() {
    add_meta_box(
        'jm_page_rating',
        __( 'Page Rating', 'jm-theme' ),
        'jm_page_rating_meta_box_callback',
        'page',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'jm_add_page_rating_meta_box' );

function jm_page_rating_meta_box_callback( $post ) {
    $current_rating = get_post_meta( $post->ID, '_jm_page_rating', true );
    ?>
    <label for="jm_page_rating"><?php _e( 'Set a rating (1-5):', 'jm-theme' ); ?></label>
    <select id="jm_page_rating" name="jm_page_rating">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <option value="<?php echo $i; ?>" <?php selected( $current_rating, $i ); ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <?php
}

// Save the meta box value
function jm_save_page_rating_meta_box( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save the rating if set
    if ( isset( $_POST['jm_page_rating'] ) ) {
        $rating = intval( $_POST['jm_page_rating'] );
        if ( $rating >= 1 && $rating <= 5 ) {
            update_post_meta( $post_id, '_jm_page_rating', $rating );
        }
    }
}
add_action( 'save_post', 'jm_save_page_rating_meta_box' );

/**
 * Enqueue scripts and styles.
 */
function jm_theme_scripts() {
	wp_enqueue_style( 'jm-theme-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'jm-theme-style', 'rtl', 'replace' );

	wp_enqueue_script( 'jm-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	// Enqueue infinite scroll JavaScript
	wp_enqueue_script('jm-infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.js', array('jquery'), '1.0', true);

	wp_localize_script('jm-infinite-scroll', 'jmThemeData', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
	));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'jm_theme_scripts' );

// Add Typography Settings to Customizer
function jm_customize_typography( $wp_customize ) {
    // === Global Typography Section ===
    $wp_customize->add_section('jm_typography', array(
        'title'    => __('Global Typography', 'jm-theme'),
        'priority' => 30,
    ));

    // Text Color 
    $wp_customize->add_setting('jm_text_color', array(
        'default'   => '#000000',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'jm_text_color', array(
        'label'   => __('Text Color ', 'jm-theme'),
        'section' => 'jm_typography',
        'settings' => 'jm_text_color',
    )));

    // Heading Color (h1-h6, but NOT links)
    $wp_customize->add_setting('jm_heading_color', array(
        'default'   => '#0000ff',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'jm_heading_color', array(
        'label'   => __('Heading Color', 'jm-theme'),
        'section' => 'jm_typography',
        'settings' => 'jm_heading_color',
    )));

    // Text Font Size
    $wp_customize->add_setting('jm_text_font_size', array(
        'default'   => '16px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('jm_text_font_size', array(
        'label'   => __('Text Font Size ', 'jm-theme'),
        'section' => 'jm_typography',
        'type'    => 'text',
    ));

    // Heading Font Size
    $wp_customize->add_setting('jm_heading_font_size', array(
        'default'   => '24px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('jm_heading_font_size', array(
        'label'   => __('Heading Font Size', 'jm-theme'),
        'section' => 'jm_typography',
        'type'    => 'text',
    ));

    // === Blog Page Colors Section ===
    $wp_customize->add_section('jm_blog_colors', array(
        'title'    => __('Blog Page Colors', 'jm-theme'),
        'priority' => 31,
    ));

    // Blog Page Text Color
    $wp_customize->add_setting('jm_blog_text_color', array(
        'default'   => '#333333',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'jm_blog_text_color', array(
        'label'   => __('Blog Text Color', 'jm-theme'),
        'section' => 'jm_blog_colors',
        'settings' => 'jm_blog_text_color',
    )));

     // Blog Page Heading Color
     $wp_customize->add_setting('jm_blog_heading_color', array(
        'default'   => '#ff0000',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'jm_blog_heading_color', array(
        'label'   => __('Blog Page Heading Color', 'jm-theme'),
        'section' => 'jm_blog_colors',
        'settings' => 'jm_blog_heading_color',
    )));

    // Blog Page Text Font Size
    $wp_customize->add_setting('jm_blog_text_font_size', array(
        'default'   => '16px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('jm_blog_text_font_size', array(
        'label'   => __('Blog Text Size', 'jm-theme'),
        'section' => 'jm_blog_colors',
        'type'    => 'text',
    ));

    // Blog Heading Font Size 
    $wp_customize->add_setting('jm_blog_heading_font_size', array(
        'default'   => '24px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('jm_blog_heading_font_size', array(
        'label'   => __('Blog Heading Size', 'jm-theme'),
        'section' => 'jm_blog_colors',
        'type'    => 'text',
    ));
}
add_action('customize_register', 'jm_customize_typography');

// Apply typography settings to the frontend
function jm_apply_customizer_styles() {
    ?>
    <style type="text/css">
        /* Global Styles */
        body p {
            color: <?php echo esc_html(get_theme_mod('jm_text_color', '#000000')); ?>;
            font-size: <?php echo esc_html(get_theme_mod('jm_text_font_size', '16px')); ?>;
        }

        h1, h2, h3, h4, h5, h6 {
            color: <?php echo esc_html(get_theme_mod('jm_heading_color', '#0000ff')); ?>;
            font-size: <?php echo esc_html(get_theme_mod('jm_heading_font_size', '24px')); ?>;
        }

        /* Blog Page Styles */
        <?php if (is_home() || is_archive()) : ?>
            body p {
                color: <?php echo esc_html(get_theme_mod('jm_blog_text_color', '#333333')); ?>;
                font-size: <?php echo esc_html(get_theme_mod('jm_blog_text_font_size', '16px')); ?>;
            }

            h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
                color: <?php echo esc_html(get_theme_mod('jm_blog_heading_color', '#ff0000')); ?>;
                font-size: <?php echo esc_html(get_theme_mod('jm_blog_heading_font_size', '24px')); ?>;
            }
        <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'jm_apply_customizer_styles');

// Display rating on the frontend for pages
function jm_display_rating_on_page($content) {
    global $post;

    if (is_page()) {
        $post_id = $post->ID;
        $rating = get_post_meta($post_id, '_jm_page_rating', true);

        if ($rating) {
            $rating_html = '<div class="page-rating">';
            $rating_html .= '<strong>Rating:</strong> ' . esc_html($rating) . ' / 5';
            $rating_html .= '</div>';
            $content .= $rating_html;
        }
    }

    // Check if we're on the blog page and handle infinite scroll
    if (is_home() && get_option('page_for_posts')) {
        $post_id = get_option('page_for_posts');
        $rating = get_post_meta($post_id, '_jm_page_rating', true);

        if ($rating) {
            $rating_html = '<div id="jm-blog-rating" class="page-rating" style="display: none;">';
            $rating_html .= '<strong>Rating:</strong> ' . esc_html($rating) . ' / 5';
            $rating_html .= '</div>';
            $content .= '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                document.getElementById("jm-blog-rating").style.display = "block";
                            }
                        });
                    }, { threshold: 1.0 });

                    var lastPost = document.querySelector(".post:last-of-type");
                    if (lastPost) {
                        observer.observe(lastPost);
                    }
                });
            </script>' . $rating_html;
        }
    }

    return $content;
}

add_filter('the_content', 'jm_display_rating_on_page', 20);

// funtion for inite ajax scroll
function jm_load_more_posts() {
    $paged = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;

    $query = new WP_Query([
        'post_type' => 'post',
        'paged' => $paged,
    ]);

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/content', get_post_type() );
        }
    }

    wp_die();
}
add_action( 'wp_ajax_load_more_posts', 'jm_load_more_posts' );
add_action( 'wp_ajax_nopriv_load_more_posts', 'jm_load_more_posts' );

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}
