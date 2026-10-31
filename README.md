# Volcanic Ember Theme Plugin

A dark UI theme for Phlix, built on the midnight base. Its warm ember-orange accents over deep volcanic blacks evoke lava and glowing coals.

## Requirements

- PHP 8.3+
- Phlix server 0.44.0 or higher

## Installation

### The supported route: the plugin catalog

The Phlix server installs plugins from a **catalog** (`plugins.json`). A catalog
entry pins an exact commit (`ref`) and the sha256 of that commit's tarball
(`artifactSha256`), and the server verifies those bytes before it extracts
anything. Without a pin, `PluginLoader::install()` refuses outright — that is a
deliberate default-deny, not a bug to work around.

**Admin UI** — *Admin → Plugins* (`/app/admin/plugins`), find **Volcanic Ember
Theme**, press **Install**, then flip its switch to **enable** it.

**CLI** — from the server root, pass the **repository URL**, not the plugin
name (the URL is what gets rewritten to the pinned tarball):

```bash
php bin/phlix plugin:install https://github.com/detain/phlix-plugin-volcanic-ember-theme
php bin/phlix plugin:enable  phlix-plugin-volcanic-ember-theme
```

While iterating on a local fork (no catalog entry yet), build an archive and
install it over `file://`, which is exempt from the pin requirement because the
bytes are already yours:

```bash
tar --exclude=vendor --exclude=.git -czf /tmp/volcanic-ember.tar.gz phlix-plugin-volcanic-ember-theme/
php bin/phlix plugin:install file:///tmp/volcanic-ember.tar.gz
php bin/phlix plugin:enable  phlix-plugin-volcanic-ember-theme
```

### What does **not** work

`composer require detain/phlix-plugin-volcanic-ember-theme` into the server, or
cloning this repository into the plugins directory, does not register anything —
the plugin loader installs catalog-pinned archives or local tarballs, never an
ambient vendor checkout or a bare directory.

### Install, enable, and selection are three different acts

Installing downloads and stages the plugin. **Enabling** it is what registers
the theme: `PluginLoader::enable()` finds the `ThemeSourceInterface` on the
entry class and contributes the `volcanic-ember` theme id. Choosing the theme is
a **per-user preference**, not an admin action — each user picks *Volcanic
Ember* under **Settings → Appearance**. None of the three steps implies the
next.

## Theme Tokens

### Color Palette

| Token | Hex | Usage |
|-------|-----|-------|
| `--accent` | `#ff6b4a` | Primary ember accent |
| `--accent-hover` | `#ff8a70` | Hover state |
| `--accent-active` | `#e54f2e` | Active/pressed state |
| `--accent-soft` | `rgba(255, 107, 74, 0.14)` | Soft accent background |
| `--accent-ring` | `rgba(255, 107, 74, 0.70)` | Focus rings |
| `--accent-text` | `#ff6b4a` | Ember orange AS foreground text/icon — AA (≥4.5:1) on the dark surfaces |

### Background Stack

| Token | Hex | Usage |
|-------|-----|-------|
| `--bg` | `#0d0705` | Page background |
| `--surface` | `#1a0d0a` | Card/panel background |
| `--surface-2` | `#261310` | Elevated surface |
| `--surface-3` | `#331a14` | Highest elevation |
| `--surface-glass` | `rgba(30, 12, 10, 0.62)` | Glass panel background |
| `--surface-glass-strong` | `rgba(15, 5, 3, 0.82)` | Strong glass panel background |

### Text Ramp

| Token | Hex | Usage |
|-------|-----|-------|
| `--text` | `#f0e6e4` | Primary text (warm off-white) |
| `--text-muted` | `#c4a8a4` | Secondary text |
| `--text-subtle` | `#9d8987` | Tertiary text |
| `--text-faint` | `#5a4a48` | Disabled/hint text |
| `--text-on-accent` | `#0a0605` | Dark ink painted ON accent fills (buttons, badges) — distinct role from `--accent-text` |

### Borders

| Token | Hex | Usage |
|-------|-----|-------|
| `--border` | `#2a1814` | Default border |
| `--border-subtle` | `#1a0e0c` | Subtle border |
| `--border-strong` | `#3a2220` | Strong/emphasis border |

### Atmosphere

| Token | Value | Usage |
|-------|-------|-------|
| `--ambient` | `rgba(255, 107, 74, 0.12)` | Ambient glow effect |
| `--vignette` | `rgba(0, 0, 0, 0.5)` | Vignette overlay |
| `--grain-opacity` | `0.035` | Film grain effect |

### Legacy Aliases (SPA compatibility)

| Token | Value | Mirrors |
|-------|-------|---------|
| `--color-bg` | `#0d0705` | `--bg` |
| `--color-surface` | `#1a0d0a` | `--surface` |
| `--color-text` | `#f0e6e4` | `--text` |
| `--color-text-muted` | `#c4a8a4` | `--text-muted` |
| `--color-border` | `#2a1814` | `--border` |

The web SPA's stylesheet still reads these older `--color-*` names, so the
theme ships them as literal duplicates of the canonical tokens' values. New
code should target the canonical names in the tables above.

## Development

```bash
composer install
```

Run the test suite. `phpunit.xml` bootstraps `tests/bootstrap.php`, which loads the `dev-stubs/` interfaces when the host's `Phlix\Shared\Plugin\LifecycleInterface` and `Phlix\Theming\ThemeSourceInterface` are absent:

```bash
vendor/bin/phpunit --colors=always
```

Static analysis and coding standards. Both scripts pass the config file explicitly so `phpstan.neon` picks up `dev-stubs/` via `scanDirectories` and `phpcs.xml` checks `src`, `tests`, and `dev-stubs`:

```bash
composer phpstan
composer phpcs
```

`.github/workflows/test.yml` runs all three on PHP `8.3` and `8.4`, and — from the `8.3` leg only — uploads the `coverage.xml` Cobertura report to Codacy. That upload step is `continue-on-error: true`: coverage reporting is best-effort and can never redden the suite (for example, a missing Codacy token degrades silently by design).

## License

MIT License - see [LICENSE](LICENSE) for details.
