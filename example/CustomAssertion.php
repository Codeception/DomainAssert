<?php
namespace Codeception;

use PHPUnit\Framework\Assert;

trait CustomAssertion
{
    public function assertUserLoggedIn($user)
    {
        Assert::assertThat(['user' => $user], new DomainRule('user and user.isValid()'));
    }

    public function assertUserDoesntHavePermissions($user)
    {
        Assert::assertThat(
            ['user' => $user],
            new DomainRule('user.isGuest() or user.isBanned()')
        );
    }

    public function assertProductAvailable($product)
    {
        Assert::assertThat(
            $product,
            new DomainRule('expected.inStock()')
        );
    }

}