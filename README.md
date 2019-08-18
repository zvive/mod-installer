# Laravel Module Installer

The purpose of this package is to allow for easy installation of standalone Modules into the [Laravel Modules](https://github.com/nWidart/laravel-modules) package. This package will ensure that your module is installed into the `Modules/` directory instead of `vendor/`.

You can specify an alternate directory by either including a `module-dir` in the extra data in your composer.json file of the laravel app NOT the module like this:

    "extra": {
        "module-dir": "Custom"
    }

Alternately you can add the following config option to config/modules.php:
    "path": "_Modules",

This is based off of (joshbrw/laravel-module-installer)[http://github.com/joshbrw/laravel-module-installer] but I didn't like how you had to follow a specific naming pattern. 

Instead I tapped into laravel's config as well as added a composer default way of adding that data. 

If you manage the package you can set the default by adding the following to extras in the package's composer.json: 
    "extra": {
        "laravel": {
            "module-name": "Blog"
        }
    }

This will move it to _Modules/Blog for example if you used the path config from the previous step.

Alternately you can 'map' your packages via the modules config: 
    'packages' => [
        ['name' => 'Blog', 'package' => 'zvive/middle']
    ]

## Installation

1. Ensure you have the `type` set to `laravel-module` in your module's `composer.json`
2. Ensure your package is named in the convention `<namespace>/<name>-module`, for example `joshbrw/user-module` would install into `Modules/User`
3. Require this package: `composer require joshbrw/laravel-module-installer`
4. Require your bespoke module using Composer. You may want to set the constraint to `dev-master` to ensure you always get the latest version.

## Notes
* When working on a module that is version controlled within an app that is also version controlled, you have to commit and push from inside the Module directory and then `composer update` within the app itself to ensure that the latest version of your module (dependant upon constraint) is specified in your composer.lock file.
