<?php

namespace AICommentModerator\Admin;

class LogsPage {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_logs_page' ] );
    }

    public function add_logs_page() {
        add_submenu_page(
            'ai-comment-moderator',
            'Comment Logs',
            'Logs',
            'manage_options',
            'ai-comment-moderator-logs',
            [ $this, 'create_logs_page' ]
        );
    }

    public function create_logs_page() {
        // Fetch logs from the database (you need to implement the log storage)
        $logs = $this->get_logs();

        ?>
        <div class="wrap">
            <h1>Comment Moderator Logs</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Comment</th>
                        <th>Spam Score</th>
                        <th>Response</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $logs ) ) : ?>
                        <?php foreach ( $logs as $log ) : ?>
                            <tr>
                                <td><?php echo esc_html( $log['comment'] ); ?></td>
                                <td><?php echo esc_html( $log['spam_score'] ); ?></td>
                                <td><?php echo esc_html( $log['response'] ); ?></td>
                                <td><?php echo esc_html( $log['date'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    private function get_logs() {
        // Implement this method to retrieve logs from the database
        // This is just a placeholder for the structure
        return [
            [
                'comment' => 'This is a test comment.',
                'spam_score' => 8,
                'response' => 'Thanks for your comment!',
                'date' => '2024-10-05',
            ],
            // Add more logs here...
        ];
    }
}