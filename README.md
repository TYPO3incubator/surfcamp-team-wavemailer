# Wave Mailer

TYPO3 extension for sending newsletters with integrated link tracking and subscriber management.

## Features
- **Special Page Type**: Dedicated doktype for newsletter pages.
- **Dispatch Automation**: Scheduled dispatch via TYPO3 Messenger.
- **Subscriber Management**: Support for double opt-in and self-service subscription management.
- **Link Tracking**: Integrated analysis of link clicks within newsletters.
- **Backend Dashboard**: Visual representation of newsletter performance.

## Installation

1. **Composer**:
   ```bash
   composer require beffp/wave-mailer
   ```
2. **Activation**: Activate in the TYPO3 Extension Manager.
3. **Template**: Include the `WaveMailer` site set in the site configuration or via TypoScript.

## Setup

### 1. Configure Scheduler Task
To automatically move due newsletters to the dispatch queue, the following command must be executed regularly (e.g., every minute):
- **Command**: `wavemailer:queue-emails`
- **Options**: `--batch-size` (default: 50) limits the number of emails processed per run.

### 2. Messenger Worker
Since dispatch is asynchronous, a messenger worker must be running. In a development environment, this can be started manually:
```bash
./vendor/bin/typo3 messenger:consume default
```
In production environments, this should be ensured via a process manager like `systemd` or `supervisor`.

### 3. TypoScript Configuration
The extension uses TypoScript constants for important settings. These should be set in the Constant Editor or via TypoScript:
- `plugin.tx_wavemailer.settings.storagePid`: The PID of the folder where subscribers and groups are stored.
- `plugin.tx_wavemailer.settings.doubleOptInPage`: Page with the "DoubleOptIn Confirmation" plugin.
- `plugin.tx_wavemailer.settings.manageSubscriptionPage`: Page with the "Manage Subscription" plugin.
- `plugin.tx_wavemailer.settings.subscriptionConfirmationPage`: Page to redirect to after submitting the registration form.

## Plugins and Content Elements

### Plugins
- **Newsletter Subscription**: Registration form. Available subscriber groups can be defined in the settings.
- **Manage Subscription**: Allows users to change their group memberships or unsubscribe.
- **Request Management Link**: Sends the user a secure link to the `Manage Subscription` page.
- **Double Opt-In Confirmation**: Processes the registration confirmation after clicking the link in the email.

### Content Elements
- **Newsletter Text & Media**: An optimized content element for designing newsletter content.

## Workflow: Sending Newsletters

1. **Create Page**: Create a page of type **Newsletter** (doktype 116).
2. **Configuration**:
    - Select the desired **Subscriber Groups** under the **Wave Mailer** tab.
    - Set the **Send Date**.
3. **Content**: Fill the page with content (e.g., `Newsletter Text & Media`).
4. **Dispatch**: At the defined time, the scheduler task will generate the emails and place them in the queue.

## Link Tracking & Analytics
The extension automatically records clicks on links within the newsletter.

- **Dashboard**: In the `Wave Mailer` backend module, you get an overview of all sent newsletters.
- **Details**: Total clicks and clicks per individual link are displayed for each newsletter.
- **Integration**: Internal TYPO3 links are resolved and show the page title. Clicking the link in the dashboard leads directly to the corresponding page in the backend layout module.

## Technical Details
- **Middleware**: The `LinkTrackingMiddleware` registers clicks based on URL parameters.
- **Web Components**: The backend dashboard uses modern Web Components (`wave-mailer-newsletter-card`) for a performant and CSP-compliant user interface.

