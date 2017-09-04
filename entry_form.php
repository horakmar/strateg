<?php
/**
 * The template for displaying entry form
 *
 * Template Name: EntryForm
 *
 * @package strateg
 */
$tb_prefix = get_theme_mod('entry_race_id');
$invalid = array();
$action = (isset($_REQUEST['id'])) ? 'edit' : 'new';

if(isset($_REQUEST['pwdok'])) {     // action = edit, Password form submitted
    $action = 'edit';
    $vals = [];
    $team_id = sanitize_id($_REQUEST['id']);
    if(pwd_check($team_id, $_REQUEST['pwd'])){
        $team = $wpdb->get_row("SELECT * FROM {$tb_prefix}_team WHERE id = $team_id", ARRAY_A);
        $vals['team'] = $team['name'];
        foreach(['comment', 'password'] as $f) {
            $vals[$f] = $team[$f];
        }
        for($i=0; $i<2; $i++){  // Get people information
            $key = "p{$i}_id";
            if($team[$key]){
                $person = $wpdb->get_row("SELECT * FROM {$tb_prefix}_person WHERE id = {$team[$key]}", ARRAY_A);
                foreach(['fname','sname','ybirth','sex','phone','email','shocart_id','meal'] as $f) {
                    $vals[$f][$i] = $person[$f];
                }
            }else{
                $vals['alone'] = 'on';
            }
        }
    } else {
        safe_redirect('_badpwd');
        exit;
    }
} elseif(isset($_REQUEST['ok'])) { 	// Input form submitted, action edit or new
	// Validation
	if(empty($_REQUEST['team'])) $invalid['team'] = 1;
	if(empty($_REQUEST['password'])) $invalid['password'] = 1;
	for($i=0; $i < 2; $i++) {
		$reqs = array('fname', 'sname', 'sex'); // Required items
		foreach($reqs as $r) {
			if(empty($_REQUEST[$r][$i])) $invalid[$r][$i] = 1;
		}
		if(isset($_REQUEST['alone']) && $_REQUEST['alone'] == 'on') break;
	}

	if(empty($invalid)){        // Valid
        if($action == 'edit') {
            $team_id = sanitize_id($_REQUEST['id']);
            if(! pwd_check($team_id, $_REQUEST['pwd'])) {   // Check password once more - one could forge fake id to edit form
                safe_redirect('_badpwd');
            }
            $pers_ids = $wpdb->get_row("SELECT p0_id, p1_id FROM {$tb_prefix}_team WHERE id = $team_id", ARRAY_N);
        }else{
            $pers_ids = [NULL, NULL];
        }
		$fields = ['fname','sname','ybirth','sex','phone','email','shocart_id'];
		for($i=0; $i < 2; $i++) {   // Process people
			$data = [];
			foreach($fields as $f) {
				$data[$f] = $_REQUEST[$f][$i];
			}
            $data['meal'] = isset($_REQUEST['meal'][$i]) ? $_REQUEST['meal'][$i] : 0;
            if($pers_ids[$i]) {     // Update existing person
                $data['id'] = $pers_ids[$i];
                $wpdb->replace($tb_prefix . '_person', $data);
            } else {                // Insert new person
                $wpdb->insert($tb_prefix . '_person', $data);
                $pers_ids[$i] = $wpdb->insert_id;
            }
            if(isset($_REQUEST['alone']) && $_REQUEST['alone'] == 'on'){
                if($pers_ids[1]){   // Delete if person not exists any more (with buddy -> alone)
                    $wpdb->delete($tb_prefix . '_person', ['id' => $pers_ids[1]], ['%d']);
                    $pers_ids[1] = NULL;
                }
                break;
            }
		}

		$data = [];
		$data['name'] = $_REQUEST['team'];
		$fields = ['comment', 'password'];
		foreach($fields as $f) {
			$data[$f] = $_REQUEST[$f];
		}
        $data['p0_id'] = $pers_ids[0];
        $data['p1_id'] = $pers_ids[1];
        if($action == 'edit') {
            $data['id'] = $team_id;
	     $data['d_create'] = $team['d_create'];
            $wpdb->replace($tb_prefix . '_team', $data);
        } else {
			$data['d_create'] = date('Y-m-d H:i:s');
			$wpdb->insert($tb_prefix . '_team', $data);
		}
        safe_redirect('_accepted', "action=$action");
		exit;
    } else {    // Invalid form - redisplay
        $vals = $_REQUEST;
    }
} // Form submitted end


#$res = create_tables($wpdb, $tb_prefix);

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
	if(! empty($invalid)) {
		echo '<div class="errmsg">Nejsou vyplněna povinná pole</div>';
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
