<?php
# https://en.wikipedia.org/wiki/Function_composition

# Associativity of function composition
# ((f ∘ g) ∘ h = f ∘ (g ∘ h))
# f( g( h(x) ) ) = f (g (h (x)))

/**
 * Functions:
 * f(x)=x+2
 * g(x)=3x
 * h(x)=x+5
 *
 * Compute f∘(g∘h)
 *
 * (g∘h)(x) = g(h(x)) = 3(x+5) = 3x + 15
 * (f∘(g∘h))(x) = (f(g∘h))(x) = (3x + 15) + 2 = 3x + 17
 *
 * Compute (f∘g)∘h
 * (f∘g)(x) = f(g(x)) = 3x + 2
 * ((f∘g)∘h)(x) = (f∘g)(h(x)) = (3(x+5)+2) = 3x + 17
 */

# Functions as morphisms
$f = (fn(int $x) => $x * 2);
$g = (fn(int $x) => $x + 3);
$h = (fn(int $x) => $x - 5);

# Function as Composition
$compose = fn(callable $f, callable $g) => fn($x) => $f($g($x));

// Composing the functions
$f_g = $compose($f, $g); // Represents f ∘ g
$g_h = $compose($g, $h); // Represents g ∘ h

// Associativity: (f ∘ g) ∘ h should be the same as f ∘ (g ∘ h)
$result1 = $compose($f_g, $h);
$result2 = $compose($f, $g_h);

// Test value
$testValue = 10;

echo "(f ∘ g) ∘ h for x = $testValue: " . $result1($testValue) . "\n";
echo "f ∘ (g ∘ h) for x = $testValue: " . $result2($testValue) . "\n";
