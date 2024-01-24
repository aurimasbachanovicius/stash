# Immutability

* Predictability (constant throughout its lifetime, easier to debug)
* Concurrency and Parallelism (race conditions/data corruption)
* Thread Safety (thread-safe)
* Statelessness (modular architecture)
* Caching and Memoization
* Copy-on-Write Optimization
* Testing (unit testing is straightforward)
* Functional Composition [Link](func_comp_math.php)

```php
class ImmutablePerson
{
    private readonly string $name;

    public function __construct()
    {
        $this->name = 'John';
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

# Pure functions

```php
# Not Pure:
function setConfigValue(array &$config, string $key, string $value): array
{
    $config[$key] = $value;
    return $config;
}

$appConfig = ["debug" => false, "max_items" => 100];
$appConfigNew = setConfigValue($appConfig, "debug", true);
var_dump($appConfig["debug"]); // true or false?
var_dump($appConfigNew["debug"]); // true or false?

# Pure:
function setConfigValue(array $config, string $key, string $value): array
{
    $newConfig = $config;
    $newConfig[$key] = $value;
    return $newConfig;
}

$appConfig = ["debug" => false, "max_items" => 100];
$appConfigNew = setConfigValue($appConfig, "debug", true);
var_dump($appConfig["debug"]); // true or false?
var_dump($appConfigNew["debug"]); // true or false?
```

```php
# Not Pure:
class Config
{
    public bool $debug = false;
    public int $maxItems = 100;
}

function setConfigValue(Config $config, string $key, string $value): Config
{
    $config->$key = $value;
    return $config;
}

$appConfig = new Config();
$appConfigNew = setConfigValue($appConfig, "debug", true);
var_dump($appConfig->debug); // true or false?
var_dump($appConfigNew->debug); // true or false?

# Pure:
class Config
{
    public bool $debug = false;
    public int $maxItems = 100;
}

function setConfigValue(Config $config, string $key, string $value): Config
{
    $newConfig = clone $config;
    $newConfig->$key = $value;
    return $newConfig;
}

$appConfig = new Config();
$appConfigNew = setConfigValue($appConfig, "debug", true);
var_dump($appConfig->debug); // true or false?
var_dump($appConfigNew->debug); // true or false?
```

# First-Class and Higher-Order Functions:

```php
$operate = fn(int $a, int $b, callable $operation): int => $operation($a, $b);

$addition = fn(int $x, int $y): int => $x + $y;
$subtraction = fn(int $x, int $y): int => $x - $y;

$result = $operate(3, 4, $addition);
$result = $operate(3, 4, $subtraction);

// simplified strategy pattern?
```

# Referential Transparency

```php
function double(int $x) {
    return $x * 2;
}

$result1 = double(5); // 10
$result2 = double(5); // 10
// ... doesn't matter how many times i'll cal double(x), it will always give the same result.
```

# Recursion and Memoization:

```php
function factorial($n) {
    if ($n <= 1) {
        return 1;
    }
    return $n * factorial($n - 1);
}

echo factorial(5); // 120

function fibonacci($n, &$memo = array()) {
    if ($n <= 1) {
        return $n;
    }

    if (!isset($memo[$n])) {
        $memo[$n] = fibonacci($n - 1, $memo) + fibonacci($n - 2, $memo);
    }
    return $memo[$n];
}

echo fibonacci(10); // Output: 55

```

# Currying (arity):

```php
$add = fn(int $x): callable => fn(int $y): int => $x + $y;
```

```php
function convertCurrency(float $exchangeRate) {
    return function (int $amount) use ($exchangeRate) {
        return $amount * $exchangeRate;
    };
}

// Define exchange rates
$usdToEur = convertCurrency(0.85);
$usdToGbp = convertCurrency(0.75);

// Convert amounts
$amountInUsd = 100;

$amountInEur = $usdToEur($amountInUsd);
$amountInGbp = $usdToGbp($amountInUsd);

echo "100 USD is equal to {$amountInEur} EUR\n";
echo "100 USD is equal to {$amountInGbp} GBP\n";
```

```php
// Define a function to calculate the total cost for a single item
function calculateItemCost($itemPrice) {
    return function ($quantity) use ($itemPrice) {
        return function ($taxRate) use ($itemPrice, $quantity) {
            return function ($discounts) use ($itemPrice, $quantity, $taxRate) {
                $discountAmount = array_reduce($discounts, function ($carry, $discount) {
                    return $carry + $discount;
                }, 0);

                return ($itemPrice * $quantity) + ($subtotal * $taxRate) - $discountAmount;
            };
        };
    };
}

// Define item-specific functions with currying
$calculateItemA = calculateItemCost(50); // Item A costs $50
$calculateItemB = calculateItemCost(30); // Item B costs $30

$taxedCalculatedItemA = $calculateItemA(2)(0.1);

function applyDiscountFromUrlCode(string $urlCode, callable $calculateItemCost) {
    $discounts = [];

    if ($urlCode === 'DISCOUNT_5') {
        $discounts[] = 5;
    }

    if ($urlCode === 'DISCOUNT_10') {
        $discounts[] = 10;
    }

    return $calculateItemCost($discounts);
}

applyDiscountFromUrlCode('DISCOUNT_5', $taxedCalculatedItemA);

// Calculate the total cost for different scenarios
$totalCostForItemA = $calculateItemA(2)(0.1)([5, 10]); // 2 items, 10% tax, $5 and $10 discounts
$totalCostForItemB = $calculateItemB(3)(0.08)([2, 3]); // 3 items, 8% tax, $2 and $3 discounts

echo "Total cost for Item A: $totalCostForItemA\n";
echo "Total cost for Item B: $totalCostForItemB\n";
```