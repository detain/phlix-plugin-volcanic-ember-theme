<?php

declare(strict_types=1);

namespace Phlix\VolcanicEmber\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Phlix\VolcanicEmber\VolcanicEmberPlugin;

#[CoversClass(VolcanicEmberPlugin::class)]
final class VolcanicEmberPluginTest extends TestCase
{
    private VolcanicEmberPlugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->plugin = new VolcanicEmberPlugin();
    }

    #[Test]
    public function testImplementsLifecycleInterface(): void
    {
        $this->assertInstanceOf(\Phlix\Shared\Plugin\LifecycleInterface::class, $this->plugin);
    }

    #[Test]
    public function testImplementsThemeSourceInterface(): void
    {
        $this->assertInstanceOf(\Phlix\Theming\ThemeSourceInterface::class, $this->plugin);
    }

    #[Test]
    public function testThemeSourceNameReturnsCorrectValue(): void
    {
        $this->assertSame('volcanic-ember', $this->plugin->themeSourceName());
    }

    #[Test]
    public function testProvidedThemesReturnsExpectedStructure(): void
    {
        $themes = $this->plugin->providedThemes();

        // No assertIsArray here: providedThemes() is declared `: array`, so the
        // language itself enforces arrayness and PHPStan level 9 flags the
        // assertion as one that can never fail. Count + shape are the checks.
        $this->assertCount(1, $themes);
        $this->assertNotEmpty($themes, 'Plugin must provide at least one theme');

        $theme = $themes[0];
        $this->assertSame('volcanic-ember', $theme['id']);
        $this->assertSame('Volcanic Ember', $theme['name']);
        $this->assertTrue($theme['dark']);
        $this->assertSame('midnight', $theme['extends']);
        $this->assertArrayHasKey('tokens', $theme);
    }

    #[Test]
    public function testAllTokensAreValidCssValues(): void
    {
        $themes = $this->plugin->providedThemes();
        $theme = $themes[0];
        $tokens = $theme['tokens'];

        // assertIsArray introduces the iterable type for level 9: $theme is
        // `array<mixed>`, so $tokens arrives here as `mixed` and a bare foreach
        // would be an unchecked operation on a possibly non-iterable value.
        $this->assertIsArray($tokens);

        $forbidden = ['var(', 'url(', '/*', ';', '}', '{', '\\', "\n", "\t"];

        foreach ($tokens as $token => $value) {
            // Narrow key and value before use: both are `mixed` out of
            // `array<mixed>`, and the failure messages interpolate the name.
            $this->assertIsString($token, 'Token name must be a string');
            $this->assertIsString($value, "Token {$token} must have a string value");
            $this->assertNotEmpty($value, "Token {$token} must not be empty");

            foreach ($forbidden as $f) {
                $this->assertStringNotContainsString(
                    $f,
                    $value,
                    "Token {$token} contains forbidden CSS construct \"{$f}\""
                );
            }
        }
    }

    #[Test]
    public function testLegacyColorAliasesMirrorTheirCanonicalPrimaries(): void
    {
        // Review theme-volcanic-ember__webux m3: the five --color-* SPA
        // aliases ship as literals, not var() references — the payload's
        // no-var() law (testAllTokensAreValidCssValues above) fixes that
        // choice. The remaining risk is drift: a future token edit that
        // updates `--bg` but forgets `--color-bg`, leaving the SPA reading
        // a stale literal. This pin makes that red in the same commit.
        $themes = $this->plugin->providedThemes();
        $tokens = $themes[0]['tokens'];
        // assertIsArray introduces the iterable type for level 9 (same
        // rationale as in testAllTokensAreValidCssValues).
        $this->assertIsArray($tokens);

        $aliases = [
            '--color-bg' => '--bg',
            '--color-surface' => '--surface',
            '--color-text' => '--text',
            '--color-text-muted' => '--text-muted',
            '--color-border' => '--border',
        ];

        foreach ($aliases as $alias => $canonical) {
            $this->assertArrayHasKey($alias, $tokens, "Legacy alias {$alias} must ship");
            $this->assertArrayHasKey($canonical, $tokens, "Canonical token {$canonical} must ship");

            // Narrow both values before comparing: they arrive as `mixed`
            // out of array<mixed> and the message interpolates them.
            $aliasValue = $tokens[$alias];
            $canonicalValue = $tokens[$canonical];
            $this->assertIsString($aliasValue);
            $this->assertIsString($canonicalValue);
            $this->assertSame(
                $canonicalValue,
                $aliasValue,
                "Legacy alias {$alias} drifted from {$canonical} — the SPA reads "
                . "the alias name, so update both literals together"
            );
        }
    }

    #[Test]
    public function testOnEnableDoesNotThrow(): void
    {
        // Official PHPUnit contract for "running this must not throw": the
        // prior body ended in assertTrue(true), an assertion that can never
        // fail (flagged by PHPStan level 9 on the tests corpus).
        $this->expectNotToPerformAssertions();
        $container = $this->createMock(\Psr\Container\ContainerInterface::class);

        $this->plugin->onEnable($container);
    }

    #[Test]
    public function testOnDisableDoesNotThrow(): void
    {
        // See testOnEnableDoesNotThrow for why this replaces assertTrue(true).
        $this->expectNotToPerformAssertions();

        $this->plugin->onDisable();
    }

    #[Test]
    public function testSubscribedEventsReturnsEmptyArray(): void
    {
        $events = $this->plugin->subscribedEvents();
        // No assertIsArray here: subscribedEvents() is declared `: array`;
        // assertEmpty on the typed return is the meaningful check.
        $this->assertEmpty($events);
    }
}
