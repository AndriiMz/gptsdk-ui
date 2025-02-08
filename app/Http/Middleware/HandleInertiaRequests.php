<?php

namespace App\Http\Middleware;

use App\Data\RepositoryData;
use App\Models\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        $userArr = [];
        $repositories = [];
        if ($user !== null) {
            $userArr = [
                'gravatar' => md5(strtolower(trim($user->email))),
                'name' => $user->name,
                'email' => $user->email
            ];

            $repositories = Repository::where('user_id', $user->id)->get();
        }

        return array_merge(parent::share($request), [
            'repositories' => RepositoryData::collect($repositories),
            'csrf_token' => csrf_token(),
            'user' => $userArr,
        ]);
    }
}
