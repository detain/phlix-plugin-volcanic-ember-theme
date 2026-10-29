<?php

declare(strict_types=1);

namespace Phlix\VolcanicEmber\Tests;

use Phlix\VolcanicEmber\VolcanicEmberPlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Contrast contract law for the shipped token palette.
 *
 * The token contract (phlix-tokens src/css/colors.css, consumed by the shipped
 * SPA through phlix-ui applyThemeTokens) assigns each role a WCAG floor,
 * measured on the grounds it is actually painted on:
 *
 *  - `--accent-text` is the accent color AS foreground text/icon — it must
 *    reach AA (>= 4.5:1) on every background and surface. The ~20 ui sites
 *    consuming it (`color: var(--accent-text)`) sit on the elevation stack.
 *    The ink-on-fill role is `--text-on-accent`, pinned against `--accent`.
 *  - `--accent-ring` is the sole desktop focus cue (the ui paints it as
 *    `box-shadow: 0 0 0 Npx var(--accent-ring)` after `outline: none`).
 *    Every ground it can be painted on, composited, must reach the 3:1
 *    WCAG 1.4.11 non-text minimum against every ground it can sit adjacent
 *    to — the full adjacency matrix, not just the same-ground state change.
 *  - `--text-subtle` carries form placeholders and hints (ui Input.vue
 *    `::placeholder`, Combobox, CommandPalette, labels/metadata) — body-text
 *    tier, so it must reach AA (>= 4.5:1) on every ground too.
 *
 * Values are parsed from providedThemes() so the law follows the palette;
 * it is not a duplicated constant table. The WCAG 1.4.3 math is recomputed
 * here from the hexes themselves — no external fixture to drift.
 *
 * Ported from the family reference phlix-plugin-arctic-jade-theme (reviewed
 * SHIP): each law landed with its own cure commit, so every commit kept the
 * suite green.
 */
#[CoversClass(VolcanicEmberPlugin::class)]
final class TokenContrastTest extends TestCase
{
    /** WCAG 1.4.3 AA floor for normal-size text. */
    private const AA_TEXT_RATIO = 4.5;

    /** WCAG 1.4.11 floor for non-text UI components (the focus ring). */
    private const NON_TEXT_RATIO = 3.0;

