# AI Comment Moderator

**AI Comment Moderator** is a WordPress plugin that uses AI to moderate comments by detecting spam and automatically responding based on predefined settings.

## Features
- Set spam score sensitivity (1-10)
- Auto-response functionality with multiple response modes (Professional, Friendly, Humorous, etc.)
- API Key integration for AI services
- Log page to view all moderated comments and responses
- Cron job for continuous comment moderation

## Installation

1. Clone or download the plugin.
2. Place the `ai-comment-moderator` folder in your WordPress `/wp-content/plugins/` directory.
3. Run `composer install` to generate the autoloader.
4. Activate the plugin in the WordPress admin panel.

## Usage

1. Go to **Settings > Comment Moderator** to configure your moderation settings.
2. Set the allowed spam score, enable auto-response, and select the desired response mode.
3. Enter your API key for the AI spam detection service.

## Requirements

- WordPress 5.5+
- PHP 7.4+

## License

This plugin is licensed under the GPL-2.0-or-later license.