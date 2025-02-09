<?php

namespace App\Storage;

use Illuminate\Http\Request;
use Logto\Sdk\Storage\Storage;
use Logto\Sdk\Storage\StorageKey;

class DbLogtoSessionStorage implements Storage
{
    public function __construct(private readonly Request $request)
    {}

    public function get(StorageKey $key): ?string
    {
        return $this->request->session()->get($key->value);
    }

    public function set(StorageKey $key, ?string $value): void
    {
        $this->request->session()->put($key->value, $value);
    }

    public function delete(StorageKey $key): void
    {
        $this->request->session()->remove($key->value);
    }
}
