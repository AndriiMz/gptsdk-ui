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
        $repositoryId = $event->payload['metadata']['repositoryId'] ?? null;
        if (!$repositoryId) {
            return;
        }


        $repository = Repository::findOrFail($repositoryId);
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

        if (null !== $newSubscriptionStatus) {
            $repository->update([
                'subscription_status' => $newSubscriptionStatus
            ]);
        }
    }
}
