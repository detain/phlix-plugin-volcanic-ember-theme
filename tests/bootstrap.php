<?php

/**
 * PHPUnit bootstrap file.
 *
 * @copyright 2026 Joe Huss <detain@interserver.net>
 * @license   MIT
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Load dev-stubbed interfaces when the real ones are absent from the host.
if (!interface_exists(\Phlix\Shared\Plugin\LifecycleInterface::class)) {
    require_once __DIR__ . '/../dev-stubs/LifecycleInterface.php';
}

if (!interface_exists(\Phlix\Theming\ThemeSourceInterface::class)) {
    require_once __DIR__ . '/../dev-stubs/ThemeSourceInterface.php';
}
