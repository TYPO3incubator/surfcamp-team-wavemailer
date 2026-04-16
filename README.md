# Wave Mailer
Wave Mailer is a TYPO3 extension for newsletter mailing management. It provides a complete subscription workflow including a sign-up form, double opt-in confirmation, and subscription management. This allows subscribers to update their preferences or unsubscribe. Newsletters are sent asynchronously via a Symfony Messenger-based queue.

## Installation
> composer require beffp/wave-mailer

## Configuration

### Include the site set
Go to Site Management > Sites, edit your site configuration and add the "WaveMailer" set under Sets. This registers the newsletter page type, content elements, and TypoScript configuration.

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

## How to create you first newsletter
1. Create one or more subscription group records (e.g. "Monthly Updates", "Product News") on a storage page. Subscribers will be assigned to these groups.
2. In the page tree, create a new page and select the page type "Newsletter" (doktype 116). In the page properties assign one or more subscription groups that should receive this newsletter and set the send date to the desired delivery time.
3. In the page properties under Appearance, select the "Wave Mailer Newsletter Layout" backend layout. This restricts the available content elements to the newsletter-specific content elements. These content elements are optimized for emails.
4. Add content elements to the page.

## Double-Opt-In

Double Opt-In Handlin 
The Double Opt In is responsible for validating a subscribers email address by verifying a unique hash sent via email

How it works
The controller resides in the namespace php:Beffp\WaveMailer\Controller. It uses the php ‘SubscriberRepository‘ to find and upate the subscriber
Code Block:
Public function confirmAction(string $hash){
 	//logic to confirm the user
}

Main Logic 
1.	Validation: Checks if the provided php:$hash is not empty
2.	Identification: Finds a subscriber by the property php: doubleOptIn Token
3.	Activation: Sets php doubleOptIn to php true and persists the change


View Assignment
The Controller assigns a translation key tot he variable html:{message}
‘doubleOptIn.userNotFound‘: Ift he hash does not match any subscriber.
‘doubleOptIn.confirmed‘: On successful activation


