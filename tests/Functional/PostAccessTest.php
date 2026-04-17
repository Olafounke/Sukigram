<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostAccessTest extends WebTestCase
{
    public function testPostCreationRedirectsToLoginWhenNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/post/new');

        $this->assertResponseRedirects('/login');
    }

    public function testAuthenticatedUserCanAccessPostForm(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('suki@test.com');

        $client->loginUser($testUser);
        $client->request('GET', '/post/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }
}
