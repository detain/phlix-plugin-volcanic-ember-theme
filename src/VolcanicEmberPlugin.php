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
                    '--accent-ring' => 'rgba(255, 107, 74, 0.50)',
                    '--accent-text' => '#0a0605',

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
