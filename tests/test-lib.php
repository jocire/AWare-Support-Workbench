<?php
function aware_test_fail(string $message): never { fwrite(STDERR, "FAIL: {$message}\n"); exit(1); }
function aware_test_assert(bool $condition, string $message): void { if (!$condition) aware_test_fail($message); }
function aware_test_same($expected, $actual, string $message): void { if ($expected !== $actual) { aware_test_fail($message . '\nExpected: ' . var_export($expected, true) . '\nActual: ' . var_export($actual, true)); } }
function aware_test_contains(string $needle, string $haystack, string $message): void { aware_test_assert(strpos($haystack, $needle) !== false, $message . " (missing {$needle})"); }
function aware_test_not_contains(string $needle, string $haystack, string $message): void { aware_test_assert(strpos($haystack, $needle) === false, $message . " (unexpected {$needle})"); }
