<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package strateg
 *
 * Print file name for debugging: 
 *   echo strrchr(__FILE__, '/');
 */

get_header(); ?>


    <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>

			<?php
			endif;

            # Debug output of customized entries
            if(get_theme_mod('entry_debug')){
                echo("<div>entry_debug = " . get_theme_mod('entry_debug') .
                    "<br>entry_race_id = " . get_theme_mod('entry_race_id') .
                    "<br>entry_deadline = " . get_theme_mod('entry_deadline') .
                    "<br>entry_show_meal = " . get_theme_mod('entry_show_meal') .
                    "<div>");
            }

            $title_image = get_theme_mod('title_image');
            if($title_image != ''){
                echo("<img class=\"title-image\" src=\"$title_image\">");
            }

			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
