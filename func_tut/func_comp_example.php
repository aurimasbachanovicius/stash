<?php

class Request
{
}

$middlewares = [];

$authMiddleware = fn($r) => 'authenticationMiddleware';

function authenticationMiddleware(Request $request)
{
// Check if the user is authenticated
// Modify $request if needed
// Return the modified $request
}

function loggingMiddleware(Request $request)
{
// Log information about the request
// Modify $request if needed
// Return the modified $request
}

function validationMiddleware(Request $request)
{
// Validate input data from the request
// Modify $request if needed
// Return the modified $request
}

// Function composition for middleware
function composeMiddleware(...$middlewares): Closure
{
    return function ($request) use ($middlewares) {
        foreach ($middlewares as $middleware) {
            $request = $middleware($request);
        }
        return $request;
    };
}

$composedMiddleware = composeMiddleware(
    'authenticationMiddleware',
    'loggingMiddleware',
    'validationMiddleware'
);

// Handling a request
$request = [
    'user' => 'user123',
    'data' => ['key' => 'value']
];

// Apply the composed middleware to the request
$resultingRequest = $composedMiddleware($request);