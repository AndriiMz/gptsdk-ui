<?php

namespace Feature;

use Tests\Helper\WithRepository;
use Tests\Helper\WithAuthorizedUser;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    use WithAuthorizedUser, WithRepository;

    public function testCreate()
    {
        $this->withRepository();
        $this->assertEquals('moroz', $this->repository->owner);
        $this->assertEquals('gptsdk', $this->repository->name);
    }
}
