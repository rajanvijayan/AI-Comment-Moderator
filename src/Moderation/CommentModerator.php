<?php

namespace AICommentModerator\Moderation;

class CommentModerator {

    public static function check_comment( $comment_content ) {
        // Simulated AI-based spam check logic. Replace this with actual AI/ML or API integration.
        $spam_keywords = [ 'spam', 'offer', 'click', 'buy now', 'free', 'discount' ];
        $spam_score = 0;

        foreach ( $spam_keywords as $keyword ) {
            if ( stripos( $comment_content, $keyword ) !== false ) {
                $spam_score += 2; // Increase score for every spam keyword detected
            }
        }

        // Assign a spam score from 1 to 10 (1 = not spam, 10 = highly likely to be spam)
        $final_score = min( $spam_score, 10 );

        // Determine if it's spam based on the score threshold from settings
        $allowed_spam_score = get_option( 'allowed_spam_score', 5 ); // Default threshold is 5
        $is_spam = $final_score >= $allowed_spam_score;

        if ( $is_spam ) {
            return [
                'spam' => true,
                'score' => $final_score,
                'reason' => 'Contains spam keywords',
            ];
        } else {
            return [
                'spam' => false,
                'score' => $final_score,
                'response' => 'Thanks for your comment!',
            ];
        }
    }
}