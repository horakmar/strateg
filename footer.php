<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package strateg
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
        <?php if ( is_active_sidebar( 'footer_text' ) ){
            dynamic_sidebar( 'footer_text' );
        } ?>
		<div class="site-info">
			Web běží na <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'strateg' ) ); ?>">WordPressu</a>.
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
