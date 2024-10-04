<?php
/**
 * Plugin Name: AI Comment Moderator
 * Description: A WordPress plugin to moderate comments using AI with spam detection and auto-response functionality.
 * Version: 1.0.0
 * Author: Rajan Vijayan
 * License: GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

// Autoload dependencies using Composer
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

use AICommentModerator\Admin\SettingsPage;
use AICommentModerator\Admin\LogsPage;
use AICommentModerator\Cron\SpamCheckCron;
use AICommentModerator\Moderation\CommentModerator;

// Initialize the plugin
function ai_comment_moderator_init() {
    // Load admin settings and logs page
    if ( is_admin() ) {
        new SettingsPage();
        new LogsPage();
    }

    // Initialize comment moderation module
    new CommentModerator();

    // Set up cron job
    SpamCheckCron::schedule();
}
add_action( 'plugins_loaded', 'ai_comment_moderator_init' );

// On plugin activation, schedule cron job
function ai_comment_moderator_activate() {
    SpamCheckCron::activate();
}
register_activation_hook( __FILE__, 'ai_comment_moderator_activate' );

// On plugin deactivation, clear scheduled cron job
function ai_comment_moderator_deactivate() {
    SpamCheckCron::deactivate();
}
register_deactivation_hook( __FILE__, 'ai_comment_moderator_deactivate' );