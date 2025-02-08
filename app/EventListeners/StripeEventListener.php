<?php

namespace App\EventListeners;

use App\Enum\SubscriptionStatus;
use App\Models\Repository;
use Laravel\Cashier\Events\WebhookReceived;

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
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            $repository->update(['subscription_status' => SubscriptionStatus::PAID]);
        }

        if ($event->payload['type'] === 'invoice.payment_failed') {
            $repository->update(['subscription_status' => SubscriptionStatus::PAST_DUE]);
        }
    }
}
