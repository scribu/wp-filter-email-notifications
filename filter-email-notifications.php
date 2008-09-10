<?php
/*
Plugin Name: Filter Email Notifications
Version: 0.6
Description: Stop WordPress from sending email notifications for comments that have been manually approved.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/projects/filter-email-notifications.html
*/

/*
Copyright (C) 2008 scribu.net (scribu AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// Init
fen_init();
register_deactivation_hook(__FILE__, 'fen_deactivate');

function fen_init() {
	global $wp_version;
	$branch = substr($wp_version, 0, 3);

	$inc = @include_once( dirname(__FILE__) . "/inc/$branch.php" );

	if ( !$inc ) {
		add_action('admin_notices', 'fen_warning');
		return;
	}

	register_activation_hook(__FILE__, 'fen_activate');
}

function fen_warning() {
		echo '<div class="updated fade"><p><em>Filter Email Notifications</em> is <strong>not compatible</strong> with your version of WordPress.</p></div>';
}

function fen_activate() {
	add_option('fen_approved_manually');
}

function fen_deactivate() {
	delete_option('fen_approved_manually');
}

function fen_set_approved_manually($comment_id) {
	$ids = ( explode(',', get_option('fen_approved_manually') ) );
	if ( in_array($comment_id, $ids) )
		return;

	$ids[] = $comment_id;

	update_option( 'fen_approved_manually', implode(',', $ids) );
}

function fen_check_approved_manually($comment_id) {
	$ids = ( explode(',', get_option('fen_approved_manually') ) );
	$s = array_search($comment_id, $ids);
	if ($s === false)
		return false;
		unset($ids[$s]);
	update_option( 'fen_approved_manually', implode(',', $ids) );

	return true;
}

