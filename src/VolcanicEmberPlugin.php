<?php

/**
 * Phlix Volcanic Ember theme plugin.
 *
 * @copyright 2026 Joe Huss <detain@interserver.net>
 * @license   MIT
 */

declare(strict_types=1);

namespace Phlix\VolcanicEmber;

use Phlix\Shared\Plugin\LifecycleInterface;
use Phlix\Theming\ThemeSourceInterface;
use Psr\Container\ContainerInterface;

/**
 * Volcanic Ember theme plugin for Phlix.
 *
 * A warm, fiery dark theme built on the midnight base with ember orange accents
 * evoking volcanic lava and glowing coals. Deep blacks and browns create
 * atmosphere while the bright ember accent provides visual hierarchy.
 *
 * @package Phlix\VolcanicEmber
 * @since 1.0.0
 */
final class VolcanicEmberPlugin implements LifecycleInterface, ThemeSourceInterface
{
    /**
     * Canonical provenance key for this source.
     */
    public const SOURCE_NAME = 'volcanic-ember';

    /**
     * Nothing to do — the host registers the themes off the `instanceof`.
     *
     * @param ContainerInterface $container The host container (unused).
     */
    public function onEnable(ContainerInterface $container): void
    {
    }

    /**
     * Nothing to do — the host deregisters this source by name on disable.
     */
    public function onDisable(): void
    {
    }

    /**
     * A theme plugin subscribes to no events.
     *
     * @return array<class-string, string> Always empty.
     */
    public function subscribedEvents(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function themeSourceName(): string
    {
        return self::SOURCE_NAME;
    }

    /**
     * @inheritDoc
     *
     * @return list<array<array-key, mixed>>
     */
    public function providedThemes(): array
    {
        return [
            [
                'id' => 'volcanic-ember',
                'name' => 'Volcanic Ember',
                'dark' => true,
                'extends' => 'midnight',
                'tokens' => [
                    // Accent ramp — ember orange tones
                    '--accent' => '#ff6b4a',
                    '--accent-hover' => '#ff8a70',
                    '--accent-active' => '#e54f2e',
                    '--accent-soft' => 'rgba(255, 107, 74, 0.14)',
                    // The ring is the sole desktop focus cue (box-shadow after
                    // outline: none) and must clear WCAG 1.4.11's 3:1 non-text
                    // floor across the full adjacency matrix of this theme's
                    // grounds (pinned in tests/TokenContrastTest.php). The
                    // review suggested >= 0.60, but ember red measures 2.54
                    // worst-adjacency at 0.60 — a dark red on dark volcanic
                    // browns never reaches 3:1 that low. 0.70 measures 3.16
                    // worst-adjacency / 3.54 same-ground.
                    '--accent-ring' => 'rgba(255, 107, 74, 0.70)',
                    // `--accent-text` is the accent color AS FOREGROUND text/
                    // icon (phlix-tokens contract; ~20 ui sites paint it as
                    // `color: var(--accent-text)` on the dark elevation stack).
                    // A near-black ink here — #0a0605 — measured 1.01:1 on
                    // --bg, invisible on every ground of this dark theme and
                    // far below the 4.5:1 AA floor. Ember orange as foreground
                    // measures 5.74:1 worst-ground; the law is pinned in
                    // tests/TokenContrastTest.php. The ink-on-fill role is
                    // `--text-on-accent` below, which stays near-black.
                    '--accent-text' => '#ff6b4a',

                    // Background + elevation stack — deep volcanic blacks and browns
                    '--bg' => '#0d0705',
                    '--surface' => '#1a0d0a',
                    '--surface-2' => '#261310',
                    '--surface-3' => '#331a14',
                    '--surface-glass' => 'rgba(30, 12, 10, 0.62)',
                    '--surface-glass-strong' => 'rgba(15, 5, 3, 0.82)',

                    // Text ramp — warm off-whites
                    '--text' => '#f0e6e4',
                    '--text-muted' => '#c4a8a4',
                    '--text-subtle' => '#8a7270',
                    '--text-faint' => '#5a4a48',
                    '--text-on-accent' => '#0a0605',

                    // Borders — subtle warm browns
                    '--border' => '#2a1814',
                    '--border-subtle' => '#1a0e0c',
                    '--border-strong' => '#3a2220',

                    // Atmosphere
                    '--grain-opacity' => '0.035',
                    '--vignette' => 'rgba(0, 0, 0, 0.5)',
                    '--ambient' => 'rgba(255, 107, 74, 0.12)',

                    // Legacy aliases for SPA compatibility
                    '--color-bg' => '#0d0705',
                    '--color-surface' => '#1a0d0a',
                    '--color-text' => '#f0e6e4',
                    '--color-text-muted' => '#c4a8a4',
                    '--color-border' => '#2a1814',
                ],
            ],
        ];
    }
}
