#Do Not Use - Work In Progress#
____

# CrudableServiceProvider

Allows you to generate `CRUD` from your Propel schema.

## Install

Add these lines in your propel configuration file:
```ini
# register the crudable behavior
propel.behavior.crudable.class = ${propel.php.dir}/C2is.Behavior.Crudable.CrudableBehavior

# setting the crudable behavior
propel.behavior.crudable.phpconf.dir = ${propel.php.dir}/Resources/config/crudable/generated
propel.behavior.crudable.web.dir     = ${propel.php.dir}/../web
propel.behavior.crudable.languages   = fr;en
```

Register the service:
```php
use C2is\Provider\CrudableServiceProvider;

$app->register(new CrudableServiceProvider());
```

## Usage

Use the `propel-gen` script for generate *model*, *form* and *listing* classes:
```shell
$ ./vendor/bin/propel-gen ./path/to/propel/ main
```