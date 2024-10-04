<?php

namespace AICommentModerator\Cron;

use AICommentModerator\Moderation\CommentModerator;

class SpamCheckCron {

    const CRON_HOOK = 'ai_comment_moderator_spam_check';

    public static function schedule() {
        // Schedule the cron job if it's not already scheduled
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time(), 'hourly', self::CRON_HOOK );
        }
        
        // Hook the cron job to the function that checks for spam
        add_action( self::CRON_HOOK, [ __CLASS__, 'check_comments_for_spam' ] );
    }

    public static function activate() {
        // Schedule the cron job upon plugin activation
        self::schedule();
    }

    public static function deactivate() {
        // Clear the cron job upon plugin deactivation
        $timestamp = wp_next_scheduled( self::CRON_HOOK );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, self::CRON_HOOK );
        }
    }

    public static function check_comments_for_spam() {
        // Fetch all comments
        $comments = get_comments( [ 'status' => 'approve' ] );

        foreach ( $comments as $comment ) {
            // Use the CommentModerator to check each comment
            $result = CommentModerator::check_comment( $comment->comment_content );

            if ( $result['spam'] ) {
                // If spam and auto-response is enabled, reply to the comment
                $auto_response = get_option( 'auto_response' );
                if ( $auto_response ) {
                    $response_mode = get_option( 'response_mode', 'professional' );
                    $relay_time = get_option( 'response_relay_time', 1000 );
                    self::reply_to_spam_comment( $comment->comment_ID, $response_mode, $relay_time );
                }

                // Log the spam detection result (log storage logic needs to be implemented)
                self::log_spam_detection( $comment, $result );
            }
        }
    }

    private static function reply_to_spam_comment( $comment_id, $response_mode, $relay_time ) {
        // Delay the response by relay time in milliseconds
        usleep( $relay_time * 1000 );

        // Generate response based on the selected response mode
        $response = self::generate_response( $response_mode );

        // Insert the response comment as a reply to the original comment
        wp_insert_comment( [
            'comment_post_ID' => get_comment( $comment_id )->comment_post_ID,
            'comment_parent'  => $comment_id,
            'comment_content' => $response,
            'comment_approved' => 1,
            'user_id'         => get_current_user_id(),
        ] );
    }

    private static function generate_response( $mode ) {
        // Return a different response based on the selected response mode
        switch ( $mode ) {
            case 'friendly':
                return "Hey there, thanks for your comment!";
            case 'humorous':
                return "Thanks! Your comment really made me chuckle!";
            case 'sarcastic':
                return "Oh wow, that’s definitely not spam at all!";
            case 'inspirational':
                return "Keep the spirit high, and thanks for commenting!";
            case 'concise':
                return "Thanks for your input!";
            case 'empathetic':
                return "I totally understand what you're saying!";
            case 'curious':
                return "Interesting! Could you tell me more about that?";
            case 'supportive':
                return "Thanks for your thoughts, we value every comment!";
            case 'informative':
                return "Thanks for the insight! Appreciate your contribution.";
            default:
                return "Thanks for your comment!";
        }
    }

    private static function log_spam_detection( $comment, $result ) {
        // Log the spam check result (this needs to be stored in the database)
        // This is a placeholder for actual implementation
        // Example:
        $log = [
            'comment'    => $comment->comment_content,
            'spam_score' => $result['score'],
            'response'   => $result['spam'] ? 'Spam detected' : 'Clean',
            'date'       => current_time( 'mysql' ),
        ];
        // Store $log in a database table for logs (table structure and insertion need implementation)
    }
}