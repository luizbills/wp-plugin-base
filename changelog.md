# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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