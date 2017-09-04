<?php
/**
 * Create database tables with given prefix
 */
function create_tables($prefix) {
	global $wpdb;
    $res = $wpdb->query("
CREATE TABLE IF NOT EXISTS `{$prefix}_person` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fname` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `sname` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `ybirth` year(4) NOT NULL DEFAULT '1985',
  `sex` enum('m','w') DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET ascii NOT NULL,
  `email` varchar(50) CHARACTER SET ascii NOT NULL,
  `d_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `shocart_id` int(10) unsigned NOT NULL,
  `meal` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    if($res){
        $res = $wpdb->query("
CREATE TABLE IF NOT EXISTS `{$prefix}_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `name` varchar(80) COLLATE utf8_czech_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `admin_comment` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `d_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `d_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `password` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `p0_id` int(11) DEFAULT NULL,
  `p1_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;");
    }
    return $res;
}

/**
 * Database tables creation page
 */
function tables_init() {
	$prefix = get_theme_mod('entry_race_id');
	if(empty($prefix)) {
		echo '<div class="errmsg">Tabulky nelze vytvořit, není vyplněn identifikátor závodu.</div>';
	} elseif(create_tables($prefix)) {
		echo '<div class="okmsg">Tabulky vytvořeny.</div>';
	} else {
		echo '<div class="errmsg">Tabulky se nepodařilo vytvořit.</div>';
	}
}
add_shortcode('tbinit', 'tables_init');
?>
