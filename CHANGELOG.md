# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Changed

- Composer: update allowed version for various dependencies from [@jrfnl](https://github.com/jrfnl).
- Use PHP Console Color to version 1.0 [#17](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/17) from [@jrfnl](https://github.com/jrfnl).

### Internal

- Travis: add build against PHP 8.0 [#18](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/18) from [@jrfnl](https://github.com/jrfnl).
- Added EOF (end of file) for some PHP files from [@peter279k](https://github.com/peter279k).
- To be compatible with future PHPUnit version, using the ^4.8.36 version at least from [@peter279k](https://github.com/peter279k).
- Changed namespace to PHPunit\Framework\TestCase class namesapce from [@peter279k](https://github.com/peter279k).
- Travis: improve caching between builds [#14](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/14) from [@jrfnl](https://github.com/jrfnl).
- Travis: change from "trusty" to "xenial" [#16](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/16) from [@jrfnl](https://github.com/jrfnl).
- PHPUnit: use a type-safe assertion [#15](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/15) from [@jrfnl](https://github.com/jrfnl).
- PHPUnit: make the tests platform independent [#15](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/15) from [@jrfnl](https://github.com/jrfnl).
- PHPUnit: use annotations for fixtures / cross-version compat up to PHPUnit 9.x [#15](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/15) from [@jrfnl](https://github.com/jrfnl).
- PHPUnit: improve configuration [#21](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/21) from [@jrfnl](https://github.com/jrfnl).
- PHP 8.0: handle changed tokenization of namespaced names [#19](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/19) from [@jrfnl](https://github.com/jrfnl).
- PHPCS: various improvements [#20](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/20) from [@jrfnl](https://github.com/jrfnl).
- CI: switch to ghactions [#23](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/23) from [@jrfnl](https://github.com/jrfnl).
- GH Actions: set error reporting to E_ALL [#24](https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/24) from [@jrfnl](https://github.com/jrfnl).

## [0.5] - 2020-05-13

### Added

- Added changelog from [@reedy](https://github.com/reedy).

### Internal

- Cleaned readme - new organization from previous package from [@grogy](https://github.com/grogy).
- Composer: marked package as replacing jakub-onderka/php-console-highlighter from [@grogy](https://github.com/grogy).
- Composer: updated dependancies to use new php-parallel-lint organisation from [@grogy](https://github.com/grogy).
- Travis: test against PHP 7.4 and nightly from [@jrfnl](https://github.com/jrfnl).
- Fixed build script from [@jrfnl](https://github.com/jrfnl).
- Added a .gitattributes file from [@reedy](https://github.com/reedy).
- Updated installation command from [@cafferata](https://github.com/cafferata).

