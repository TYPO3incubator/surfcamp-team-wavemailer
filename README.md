# Wave Mailer
Wave Mailer is a TYPO3 extension for newsletter mailing management. It provides a complete subscription workflow including a sign-up form, double opt-in confirmation, and subscription management. This allows subscribers to update their preferences or unsubscribe. Newsletters are sent asynchronously via a Symfony Messenger-based queue.

## Installation
> composer require beffp/wave-mailer

## Configuration

### Queue and send newsletter emails
Newsletter sending is a two-step process:
1. The ```wavemailer:queue-emails``` command finds newsletter pages (doktype 116) whose send date has passed, resolves the assigned subscription groups, and creates a mail queue entry for each active subscriber. Each entry is dispatched as a SendNewsletterMessage to the Symfony Messenger queue.
2. The ```messenger:consume``` command picks up the queued messages and sends the actual emails. The newsletter content is rendered by fetching the page via an internal HTTP request, so the page must be accessible from the server. Failed deliveries are retried up to 5 times before being marked as failed.

#### Setup
Create two scheduler tasks in the backend module Administration > Scheduler:
1. ```wavemailer:queue-emails``` Accepts an optional --batch-size (-b) option to limit the number of emails queued per run (default: 50). Should run periodically (e.g. every minute) to ensure timely delivery.
2. ```messenger:consume``` This is a standard TYPO3/Symfony Messenger consumer command. Check the documentation to configure the automatic restart: https://docs.typo3.org/permalink/t3coreapi:message-bus-consume-command

**Mail queue statuses:**

| Status | Description                                    |
|--------|------------------------------------------------|
| queued | Entry created, message dispatched to the queue |
| sent   | Email successfully delivered                   |
| failed | Delivery failed after 5 retry attempts         |

### Anonymize unsubscribed users
To be GDPR-compliant there is a command to anonymize the first name, last name and email address of unsubscribed users.
Create the scheduler task ```wavemailer:anonymize-subscribers```in the backend module Administration > Scheduler and choose the number of days after which the unsubscribed users should be anonymized.

