<?php

namespace App\Providers;

use App\Enum\PromptRepositoryType;
use App\Enum\SubscriptionStatus;
use App\Models\AiApiKey;
use App\Models\AiConnector;
use App\Models\Repository;
use App\Models\User;
use App\PromptRepository\GitHubPromptRepository;
use App\PromptRepository\TempPromptRepository;
use Gptsdk\AI\AnthropicAIVendor;
use Gptsdk\AI\OpenAIVendor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(User::class);


        Route::bind('repository', function (string $value) {
            $user = auth()->user();

            return Repository::where('user_id', $user->id)->where('id', $value)->firstOrFail();
        });

        Route::bind('aiApiKey', function (string $value) {
            $user = auth()->user();

            return AiApiKey::where('user_id', $user->id)->where('id', $value)->firstOrFail();
        });

        Route::bind('aiConnector', function (string $value) {
            $user = auth()->user();

            return AiConnector::where('user_id', $user->id)->where('id', $value)->firstOrFail();
        });

        Route::bind('paidRepository', function (string $value) {
            $user = auth()->user();

            return Repository::where('user_id', $user->id)
                ->where('subscription_status', SubscriptionStatus::PAID)
                ->where('id', $value)
                ->firstOrFail();
        });

        $this->app->singleton(
            HttpClientInterface::class,
            function() {
                return HttpClient::create();
            }
        );

        $this->app->singleton(
            OpenAIVendor::class,
            function (Application $application) {
                return new OpenAIVendor($application->get(HttpClientInterface::class));
            }
        );

        $this->app->singleton(
            AnthropicAIVendor::class,
            function (Application $application) {
                return new AnthropicAIVendor($application->get(HttpClientInterface::class));
            }
        );

        $this->app->singleton(
            PromptRepositoryProvider::class,
            function (Application $application) {
                return new PromptRepositoryProvider(
                    [
                        PromptRepositoryType::GITHUB->value => new GitHubPromptRepository(),
                        PromptRepositoryType::TEMP->value => new TempPromptRepository()
                    ]
                );
            }
        );
    }
}
