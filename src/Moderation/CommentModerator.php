<?php
namespace AICommentModerator\Moderation;

use AIEngine\AIEngine;

class CommentModerator {

    public function __construct() {
        // Hook into comment submission to trigger moderation check
        add_action( 'comment_post', [ $this, 'check_comment' ], 10, 2 );

        // Schedule a cron job if not already scheduled
        if ( ! wp_next_scheduled( 'ai_comment_moderation_cron' ) ) {
            wp_schedule_event( time(), 'hourly', 'ai_comment_moderation_cron' );
        }

        // Attach the cron job callback
        add_action( 'ai_comment_moderation_cron', [ $this, 'process_auto_replies' ] );
    }

    /**
     * Check if the comment should be approved or rejected.
     * 
     * @param int $comment_ID The comment ID.
     * @param int|string $approved The current approval status of the comment.
     * 
     * @return void
     */
    public function check_comment( $comment_ID, $approved ) {
        // Get the comment content using the comment ID
        $comment = get_comment( $comment_ID );
        $comment_content = $comment->comment_content;
    
        // Get AI API key from settings
        $api_key = get_option( 'api_key', '' );
        if ( empty( $api_key ) ) {
            error_log( 'API key is missing in the AI Comment Moderator plugin settings.' );
            return;
        }
    
        // Initialize AI engine with API key
        $ai_client = new AIEngine( $api_key );
        $response_mode = get_option( 'response_mode', 'professional' );
    
        // Response format for AI prompt
        $response_format = "if it is spam { 'spam': true } else { 'spam': false, comment_reply: 'REPLY MESSAGE BASED ON COMMENT' }, response mode should be '$response_mode'";
    
        // AI Prompt
        $prompt = "This is a blog post comment. Check if this is spam or not and return value in the following format: " 
                    . $response_format . " '" . $comment_content . "'";
    
        // Get the AI response
        $response = $ai_client->generateContent($prompt);
    
        error_log( 'AI response: ' . print_r( $response, true ) );
    
        // 1. Check if $response is already an array
        if (is_array($response)) {
            $response_data = $response;  // No need for json_decode, already an array
        } else {
            // 2. If it's a string, try to decode it into an array
            $response = preg_replace("/'([^']+)'/", '"$1"', $response);  // Sanitize quotes
            $response_data = json_decode($response, true);
        }
    
        // If the response says it's spam, mark the comment as spam
        if ( isset($response_data['spam']) && $response_data['spam'] === true ) {
            wp_spam_comment( $comment_ID );
        }
    
        // If not spam, store the reply message and schedule it for later via cron (if auto-response is enabled)
        $auto_response = get_option( 'auto_response', 'off' );
    
        if ( '1' === $auto_response ) {
            $reply_message = isset($response_data['comment_reply']) ? $response_data['comment_reply'] : 'Thank you for your comment!';
    
            // Store reply message in comment meta to use later with cron job
            add_comment_meta( $comment_ID, '_ai_reply_message', $reply_message );
            add_comment_meta( $comment_ID, '_ai_reply_time', time() ); // Store the time the comment was approved
        }
    }

    /**
     * Process auto-replies to approved comments based on the relay time setting.
     * This function is hooked to the cron job and will run periodically.
     */
    public function process_auto_replies() {
        // Get the response relay time from plugin settings (default: 5000 milliseconds)
        $relay_time_ms = (int) get_option( 'response_relay_time', 5000 );

        // Convert relay time to seconds for comparison
        $relay_time_seconds = $relay_time_ms / 1000;

        // Query all comments that are awaiting an AI reply
        $comments_query = new \WP_Comment_Query( [
            'meta_key'    => '_ai_reply_message',
            'meta_compare' => 'EXISTS',
            'status'      => 'approve', // Only approved comments
        ]);

        foreach ( $comments_query->get_comments() as $comment ) {
            $comment_id = $comment->comment_ID;
            $reply_time = get_comment_meta( $comment_id, '_ai_reply_time', true );

            // Check if the relay time has passed since the comment was approved
            if ( ( time() - $reply_time ) >= $relay_time_seconds ) {
                $this->send_auto_reply( $comment_id );
            }
        }
    }

    /**
     * Send the AI-generated reply to the comment.
     * 
     * @param int $comment_id The ID of the comment.
     */
    public function send_auto_reply( $comment_id ) {
        // Get the reply message from comment meta
        $reply_message = get_comment_meta( $comment_id, '_ai_reply_message', true );
    
        // If a reply message exists, post it as a comment reply
        if ( $reply_message ) {
            $comment_data = get_comment( $comment_id );
    
            // Retrieve the user ID from the plugin settings
            $moderator_user_id = get_option( 'moderator_user' );
    
            // Prepare reply comment data
            $reply_data = [
                'comment_post_ID' => $comment_data->comment_post_ID,
                'comment_parent'  => $comment_data->comment_ID,
                'comment_content' => $reply_message,
                'user_id'         => $moderator_user_id, // Use the moderator user ID from settings
                'comment_approved'=> 1, // Auto approve reply
            ];
    
            // Insert the reply comment
            wp_insert_comment( $reply_data );
    
            // Clean up the meta after posting the reply
            delete_comment_meta( $comment_id, '_ai_reply_message' );
            delete_comment_meta( $comment_id, '_ai_reply_time' );
        }
    }
}