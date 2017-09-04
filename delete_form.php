<?php
/**
 * The template for deleting entry records
 *
 * Template Name: DeleteForm
 *
 * @package strateg
 */
$tb_prefix = get_theme_mod('entry_race_id');
if(isset($_REQUEST['pwdok'])) {     // Password form submitted
    $id = sanitize_id($_REQUEST['id']);
    if(pwd_check($id, $_REQUEST['pwd'])){
        $pers_ids = $wpdb->get_row("SELECT p0_id, p1_id FROM {$tb_prefix}_team WHERE id = $id", ARRAY_N);
        foreach($pers_ids as $i){   // Delete people
            if($i){
                $wpdb->delete($tb_prefix . '_person', ['id' => $i], ['%d']);
            }
        }
        $wpdb->delete($tb_prefix . '_team', ['id' => $id], ['%d']);
        safe_redirect('_accepted', 'action=delete');
    } else {
        $errmsg = 'Heslo nesouhlasí, přihlášku nelze smazat.';
    }
}


#$res = create_tables($wpdb, $tb_prefix);

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
	if(isset($errmsg)) {
		echo "<div class=\"errmsg\">$errmsg</div>";
	}
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/content', 'page' );
			endwhile; // End of the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
?>
