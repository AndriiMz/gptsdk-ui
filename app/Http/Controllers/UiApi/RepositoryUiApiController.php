<?php

namespace App\Http\Controllers\UiApi;

use App\Data\RepositoryData;
use App\Enum\SubscriptionStatus;
use App\Models\Repository;
use App\Providers\PromptRepositoryProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Subscription;

class RepositoryUiApiController
{
    public function delete(Repository $repository)
    {
        if (
            $repository->subscription_status === SubscriptionStatus::PAID &&
            !empty($repository->subscription_id)
        ) {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $subscription = Subscription::retrieve($repository->subscription_id);
            $subscription->cancel();
        }

        $repository->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    public function getFiles(
        PromptRepositoryProvider $promptRepositoryProvider,
        Repository $repository,
        string $path = ''
    ) {
        $promptRepository = $promptRepositoryProvider->getPromptRepository($repository->type->value);

        return new JsonResponse([
            'files' => $promptRepository->getList(
                $repository->token,
                $repository->owner,
                $repository->name,
                $path
            )
        ]);
    }
}
