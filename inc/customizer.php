<?php
/**
 * strateg Theme Customizer
 *
 * @package strateg
 */

/**
 * Sanitization functions
 */
function sanitize_race_id($raceid){
    $len = strlen($raceid);
    if($len < 4){
        return '';
    }
    $a = str_split($raceid);
    $ret = '';
    $len = min($len, 8);
    for($i = 0; $i < $len; $i++){
        $o = ord($a[$i]);
        if(($o >= ord('a') && $o <= ord('z')) || ($o >= ord('A') && $o <= ord('Z')) || ($o >= ord('0') && $o <= ord('9')) || $a[$i] == '-' || $a[$i] == '_' || $a[$i] == '.'){
            $ret .= $a[$i];
        } else {
            $ret .= 'x';
        }
    }
    return $ret;
}

function sanitize_date($date){
    $format = get_option('date_format');
    if($d = date_create_from_format($format, str_replace(' ', '', $date))){
        return $d->format($format);
    }else{
        return '';
    }
}


/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function strateg_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

    $wp_customize->add_section( 'title_image_section' , array(
    	'title'      => __( 'Title image', 'strateg' ),
        'priority'   => 90
    ));
	$wp_customize->add_setting( 'title_image' );
 	$wp_customize->add_control( new WP_Customize_Image_Control(
        $wp_customize,
        'title_image',
        array(
            'label' => __('Title image', 'strateg' ), 
            'section' => 'title_image_section',
            'settings' => 'title_image'
        )
    ));

    $wp_customize->add_section( 'entry_form_section' , array(
    	'title'      => __( 'Entry form', 'strateg' ),
        'priority'   => 180
    ));
	$wp_customize->add_setting('entry_race_id', array(
        'sanitize_callback' => 'sanitize_race_id',
    ));
	$wp_customize->add_setting('entry_deadline', array(
        'sanitize_callback' => 'sanitize_date',
    ));
    $wp_customize->add_setting('entry_show_meal');
    $wp_customize->add_setting('entries_enabled');
    $wp_customize->add_setting('entry_debug');

    $wp_customize->add_control('entry_race_id', array(
        'type' => 'text',
        'label' => 'Identifikátor závodu (4 - 8 znaků)',
        'section' => 'entry_form_section',
    ));
    $wp_customize->add_control( 'entry_deadline', array(
        'type' => 'text',
        'label'   => 'Přihlášky do',
        'section' => 'entry_form_section',
    ));
    $wp_customize->add_control( 'entry_show_meal', array(
        'type' => 'checkbox',
        'label' => 'Zobrazit dotaz na jídlo',
        'section' => 'entry_form_section',
    ));
    $wp_customize->add_control( 'entries_enabled', array(
        'type' => 'checkbox',
        'label' => 'Povolit přihlášky',
        'section' => 'entry_form_section',
    ));
    $wp_customize->add_control( 'entry_debug', array(
        'type' => 'checkbox',
        'label' => 'Ladicí výstup',
        'section' => 'entry_form_section',
    ));
}

add_action( 'customize_register', 'strateg_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function strateg_customize_preview_js() {
	wp_enqueue_script( 'strateg_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'strateg_customize_preview_js' );
