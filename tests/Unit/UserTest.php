<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        
        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }

    public function testRoles(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
