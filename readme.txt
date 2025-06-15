=== Login Bot Blocker ===
Contributors: philstudio
Tags: login, security, honeypot, anti-bot, telegram
Requires at least: 5.3
Tested up to: 6.8.1
Requires PHP: 7.2
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://yoomoney.ru/to/4100141266469

Protect your WordPress login form from bots using a hidden honeypot field. Alerts are instantly sent to Telegram when a bot is detected.

== Description ==

Login Bot Blocker 🛡️ quietly defends your login form from automated bots by injecting a hidden honeypot field. If a bot fills this field — it's blocked, logged, and instantly reported to your Telegram.

Built for security-conscious admins and developers, this plugin uses **non-intrusive logic** to reduce brute force and credential stuffing attempts.

== Features ==

* 🐝 Adds hidden honeypot trap to login form
* 🚫 Blocks bots that fill the trap field
* 🔐 Does not interfere with legitimate user login
* 📡 Sends bot detection alerts to Telegram
* ⚙️ Simple configuration via admin panel
* 👻 Avoids browser autofill with anti-autocomplete trick
* 🪓 Clean code with no anonymous functions

== Installation ==

= Using the WordPress Plugin Installer =

1. Go to your WordPress Dashboard > Plugins > Add New.
2. Search for “Login Bot Blocker”.
3. Click “Install” and then “Activate”.
4. Configure your Telegram settings in Tools > Login Bot Blocker.

= Manual Installation =

1. Upload the plugin folder to `/wp-content/plugins/login-bot-blocker/`
2. Activate via WordPress Dashboard > Plugins.
3. Set your Telegram token and chat ID.

== Frequently Asked Questions ==

= Will this block real users? =

No. The honeypot field is hidden and ignored by human users. Only bots that autofill all form inputs will be trapped.

= How do I get my Telegram chat ID and bot token? =

Use [@BotFather](https://t.me/BotFather) to create a bot and get your token. Start a chat with the bot, then use `https://api.telegram.org/bot<your_token>/getUpdates` to retrieve the chat ID.

= Can I customize the honeypot field name? =

Not yet — but it's randomized to avoid static detection.

= Does this use JavaScript or cookies? =

No JavaScript or cookies required. Pure PHP logic.

== Screenshots ==

1. Plugin settings page (Tools > Login Bot Blocker)
2. Telegram notification example

== Changelog ==

= 1.0 =
* Initial release with Telegram alerts and anti-bot honeypot trap.

== Upgrade Notice ==

= 1.0 =
First release – blocks login bots via honeypot and alerts admins via Telegram.