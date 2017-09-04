<?php
/**
 * Sanitize team id for case it was forged
 */
function sanitize_id($id) {
    if(is_numeric($id)) {
        return (int) $id;
    } else {
        return 0;
    }
}

/**
 * Redirect to EXISTING page with $slug?$action, or to default page [_list]
 */
function safe_redirect($slug, $action = '', $default = '_list') {
    if($target = get_page_by_path($slug, OBJECT)) {
        if($action) $slug .= "?$action";
        wp_redirect(home_url($slug));
    } else {
        wp_redirect(home_url($default));
    }
}

/**
 * Javascript for on/off second person form when (un)checked "alone"
 */
function entry_scripts() {
    if(is_page_template('entry_form.php')){
        wp_enqueue_script('entry-js', get_template_directory_uri() . '/js/entry_form.js', array(), '20170421');
    }
}
add_action('wp_enqueue_scripts', 'entry_scripts');

/**
 * Fill form input with value from $vals global
 */
function ev($key, $index=99, $type='text', $comp='on') {
	global $vals;
	if($index == 99){
		if(isset($vals[$key])){
			$r = $vals[$key];
		}
	}else{
    	if(isset($vals[$key][$index])){
        	$r = $vals[$key][$index];
		}
    }
	if(isset($r)){
		if($type == 'text') echo " value=\"$r\"";
		elseif($type == 'check') {
			if($r == $comp) echo ' checked';
		}
	}
}

/**
 * Check team password. Skipped for site editor
 */
function pwd_check($id, $pwd) {
    if(current_user_can('edit_pages')) return TRUE;
    $prefix = get_theme_mod('entry_race_id');
    global $wpdb;
    $sql = $wpdb->prepare("SELECT password FROM `{$prefix}_team` WHERE id = %d", $id);
    $db_pwd = $wpdb->get_var($sql);
    return ($db_pwd && $pwd == $db_pwd);
}

/**
 * Fill class for invalid form input items
 */
function inval($key, $i=99) {
	global $invalid;
	if(($i == 99 && isset($invalid[$key])) || isset($invalid[$key][$i])){
		echo ' class="invalid"';
	}
}

/**
 * Entry form
 */
function entry_form() {
    global $vals, $team_id;
    if(! get_theme_mod('entries_enabled')){
?>
<div class="errmsg">Přihlášky jsou momentálně zastaveny.</div>
<?php } elseif(is_entry_time()) { ?>
<div id="entryform">
<form action="" method="post">
<div>Název týmu&nbsp;&nbsp;<input type="text" name="team" size="30"<?php ev('team'); inval('team') ?> required></div>
<?php
    for($i=0; $i<=1; $i++):
        if($i == 0){
            echo '<fieldset><legend>Biker(ka)</legend>';
        }else{
            echo '<fieldset id="second_biker"><legend>Parťák</legend>';
        }
?>
<div class="row">
  <div class="col-1">
    <label for="fname<?php echo $i?>]">Jméno</label><br />
    <input type="text" id="fname<?php echo $i?>" name="fname[<?php echo $i?>]" size="20"<?php ev('fname',$i); inval('fname', $i) ?> required>
  </div>
  <div class="col-1">
    <label for="sname<?php echo $i?>]">Příjmení</label><br />
    <input type="text" id="sname<?php echo $i?>" name="sname[<?php echo $i?>]" size="20"<?php ev('sname',$i); inval('sname', $i) ?> required>
  </div>
  <div class="col-1">
    <label for="ybirth[<?php echo $i?>]">Rok narození</label><br />
    <select name="ybirth[<?php echo $i?>]">
<?php for($j = 1950; $j <= 2010; $j++){
	echo "<option";
	if(isset($vals['ybirth'][$i])){
		if($vals['ybirth'][$i] == $j) echo " selected";
	}else{
		if($j == 1985) echo " selected";
	}
	echo ">$j</option>";
}?>
    </select>
  </div>
</div>
<div class="row">
  <div class="col-1">
    <label for="phone[<?php echo $i?>]">Telefon</label><br />
    <input type="text" name="phone[<?php echo $i?>]" size="20"<?php ev('phone',$i)?>>
  </div>
  <div class="col-1">
    <label for="email[<?php echo $i?>]">Email</label><br />
    <input type="email" name="email[<?php echo $i?>]" size="20"<?php ev('email',$i)?>>
  </div>
  <div class="col-1">
    <label for="sex[<?php echo $i?>]">Pohlaví</label><br />
    <input type="radio" value="m" name="sex[<?php echo $i?>]"<?php ev('sex', $i, 'check', 'm'); inval('sex', $i) ?>>Muž
&nbsp;<input type="radio" value="w" name="sex[<?php echo $i?>]"<?php ev('sex', $i, 'check', 'w'); inval('sex', $i) ?>>Žena
  </div>
</div>
<div class="row">
  <div class="col-1">
    <label for="shocart_id[<?php echo $i?>]">SHOCartLiga ID</label><br />
    <input type="text" name="shocart_id[<?php echo $i?>]" size="5"<?php ev('shocart_id',$i)?>>
  </div>
</div>
<?php   if($i == 0){    // First biker ?>
<div class="p"><input type="checkbox" <?php ev('alone', 99, 'check', 'on') ?> name="alone" id="alone" style="vertical-align: middle" onChange="javascript: ToggleSecond(document.getElementById('alone').checked);"> Jedu sám(a)</div>
<?php  
        }
        echo '</fieldset>';
    endfor;
?>
<div class="p"><label for="comment">Poznámka - cokoliv byste chtěli dodat</label><br />
<input type="text" name="comment" size="60"<?php echo ev('comment')?>></div>
<div class="p"><label for="password">Heslo pro změny v přihlášce</label><br />
<input type="text" name="password"<?php echo ev('password'); inval('password') ?> required></div>
<?php if(isset($team_id)): ?>
<input type="hidden" name="id" value="<?php echo $team_id ?>">
<input type="hidden" name="pwd" value="<?php echo $_REQUEST['pwd'] ?>">
<?php endif ?>
<div class="p"><input type="submit" name="ok" value=" Odeslat "></div>
</form>
</div><!-- entryform -->
<script type="text/javascript">
ToggleSecond(document.getElementById('alone').checked);
</script>
<?php
    }
}
add_shortcode('entryform', 'entry_form');

