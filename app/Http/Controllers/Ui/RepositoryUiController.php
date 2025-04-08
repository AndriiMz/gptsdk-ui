<?php

namespace App\Http\Controllers\Ui;

use App\Data\RepositoryFormData;
use App\Enum\Status;
use App\Enum\SubscriptionStatus;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RepositoryUiController
{
    public function upsertRepository(
        RepositoryFormData $repositoryData,
        ?Repository $repository = null
    ) {
        /** @var User $user */
        $user = Auth::user();
        if ($repository) {
            $repository->update(
                array_merge(
                    $repositoryData->toArray()
                ));
        } else {
            $repository = Repository::create(
                array_merge(
                    $repositoryData->toArray(),
                    [
                        'user_id' => $user->id,
                        'subscription_status' => $user->paidRepositories()->count() < $user->free_repositories_limit ?
                            SubscriptionStatus::PAID : SubscriptionStatus::FREE
                    ]
                )
            );
        }

        return to_route(
            'prompts',
            [
                'repository' => $repository->id
            ]
        );
    }

    public function purchaseRepository(
        ?Repository $repository = null
    ) {
        return Auth::user()->newSubscription(
            config('cashier.default_subscription.product'),
            config('cashier.default_subscription.pricing'),
        )->checkout(
            [
                'success_url' => route('prompts', ['repository' => $repository]),
                'cancel_url' => route('prompts', ['repository' => $repository]),
                'metadata' => ['repositoryId' => $repository->id],
            ]
        );
    }
}
