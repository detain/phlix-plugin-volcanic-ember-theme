# Volcanic Ember Theme Plugin

A warm, fiery dark UI theme for Phlix built on the midnight base with ember orange accents evoking volcanic lava and glowing coals.

## Theme Tokens

### Color Palette

| Token | Hex | Usage |
|-------|-----|-------|
| `--accent` | `#ff6b4a` | Primary ember accent |
| `--accent-hover` | `#ff8a70` | Hover state |
| `--accent-active` | `#e54f2e` | Active/pressed state |
| `--accent-soft` | `rgba(255, 107, 74, 0.14)` | Soft accent for backgrounds |
| `--accent-ring` | `rgba(255, 107, 74, 0.70)` | Focus rings |
| `--accent-text` | `#ff6b4a` | Ember orange AS foreground text/icon — AA (≥4.5:1) on the dark surfaces |

### Background Stack

| Token | Hex | Usage |
|-------|-----|-------|
| `--bg` | `#0d0705` | Page background |
| `--surface` | `#1a0d0a` | Card/panel background |
| `--surface-2` | `#261310` | Elevated surface |
| `--surface-3` | `#331a14` | Highest elevation |
| `--surface-glass` | `rgba(30, 12, 10, 0.62)` | Glass effect background |
| `--surface-glass-strong` | `rgba(15, 5, 3, 0.82)` | Strong glass effect |

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

## Requirements

- PHP 8.3+
- Phlix 0.44.0+

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

`.github/workflows/test.yml` runs all three on PHP `8.3` and `8.4`, and uploads the `coverage.xml` cobertura report to Codacy from the `8.3` leg.

## License

MIT License - see [LICENSE](LICENSE) for details.
