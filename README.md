#Do Not Use - Work In Progress#

Behavior that allows you to generate crud from your Propel schema.

```ini
# register the crudable behavior
propel.behavior.crudable.class = ${propel.php.dir}/C2is.Behavior.Crudable.CrudableBehavior

# setting the crudable behavior
propel.behavior.crudable.phpconf.dir = ${propel.php.dir}/Resources/config/crudable/generated
propel.behavior.crudable.web.dir     = ${propel.php.dir}/../web
propel.behavior.crudable.languages   = fr;en
```

Add form extension:
```php
$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
    $extensions[] = new \C2is\Form\Extension();

    return $extensions;
}));
```

Add translation file:
```php
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $languagesConf = require __DIR__.'/Resources/config/crudable/generated/languages-conf.php';
    foreach ($languagesConf as $languageConf) {
        $translator->addResource('yaml', sprintf('%s/Resources/locales/%s', __DIR__, $languageConf['filename']), $languageConf['locale']);
    }

    return $translator;
}));
```
Add custom render form layout:
```php
$app->register(new TwigServiceProvider(), [
    'twig.path'           => array(__DIR__.'/Resources/views/', __DIR__.'/C2is/Resources/views/'),
    'twig.form.templates' => array('Form/form_c2is_layout.html.twig'),
]);
```