/**
 * List of entries
 */
function entry_list() {
    global $wpdb;
    $tb_prefix = get_theme_mod('entry_race_id');
    $table_t = $tb_prefix . '_team';
    $table_p = $tb_prefix . '_person';
    $entries = $wpdb->get_results("SELECT t.id, t.name, t.comment,
	  p0.fname as fname0, p0.sname as sname0, p0.sex as sex0, p0.meal as meal0,
	  p1.fname as fname1, p1.sname as sname1, p1.sex as sex1, p1.meal as meal1
      FROM `$table_t` t LEFT JOIN `$table_p` p0 ON p0.id = t.p0_id
      LEFT JOIN `$table_p` p1 ON p1.id = t.p1_id
      ORDER BY t.d_create, t.id",
      ARRAY_A);
    if($entries) {
        $url = get_template_directory_uri();
?>
<table id="entrylist">
<thead><tr>
<td class="number" width="5%">Číslo</td><td>Tým</td><td>1. závodník</td><td>2. závodník</td><td>Kat.</td><td>Poznámka</td><td class="links"></td><td class="links"></td>
</tr></thead>
<tbody>
<?php
        $i = 1;
        foreach($entries as $entry):
            $scat = $entry['sex0'] . $entry['sex1'];
            switch($scat){
                case 'mw':
                case 'wm': $cat = 'MW'; break;
                case 'mm':
                case  'm': $cat = 'MM'; break;
                case 'ww':
                case  'w': $cat = 'WW'; break;
                default: $cat = 'XX';
            }
            if(strlen($entry['comment']) > 20) $entry['comment'] = substr($entry['comment'], 0, 20) . '...';
?>
<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd' ?>">
<td class="number"><?php echo($i++) ?></td><td><?php echo $entry['name'] ?></td>
<td><?php echo $entry['fname0'] . " " . $entry['sname0'] ?></td>
<td><?php echo $entry['fname1'] . " " . $entry['sname1'] ?></td>
<td><?php echo $cat ?></td><td><?php echo $entry['comment'] ?></td>
<td class="links"><a href="<?php echo home_url('_edit?id='. $entry['id']) ?>"><img src="<?php echo $url ?>/img/edit.gif" title="Upravit" width="14" height="14" border="0"></a></td>
<td class="links"><a href="<?php echo home_url('_delete?id='. $entry['id']) ?>"><img src="<?php echo $url ?>/img/delete.gif" title="Smazat" width="14" height="14" border="0"></a></td>
</tr>
<?php   endforeach;?>
</tbody></table>
<?php
    } else {
        echo '<div class="noentries">Dosud není nikdo přihlášen.</div>';
    }
}
add_shortcode('entrylist', 'entry_list');

