# Slack Notification Service

This project demonstrates a simple integration with Slack using Laravel, enabling message delivery to Slack channels via Laravel commands. The implementation leverages a dedicated `SlackService` for handling API requests and a `SlackMessage` command for interacting with users through Artisan commands.

## Features

- Send messages to Slack channels using the Slack API.
- Customize the channel and message content via Artisan command options.
- Send a direct message to a user upon login (one-time only, per session).
- Clean separation of concerns with the `SlackService` handling API communication.
- Utilize Laravel Pint for code formatting and adherence to Laravel standards.

## Prerequisites

- A Slack workspace with a valid bot token.
- Laravel 9.x or higher installed in your project.
- A valid Slack Webhook URL for sending notifications.

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/arthurvpires/slack-api
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

3. Configure your environment:
   Add your Slack bot token and Webhook URL to your `.env` file:

   ```env
   SLACK_BOT_TOKEN=<your_slack_bot_token>
   SLACK_WEBHOOK_URL=<your_slack_webhook_url>
   ```

## Usage

### 1. SlackService

The `SlackService` handles all requests to the Slack API, including listing channels and sending messages.

Example methods in `SlackService`:

- `listChannels()`: Retrieves all channels in the Slack workspace.
- `sendMessage(string $channelId, string $message)`: Sends a message to the specified Slack channel.
- `getUserIdByEmail(string $email)`: Returns the user ID associated with the provided email address.

### 2. SlackMessage Command

The `SlackMessage` command is used to send messages via the Laravel Artisan CLI. It utilizes the `SlackService` for the actual API communication.

#### Command Syntax

```bash
php artisan send:slack-message --channel=<channel_name> --message=<message_text>
```

#### Command Options

- `--channel`: Specifies the Slack channel to send the message to (e.g., `#general`). If omitted, a default channel (`integração-api`) is used.
- `--message`: Specifies the content of the message to be sent. If omitted, a default message (`Mensagem enviada via Slack API`) is used.

#### Examples

1. Send a message to a specific channel:

   ```bash
   php artisan send:slack-message --channel="#general" --message="Hello Slack!"
   ```

2. Use default values:

   ```bash
   php artisan send:slack-message
   ```

   This sends the default message to the default channel.

3. Customize the message only:

   ```bash
   php artisansend:slack-message --message="Testing options!"
   ```

4. Customize the channel only:

   ```bash
   php artisan send:slack-message --channel="#random"
   ```

### 3. Automatic Login Notification

When a user logs in, the application sends a direct message (DM) to the user via Slack. This notification is sent only once per session. The logic involves:

- Checking if a session variable indicates the message has already been sent.
- If not, the `SlackService` sends a DM to the user.
- The session variable is then updated to prevent duplicate notifications.

#### Implementation

- The login notification logic is triggered in the `UserController`.
- Example workflow:
  1. Upon successful login, check for the session variable `has_received_login_notification`.
  2. If the variable is not set, send a DM using `SlackService`.
  3. Set the session variable `has_received_login_notification` to `true`.

## Code Structure

### SlackService

- Location: `app/Services/SlackService.php`
- Responsible for:
  - Interacting with Slack API endpoints.
  - Abstracting the API details from other parts of the application.

### SlackMessage Command

- Location: `app/Console/Commands/SlackMessage.php`
- Responsible for:
  - Handling user input via Artisan.
  - Passing user options to the `SlackService`.

### Configuration

- Slack Webhook URL and Bot Token are stored in the `.env` file.

## Code Formatting with Laravel Pint

Laravel Pint is used to maintain a clean and consistent code style. Run Pint to format your codebase:

   ```bash
   ./vendor/bin/pint

   ```
