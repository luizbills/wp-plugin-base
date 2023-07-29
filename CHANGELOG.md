# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.10.0 - 2023-07-29

-   Feat: Added [`h::critical_log()`](https://github.com/luizbills/wp-plugin-base/blob/main/core/Traits/Log_Helpers.php#L35) helper

## 1.9.0 - 2023-07-18

-   Refactor: Now the `init` hook is used to load the plugin text domain (in `core/Main.php`)
-   Refactor: Simplified the `main.php`

## 1.8.0 - 2023-06-08

-   Feat: allow secundary development config in [`config.dev.php`](/config.dev.php)

## 1.7.1 - 2023-06-07

-   Fix: `h::get_transient()` was not respecting the `CACHE_ENABLED` config.

## 1.7.0 - 2022-09-28

-   By default, `Helpers::throw_if()` now throws `\Exception` rather than `\Error`.

## 1.6.1 - 2022-09-28

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.6.0...1.6.1)

-   Fix: Undefined variable `$path` in [`core/Traits/Template_Helpers.php`](/core/Traits/Template_Helpers.php).

## 1.6.0 - 2022-09-01

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.5.0...1.6.0)

-   Added `get_template_path` method in [`core/Traits/Template_Helpers.php`](/core/Traits/Template_Helpers.php).
-   Added `get_templates_dir` method in [`core/Traits/Template_Helpers.php`](/core/Traits/Template_Helpers.php).

## 1.5.0 - 2022-09-01

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.4.1...1.5.0)

-   Fix and improve the missing dependencies notice.

## 1.4.1 - 2022-09-01

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.4.0...1.4.1)

-   Fix: incorrect `h::log` output.

## 1.4.0 - 2022-09-01

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.3.1...1.4.0)

-   Feat: allow class instances in `loader.php`.
-   Fix: remove _falsy_ values from `loader.php`.

## 1.3.1 - 2022-08-19

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.3.0...1.3.1)

-   Fixed the `composer run upgrade-core` command.

## 1.3.0 - 2022-08-19

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.2.0...1.3.0)

-   Now [`/classes/Helpers.php`](/classes/Helpers.php) it's a bunch of Traits. This will allow helper methods to be improved and fixed when needed. See [/core/Traits](/core/Traits)
-   Removed the following helpers: `h::get_slug`, `h::logger`, `h::update_option`, `h::get_option`, `h::nothrow`.

### ➞ Migration Guide

-   If you are using the `classes/Helpers.php` file, replace your file with the [new version with Traits](/classes/Helpers.php).

## 1.2.0 - 2022-08-15

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.1.0...1.2.0)

-   refactor: remove `Helpers::log_wp_error`
-   refactor: remove `Helpers::pipe`
-   refactor: PHP version verification now is made in `main.php` rather than in `/dependencies.php`
-   feat: classes in `/loader.php` now can has a `__activation` static method. This is a alias for `register_activation_hook`
-   feat: classes in `/loader.php` now can has a `__deactivation` static method. This is a alias for `register_deactivation_hook`
-   feat: shortcut for check dependencies. Learn more in [dependencies.php](/1.2.0/dependencies.php)

## 1.1.0 - 2022-08-10

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.0.3...1.1.0)

-   refactor: remove unused `LANGUAGES_DIR` key from `/config.php`
-   docs: better README.md

## 1.0.3 - 2022-08-08

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.0.2...1.0.3)

-   Minor fixes

## 1.0.2 - 2022-08-08

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.0.1...1.0.2)

-   Fix: `composer run make-pot` don't create pot file in correct folder

## 1.0.1 - 2022-08-07

[Source code changes](https://github.com/luizbills/wp-plugin-base/compare/1.0.0...1.0.1)

-   Fix: undefined `Helpers::user_is_admin` used in `Helpers::get_template`
-   Feat: added `Requires PHP` and `Requires at least` plugin headers

## 1.0.0 - 2022-08-05

-   Public release
