<?php

namespace App\Http\Controllers\UiApi;

use App\Enum\SubscriptionStatus;
use App\Models\Repository;
use Illuminate\Http\JsonResponse;
use Stripe\Subscription;

class RepositoryUiApiController
{
    public function delete(Repository $repository)
    {
        if ($repository->subscription_status === SubscriptionStatus::PAID) {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $subscription = Subscription::retrieve($repository->subscription_id);
            $subscription->cancel();
        }

        $repository->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
