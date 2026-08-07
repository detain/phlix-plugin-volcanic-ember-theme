<?php

/**
 * Dev-only stub of the host server's theme source contract.
 *
 * @copyright 2026 Joe Huss <detain@interserver.net>
 * @license   MIT
 */

declare(strict_types=1);

namespace Phlix\Theming;

/**
 * Dev-only stub of `Phlix\Theming\ThemeSourceInterface`.
 *
 * The canonical definition ships in `detain/phlix-shared`
 * (`src/Theming/ThemeSourceInterface.php`) and is resolved by the host
 * application's autoloader in an installed plugin. This copy exists only for
 * the case where the plugin is checked out and analysed without that package
 * present; `tests/bootstrap.php` loads it only when the real interface is
 * absent.
 *
 * @internal Tests/analysis only — never autoloaded into production.
 */
interface ThemeSourceInterface
{
    /**
     * Returns the canonical provenance key for this source.
     */
    public function themeSourceName(): string;

    /**
     * Returns the list of themes provided by this source.
     *
     * @return list<array<array-key, mixed>>
     */
    public function providedThemes(): array;
}
