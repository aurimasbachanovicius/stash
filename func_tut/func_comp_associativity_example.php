<?php

readonly class User
{
    public function __construct(public string $name, public bool $isActive,)
    {
    }
}

readonly class UserResponse
{
    public function __construct(public string $name)
    {
    }
}

$users = [new User("test", 0), new User("test2", 1), new User("test3", 0)];

$filterActive = fn(array $users) => array_filter($users, fn(User $user) => $user->isActive);

# non associative
$simplifyUser = fn(array $users) => array_map(fn(User $user) => new UserResponse($user->name), $users);

# associative
$simplifyUser = fn(array $users) => array_map(fn(User $user) => new User($user->name, $user->isActive), $users);

$sortUsers = fn(array $users) => usort($users, fn(User $a, User $b) => $a->name <=> $b->name);

$compose = fn(callable $a, callable $b): callable => fn($x) => $a($b($x));

$processUsers = $compose($compose($filterActive, $simplifyUser), $sortUsers);
$processedUsers = $processUsers($users);

// Output the result
print_r($processedUsers);

# Enhances Composability
# Simplifies Reasoning, do not care of order.
# Facilitates Algebraic Manipulations (a + b = b + a) or (2(x+3) = 2x + 6).
#   It Can change the composition which doesn't change the outcome.
# Reasoning About Code:
#   Just like algebraic structures help in understanding mathematical problems
#   and solving them step by step, algebraic manipulations in functional programming
#   help developers understand and reason about code. You can break down complex operations
#   into simpler, more manageable parts, and understand how data transforms as it flows through
#   these parts.


# $compose = fn(callable $a, callable $b): callable => fn($x) => $a($b($x));
$processData = $compose(
    $compose(
        $compose('filterActiveUsers', 'prepareReport'),
        'sortData'
    ),
    $compose(
        $compose('groupByAttribute', 'aggregateData'),
        $compose('applyMathOperations', 'convertFormat')
    )
);

$processData = $compose(
    $compose(
        fn($data) => 'prepareReport',
        $compose(
            fn($data) => 'sortData',
            fn($data) => 'sortDataByDate',
        ),
    ),
    fn($data) => 'filterActiveUsers',
);
