<?php

namespace AICommentModerator\Admin;

class SettingsPage {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_settings_page() {
        add_options_page(
            'Comment Moderator Settings',
            'Comment Moderator',
            'manage_options',
            'ai-comment-moderator',
            [ $this, 'create_settings_page' ]
        );
    }

    public function register_settings() {
        register_setting( 'ai_comment_moderator_group', 'spam_score' );
        register_setting( 'ai_comment_moderator_group', 'auto_response' );
        register_setting( 'ai_comment_moderator_group', 'response_mode' );
        register_setting( 'ai_comment_moderator_group', 'response_relay_time' );
        register_setting( 'ai_comment_moderator_group', 'api_key' );
    }

    public function create_settings_page() {
        ?>
        <div class="wrap">
            <h1>Comment Moderator Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'ai_comment_moderator_group' ); ?>
                <?php do_settings_sections( 'ai_comment_moderator_group' ); ?>
                <h2>Basic Settings</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Allowed Spam Score</th>
                        <td>
                            <select name="spam_score">
                                <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
                                    <option value="<?php echo esc_attr( $i ); ?>" <?php selected( get_option( 'spam_score' ), $i ); ?>><?php echo esc_html( $i ); ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Auto Response</th>
                        <td>
                            <input type="checkbox" name="auto_response" value="1" <?php checked( get_option( 'auto_response' ), 1 ); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Response Mode</th>
                        <td>
                            <select name="response_mode">
                                <option value="professional" <?php selected( get_option( 'response_mode' ), 'professional' ); ?>>Professional</option>
                                <option value="friendly" <?php selected( get_option( 'response_mode' ), 'friendly' ); ?>>Friendly</option>
                                <option value="humorous" <?php selected( get_option( 'response_mode' ), 'humorous' ); ?>>Humorous</option>
                                <option value="sarcastic" <?php selected( get_option( 'response_mode' ), 'sarcastic' ); ?>>Sarcastic</option>
                                <option value="inspirational" <?php selected( get_option( 'response_mode' ), 'inspirational' ); ?>>Inspirational</option>
                                <option value="concise" <?php selected( get_option( 'response_mode' ), 'concise' ); ?>>Concise</option>
                                <option value="empathetic" <?php selected( get_option( 'response_mode' ), 'empathetic' ); ?>>Empathetic</option>
                                <option value="curious" <?php selected( get_option( 'response_mode' ), 'curious' ); ?>>Curious</option>
                                <option value="supportive" <?php selected( get_option( 'response_mode' ), 'supportive' ); ?>>Supportive</option>
                                <option value="informative" <?php selected( get_option( 'response_mode' ), 'informative' ); ?>>Informative</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Response Relay Time (ms)</th>
                        <td>
                            <input type="number" name="response_relay_time" value="<?php echo esc_attr( get_option( 'response_relay_time', 1000 ) ); ?>" />
                        </td>
                    </tr>
                </table>
                <h2>API Settings</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td>
                            <input type="password" name="api_key" value="<?php echo esc_attr( get_option( 'api_key' ) ); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}