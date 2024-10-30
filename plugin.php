<?php

/*
	Plugin Name: CC-Disable-Date
	Plugin URI:  https://wordpress.org/plugins/cc-disable-date
	Description: This plugin allows you to selectively remove the publication date from blog posts and posts listing.
	Version:     1.0.0
	Author:      Clearcode
	Author URI:  https://clearcode.cc
	Text Domain: cc-disable-date
	Domain Path: /
	License:     GPL-3.0+
	License URI: http://www.gnu.org/licenses/gpl-3.0.txt

	Copyright (C) 2017 by Clearcode <https://clearcode.cc>
	and associates (see AUTHORS.txt file).

	This file is part of CC-Disable-Date.

	CC-Disable-Date is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	CC-Disable-Date is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with CC-Disable-Date; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined( 'ABSPATH' ) or exit;

add_action( 'add_meta_boxes', function() {
	if ( current_user_can( 'edit_posts' ) )
		add_meta_box(
			'cc-disable-date',
			__( 'Date', 'cc-disable-date' ),
			function( $post ) {
				wp_nonce_field( 'cc-disable-date', 'cc-disable-date' );
				printf(
					'<input type="checkbox" name="_cc-disable-date" value="1" %s /> %s',
					get_post_meta( $post->ID, '_cc-disable-date', true ) ? checked( true, true, false ) : '',
					__( 'Disable', 'cc-disable-date' )
				);
			},
			'post',
			'side'
		);
} );

add_action( 'save_post', function( $post_id ) {
	if ( ! current_user_can( 'edit_posts', $post_id ) ) return;
	if ( 'post' != get_post_type( $post_id ) ) return;
	if ( ! isset( $_REQUEST['cc-disable-date'] ) ) return;
	if ( ! check_admin_referer( 'cc-disable-date', 'cc-disable-date' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! empty( $_REQUEST['_cc-disable-date'] ) ) update_post_meta( $post_id, '_cc-disable-date', true );
	else delete_post_meta( $post_id, '_cc-disable-date' );
} );

add_filter( 'get_the_date', function( $the_date, $d, $post ) {
	return (bool)get_post_meta( $post->ID, '_cc-disable-date', true ) ? '' : $the_date;
}, 10, 3 );
