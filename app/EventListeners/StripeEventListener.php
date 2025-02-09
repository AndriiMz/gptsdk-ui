<?php

namespace App\EventListeners;

use App\Enum\SubscriptionStatus;
use App\Models\Repository;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Event;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        var_dump($event->payload);
        die;

        if (
            $event->payload['type'] === Event::CHECKOUT_SESSION_COMPLETED &&
            isset($event->payload['metadata']['repositoryId'])
        ) {
            $repository = Repository::findOrFail(
                $event->payload['metadata']['repositoryId']
            );
            var_dump($repository->id);
            die;

            $repository->update([
                'subscription_status' => SubscriptionStatus::PAID,
                'subscription_id' => $event->payload['subscription']
            ]);

            return;
        }

        $repository = Repository::firstWhere('subscription_id', $event->payload['id']);
        $newSubscriptionStatus = null;
        switch ($event->payload['type']) {
            case Event::CUSTOMER_SUBSCRIPTION_CREATED:
            case Event::CUSTOMER_SUBSCRIPTION_RESUMED:
                $newSubscriptionStatus = SubscriptionStatus::PAID;
                break;
            case Event::CUSTOMER_SUBSCRIPTION_DELETED:
                $newSubscriptionStatus = SubscriptionStatus::FREE;
                break;
            case Event::CUSTOMER_SUBSCRIPTION_PAUSED:
                $newSubscriptionStatus = SubscriptionStatus::PAST_DUE;
                break;
        }

        if (null !== $newSubscriptionStatus && $repository) {
            $repository->update([
                'subscription_status' => $newSubscriptionStatus
            ]);
        }
    }
}
