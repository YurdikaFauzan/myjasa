<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Models\User;

class UserTest extends TestCase
{
    public function testGetAvailableRolesReturnsAllRoles(): void
    {
        $roles = User::getAvailableRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('admin', $roles);
        $this->assertContains('customer', $roles);
        $this->assertContains('technician', $roles);
    }

    public function testRoleConstantsMatchValues(): void
    {
        $this->assertEquals('admin', User::ROLE_ADMIN);
        $this->assertEquals('customer', User::ROLE_CUSTOMER);
        $this->assertEquals('technician', User::ROLE_TECHNICIAN);
    }

    public function testGetRoleCardsContainsExpectedMetadata(): void
    {
        $cards = User::getRoleCards();

        $this->assertCount(3, $cards);
        $roleIds = array_column($cards, 'id');
        $this->assertContains(User::ROLE_CUSTOMER, $roleIds);
        $this->assertContains(User::ROLE_TECHNICIAN, $roleIds);
        $this->assertContains(User::ROLE_ADMIN, $roleIds);

        foreach ($cards as $card) {
            $this->assertArrayHasKey('id', $card);
            $this->assertArrayHasKey('name', $card);
            $this->assertArrayHasKey('badge', $card);
            $this->assertArrayHasKey('icon', $card);
            $this->assertArrayHasKey('description', $card);
        }
    }
}

