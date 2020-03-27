<?php

use Codeception\DomainRule;

class CustomAssertionTest extends \PHPUnit\Framework\TestCase
{
    public function testSuccessfulAssertionCanBeExecuted()
    {
        $assertions = \PHPUnit\Framework\Assert::getCount();
        $this->assertUserIsValid(new User('user'));
        $this->assertUserIsValid(new User('admin'));
        $this->assertEquals($assertions + 2, \PHPUnit\Framework\Assert::getCount());
    }

    public function testFailedAssertion()
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        if (method_exists($this, 'expectExceptionMessageRegExp')) {
            // PHPUnit 7 until 8.3
            $this->expectExceptionMessageRegExp("~Failed asserting that `user and user.isValid()`~");
            $this->expectExceptionMessageRegExp("~\[user\]: User Object~");
        } else {
            // PHPUnit 8.4+
            $this->expectExceptionMessageMatches("~Failed asserting that `user and user.isValid()`~");
            $this->expectExceptionMessageMatches("~\[user\]: User Object~");
        }
        $this->assertUserIsValid(new User('guest'));
    }

    public function testFailedAssertionWithNonUser()
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->expectExceptionMessage(
"Failed asserting that `user and user.isValid()`.
[user]: null"
        );
        $this->assertUserIsValid(null);
    }

    public function testUserHasNoAccess()
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->assertUserDoesntHavePermissions(new User('user'));

    }

    public function testErrorDescriptions()
    {
        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        if (method_exists($this, 'expectExceptionMessageRegExp')) {
            // PHPUnit 7 until 8.3
            $this->expectExceptionMessageRegExp("~Failed asserting that `product.stockAmount > num`~");
            $this->expectExceptionMessageRegExp("~\[product\]: Product Object~");
            $this->expectExceptionMessageRegExp("~\[num\]: 5~");
        } else {
            $this->expectExceptionMessageMatches("~Failed asserting that `product.stockAmount > num`~");
            $this->expectExceptionMessageMatches("~\[product\]: Product Object~");
            $this->expectExceptionMessageMatches("~\[num\]: 5~");
        }

        $product = new Product();
        $product->name = 'iphone';
        $product->stockAmount = 3;
        $this->assertProductsEnoughInStock($product, 5);

    }

    protected function assertUserIsValid($user)
    {
        $this->assertThat(['user' => $user], new DomainRule('user and user.isValid()'));
    }

    protected function assertUserDoesntHavePermissions($user)
    {
        $this->assertThat(
            ['user' => $user],
            new DomainRule('user.isGuest() or user.isBanned()')
        );
    }

    protected function assertProductsEnoughInStock($product, $amount)
    {
        $this->assertThat(
            ['product' => $product, 'num' => $amount],
            new DomainRule('product.stockAmount > num')
        );
    }

}

class User {

    protected $role = 'guest';

    public function __construct($role = null)
    {
        if ($role) {
            $this->role = $role;
        }
    }

    public function isGuest()
    {
        return $this->role === 'guest';
    }

    public function isBanned()
    {
        return $this->role === 'banned';
    }

    public function isValid()
    {
        return $this->role !== 'guest';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}

class Product {

    public $name;
    public $stockAmount = 0;

    public function __toString()
    {
        return $this->name;
    }
}