<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package strateg
 */

if ( ! function_exists( 'strateg_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function strateg_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'strateg' ), $time_string
	);

	$byline = sprintf(
		esc_html_x( '%s', 'post author', 'strateg' ),
		'<span class="author vcard">' . esc_html( get_the_author() ) . '</span>'
	);

	echo '<span class="post-date">' . $posted_on . '</span><span class="post-author"> ' . $byline . '</span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'strateg_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function strateg_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'strateg' ) );
		if ( $categories_list && strateg_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'strateg' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'strateg' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'strateg' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
    }
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function strateg_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'strateg_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'strateg_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so strateg_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so strateg_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in strateg_categorized_blog.
 */
function strateg_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'strateg_categories' );
}
add_action( 'edit_category', 'strateg_category_transient_flusher' );
add_action( 'save_post',     'strateg_category_transient_flusher' );
