# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Changed

- PHP 8.0: handle changed tokenization of namespaced names [#19] from [@jrfnl].
- Use PHP Console Color to version 1.0 [#17] from [@jrfnl].

### Internal

- Travis: add build against PHP 8.0 [#18] from [@jrfnl].
- Added EOF (end of file) for some PHP files [#10] from [@peter279k].
- To be compatible with future PHPUnit version, using the ^4.8.36 version at least [#10] from [@peter279k].
- Changed namespace to PHPunit\Framework\TestCase class namespace [#10] from [@peter279k].
- Travis: improve caching between builds [#14] from [@jrfnl].
- Travis: change from "trusty" to "xenial" [#16] from [@jrfnl].
- PHPUnit: use a type-safe assertion [#15] from [@jrfnl].
- PHPUnit: make the tests platform independent [#15] from [@jrfnl].
- PHPUnit: use annotations for fixtures / cross-version compat up to PHPUnit 9.x [#15] from [@jrfnl].
- PHPUnit: improve configuration [#21] from [@jrfnl].
- PHPCS: various improvements [#20] from [@jrfnl].
- Composer: update allowed version for various dependencies [#12] from [@jrfnl].
- CI: switch to ghactions [#23] from [@jrfnl].
- GH Actions: set error reporting to E_ALL [#24] from [@jrfnl].

[#10]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/10
[#12]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/12
[#14]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/14
[#15]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/15
[#16]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/16
[#17]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/17
[#18]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/18
[#19]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/19
[#20]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/20
[#21]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/21
[#23]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/23
[#24]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/pull/24


## [0.5] - 2020-05-13

### Added

- Added changelog from [@reedy].

### Internal

- Cleaned readme - new organization from previous package from [@grogy].
- Composer: marked package as replacing jakub-onderka/php-console-highlighter from [@grogy].
- Composer: updated dependencies to use new php-parallel-lint organisation from [@grogy].
- Travis: test against PHP 7.4 and nightly from [@jrfnl].
- Fixed build script from [@jrfnl].
- Added a .gitattributes file from [@reedy].
- Updated installation command from [@cafferata].


[Unreleased]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/compare/v0.5...HEAD
[0.5]: https://github.com/php-parallel-lint/PHP-Console-Highlighter/compare/v0.4...v0.5

[@cafferata]: https://github.com/cafferata
[@grogy]: https://github.com/grogy
[@jrfnl]: https://github.com/jrfnl
[@peter279k]: https://github.com/peter279k
[@reedy]: https://github.com/reedy
