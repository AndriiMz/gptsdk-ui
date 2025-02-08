<?php

namespace Tests\Helper;

use App\Enum\PromptRepositoryType;
use App\Enum\SubscriptionStatus;
use App\Models\Repository;
use App\PromptRepository\TempPromptRepository;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

trait WithRepository
{
    use WithAuthorizedUser, MakesHttpRequests;

    protected Repository $repository;

    protected function withRepository()
    {
        $this->withAuthorizedUser();
        (new TempPromptRepository())->reset();

        $this->post('/repository', [
            'type' => PromptRepositoryType::TEMP->value,
            'token' => '1234',
            'url' => 'https://github.com/moroz/gptsdk',
        ]);

        $this->repository = Repository::where('user_id', $this->user->id)->first();
    }

    protected function markRepsoitoryAsPaid()
    {
        $this->repository->update(['subscription_status' => SubscriptionStatus::PAID]);
    }

    protected function getRepositoryPrompts()
    {
        $repositoryId = $this->repository->id;
        $response = $this->get("/repository/$repositoryId/prompts/", [
            'Accept' => 'application/json'
        ]);

        return $response->json();
    }
}
