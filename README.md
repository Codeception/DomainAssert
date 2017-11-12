# Domain Assertions

Assertion library for [PHPUnit][1] or [Codeception][2] powered by [Symfony Expression Language][3].

## Pitch

This tiny library helps you to create domain-specific assertions in tests:

Instead of:

```php
$this->assertTrue($user->isValid(), 'user is valid');
```
use

```php
$this->assertUserIsValid($user);
```

### Why? 

See how test fails in first example:

```bash
user is valid
Failed asserting that false is true.
```

And how test fails in second example:

```bash
Failed asserting that `user.isValid()`.
[user]: User Object &000000005689696e000000004066036e (
    'role' => 'guest'
)
```

What makes more sense to you? In second example you get the business logic behind the assertion as well as values passed into it.
That's why if you have business logic in your project `domain-assert` is your choice.
 
## How To Use It

Install this package:

```bash
composer require codeception/domain-assert --dev
```
Create a **trait** with a custom assertion. *We recommend using traits as you can reuse them accross different test cases.*
 
```php
use Codeception\DomainRule;

trait CustomAssertion
{
    public function assertValidUser(User $user)
    {
        $this->assertThat(
            ['user' => $user], 
            new DomainRule('user and user.isValid()')
        );
    }
}
```

*In this example we check that `$user` exists and `$user->isValid()` return true;* 

That's all! Now inject this trait to TestCases and use it.

```php
class UserTest extends \PHPUnit\Framework\TestCase
{
    use CustomAssertion;
}

```

## Defining Business Rules

This library uses [Expression Language][3] to define domain rules for assertions. 

Let's define a rule to check if we have enough products in the stock:

```
stock and product.getStock() == stock and product.getAmount() > amount
```

We have 3 items here: `product`, `stock`, and `amount` which is a number of items we need.
Let's create assertion according to this rule:

```php
public function assertEnoughProductsInStock(Stock stock, Product product, amount)
{
    $this->assertThat(
        ['product' => $product], 
        ['stock' => $stock], 
        ['amount' => $amount], 
        new DomainRule('stock and product.getStock() == stock and product.getAmount() > amount')
    );
}
```

Now it can be used inside your tests:

```php
$product = new Product('iPhone');
$stock->addProduct($product);
$stock->addProduct($product);
$stock->addProduct($product);
$this->assertEnoughProductsInStock($stock, $product, 2);
```

## Advanced Concepts

* `Codeception\DomainRule` extends `PHPUnit\Framework\Constraint`.
* `Codeception\DomainRule` uses `Symfony\Component\ExpressionLanguage\ExpressionLanguage`
* [Expression Language can be extended](https://symfony.com/doc/current/components/expression_language/extending.html) by calling `$domainRule->getLanguage()` 
* `assertThat` can receive first parameter as scalar value. In this case it will be treated as `expected` inside an expression:

```php
public function assertIsGreaterThanMinimal()
{
    $this->assertThat(
        $minimalPrice,
        new DomainRule('expected > 1000')
    );    
}
```

## License

Verify is open-sourced software licensed under the [MIT][4] License. © Codeception PHP Testing Framework

[1]: https://phpunit.de/
[2]: http://codeception.com/
[3]: https://symfony.com/doc/current/components/expression_language.html
[4]: https://github.com/Codeception/DomainAssert/blob/master/LICENSE