    private VolcanicEmberPlugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->plugin = new VolcanicEmberPlugin();
    }

    /**
     * @return array<string, string>
     */
    private function tokens(): array
    {
        $themes = $this->plugin->providedThemes();
        /** @var array<string, string> $tokens */
        $tokens = $themes[0]['tokens'];

        return $tokens;
    }

    /**
     * @return array<string, string>
     */
    private function grounds(): array
    {
        $tokens = $this->tokens();
        $grounds = [];
        foreach (['--bg', '--surface', '--surface-2', '--surface-3'] as $key) {
            $grounds[$key] = $tokens[$key];
        }

        return $grounds;
    }

    #[Test]
    public function testAccentTextMeetsAaAsForegroundOnEveryGround(): void
    {
        $ink = $this->tokens()['--accent-text'];

        foreach ($this->grounds() as $name => $ground) {
            $ratio = $this->contrastRatio($ink, $ground);
            $this->assertGreaterThanOrEqual(
                self::AA_TEXT_RATIO,
                $ratio,
                sprintf(
                    '--accent-text %s on %s (%s) is %.2f:1 — below the 4.5:1 AA foreground floor',
                    $ink,
                    $name,
                    $ground,
                    $ratio
                )
            );
        }
    }

    #[Test]
    public function testTextOnAccentMeetsAaAsInkOnTheAccentFill(): void
    {
        $tokens = $this->tokens();
        $ratio = $this->contrastRatio($tokens['--text-on-accent'], $tokens['--accent']);

        $this->assertGreaterThanOrEqual(
            self::AA_TEXT_RATIO,
            $ratio,
            sprintf(
                '--text-on-accent %s on --accent %s is %.2f:1 — below the 4.5:1 AA floor',
                $tokens['--text-on-accent'],
                $tokens['--accent'],
                $ratio
            )
        );
    }

    #[Test]
    public function testTextSubtleMeetsAaAsBodyTierOnEveryGround(): void
    {
        $ink = $this->tokens()['--text-subtle'];

        foreach ($this->grounds() as $name => $ground) {
            $ratio = $this->contrastRatio($ink, $ground);
            $this->assertGreaterThanOrEqual(
                self::AA_TEXT_RATIO,
                $ratio,
                sprintf(
                    '--text-subtle %s on %s (%s) is %.2f:1 — below the 4.5:1 AA floor',
                    $ink,
                    $name,
                    $ground,
                    $ratio
                )
            );
        }
    }

    #[Test]
    public function testAccentRingMeetsNonTextMinimumUnderEveryAdjacency(): void
    {
        $ring = $this->parseRgba($this->tokens()['--accent-ring']);

        $grounds = [];
        foreach ($this->grounds() as $name => $hex) {
            $grounds[$name] = $this->parseHex($hex);
        }

        foreach ($grounds as $onName => $paintedOn) {
            $painted = $this->compositeOver($ring, $paintedOn);
            foreach ($grounds as $adjName => $adjacent) {
                $ratio = $this->contrastRatioRgb($painted, $adjacent);
                $this->assertGreaterThanOrEqual(
                    self::NON_TEXT_RATIO,
                    $ratio,
                    sprintf(
                        '--accent-ring painted on %s is %s vs adjacent %s: %.2f:1 — below the 3:1 non-text minimum',
                        $onName,
                        $this->toHex($painted),
                        $adjName,
                        $ratio
                    )
                );
            }
        }
    }

    /**
     * WCAG 2.x relative luminance of a #rrggbb hex.
     *
     * @param array{int, int, int} $rgb
     */
    private function relativeLuminance(array $rgb): float
    {
        $channels = [];
        foreach ($rgb as $channel) {
            $srgb = $channel / 255;
            $channels[] = $srgb <= 0.04045
                ? $srgb / 12.92
                : ((($srgb + 0.055) / 1.055) ** 2.4);
        }

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function contrastRatio(string $hexA, string $hexB): float
    {
        return $this->contrastRatioRgb($this->parseHex($hexA), $this->parseHex($hexB));
    }

    /**
     * @param array{int, int, int} $rgbA
     * @param array{int, int, int} $rgbB
     */
    private function contrastRatioRgb(array $rgbA, array $rgbB): float
    {
        $luminanceA = $this->relativeLuminance($rgbA);
        $luminanceB = $this->relativeLuminance($rgbB);
        $brighter = max($luminanceA, $luminanceB);
        $darker = min($luminanceA, $luminanceB);

        return ($brighter + 0.05) / ($darker + 0.05);
    }

    /**
     * @return array{int, int, int}
     */
    private function parseHex(string $hex): array
    {
        $matches = [];
        if (preg_match('/^#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/', $hex, $matches) !== 1) {
            throw new RuntimeException("Contrast law expects literal #rrggbb, got '$hex'");
        }

        return [
            (int) hexdec($matches[1]),
            (int) hexdec($matches[2]),
            (int) hexdec($matches[3]),
        ];
    }

    /**
     * @param array{int, int, int, float} $rgba
     * @param array{int, int, int} $ground
     *
     * @return array{int, int, int}
     */
    private function compositeOver(array $rgba, array $ground): array
    {
        [$r, $g, $b, $alpha] = $rgba;

        return [
            (int) round($alpha * $r + (1 - $alpha) * $ground[0]),
            (int) round($alpha * $g + (1 - $alpha) * $ground[1]),
            (int) round($alpha * $b + (1 - $alpha) * $ground[2]),
        ];
    }

    /**
     * @return array{int, int, int, float}
     */
    private function parseRgba(string $value): array
    {
        $matches = [];
        if (
            preg_match('/^rgba\((\d{1,3}), (\d{1,3}), (\d{1,3}), (0?\.\d+|1|0)\)$/', $value, $matches)
                !== 1
        ) {
            throw new RuntimeException("Contrast law expects literal rgba(r, g, b, a), got '$value'");
        }

        return [
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3],
            (float) $matches[4],
        ];
    }

    /**
     * @param array{int, int, int} $rgb
     */
    private function toHex(array $rgb): string
    {
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }
}