/**
 * Form for password checking. Common for edit and delete
 */
function pwd_form() {
    if(! get_theme_mod('entries_enabled')){
        echo '<div class="errmsg">Přihlášky jsou momentálně zastaveny.</div>';
    } else {
        if(is_page('_delete')){
            $formaction = '';
            $buttontxt = 'Smazat!';
        } elseif(is_page('_edit')) {
            $formaction = home_url('_entry');
            $buttontxt = 'Upravit';
        } else {
            echo '<div class="errmsg">Chybný odkaz. Něco je špatně.</div>';
            return;
        }
        if(isset($_REQUEST['id'])) {
            $team_id = sanitize_id($_REQUEST['id']);
            if($team_id > 0) {
                global $wpdb;
                $tb_prefix = get_theme_mod('entry_race_id'); $table_t = $tb_prefix . '_team'; $table_p = $tb_prefix . '_person';
                $entry = $wpdb->get_row("SELECT t.id, t.name, t.comment,
                  p0.fname as fname0, p0.sname as sname0, p0.sex as sex0, p0.meal as meal0,
                  p1.fname as fname1, p1.sname as sname1, p1.sex as sex1, p1.meal as meal1
                  FROM `$table_t` t LEFT JOIN `$table_p` p0 ON p0.id = t.p0_id
                  LEFT JOIN `$table_p` p1 ON p1.id = t.p1_id
                  WHERE t.id = $team_id",
                  ARRAY_A);
                if($entry) { ?>
    <table id="entryshow">
    <tr class="first"><td>Tým:</td><td><?php echo $entry['name'] ?></td></tr>
    <tr><td>1. závodník:</td><td><?php echo $entry['fname0'] . '&nbsp;' . $entry['sname0'] ?></td></tr>
    <tr><td>2. závodník:</td><td><?php echo $entry['fname1'] . '&nbsp;' . $entry['sname1'] ?></td></tr>
    <tr><td>Poznámka:</td><td><?php echo $entry['comment'] ?></td></tr>
    </table>
    <form action="<?php echo $formaction ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $team_id ?>" />
    <p>Heslo:  <input type="text" name="pwd" /></p>
    <p><input type="submit" name="pwdok" value=" <?php echo $buttontxt ?> "></p>
    </form>
    <?php
                    return;
                }
            }
        }
        echo '<div class="errmsg">Chybné, nebo neexistující ID záznamu.</div>';
    }
}
add_shortcode('pwdform', 'pwd_form');

/**
 * Accepted redirect target page. Common for new, edit and delete
 */
function accepted_page(){
    if(! isset($_REQUEST['action'])) $_REQUEST['action'] = 'none';
    switch($_REQUEST['action']) {
    case 'new':
        echo '<p>Přihláška byla přijata, budeme se těšit na setkání.</p>';
        break;
    case 'edit':
        echo '<p>Změny byly uloženy.</p>';
        break;
    case 'delete':
        echo '<p>Přihláška byla smazána.</p>';
        break;
    default:
        echo '<p>Neznámá akce, asi je něco špatně.</p>';
    }
}
add_shortcode('accepted', 'accepted_page');

/**
 * Days to entry deadline
 */
function days_remain() {
    $deadline = date_create_from_format(get_option('date_format'), get_theme_mod('entry_deadline'));
    $current_date = date_create();
    $remains = $current_date->diff($deadline);
    if($remains->invert > 0){
        echo "<div class=\"deadline\">Termín přihlášek vypršel.</div>";
    } else {
        if($remains->days < 1) {
            $days = 'již dnes!';
        } elseif($remains->days < 2) {
            $days = 'již zítra.';
        } elseif($remains->days < 5) {
            $days = 'za ' . $remains->format('%a') . ' dny.';
        } else {
            $days = 'za ' . $remains->format('%a') . ' dní.';
        }
        echo "<div class=\"deadline\">Termín přihlášek vyprší $days</div>";
    }
}
add_shortcode('remains', 'days_remain');

/**
 * Is still before entries deadline?
 */
function is_entry_time() {
    $deadline = date_create_from_format(get_option('date_format'), get_theme_mod('entry_deadline'));
    return (time() <= $deadline->getTimestamp());
}
?>
