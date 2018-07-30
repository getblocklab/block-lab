# advanced-custom-blocks
A starting point for new WordPress plugins.

This plugin base requires at least PHP 5.3+ to use interfaces, abstract classes and anonymous functions. PHP 5.4+ is required to make use of *traits*.

---
#### PHPUnit Tests

Tests can be run by executing `make test`. These tests will run inside Docker containers. Minimum requirement
to run tests are:

- Docker (e.g. DockerCE for Mac)
- Docker Compose CLI (usually installed with Docker)
- GNU make (usually already on *nix based system. For Windows see: http://gnuwin32.sourceforge.net/packages/make.htm)

Test variables can be altered (with caution) in `./tests/docker/.env`

---

You will need to override the following in the plugin (or use `generate-wp-plugin` as a script to clone this repo and automatically substiture the bellow):

- `Advanced Custom Blocks` to be the name of your plugin.
- `AdvancedCustomBlocks` with the package and namespace of your plugin.
- `*advanced-custom-blocks*` slugs, textdomains and handles for the plugin.
- `*advanced_custom_blocks*` function names.
- `The Author` to your name.
- `https://authorsite.com` to be your url.
- `2018` to the current year.
- `0.1-alpha` to the plugin version.
