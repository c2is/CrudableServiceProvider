<?php

namespace C2is\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

use \RuntimeException;

/**
 * Crudable Provider.
 *
 * @author Morgan Brunot <brunot.morgan@gmail.com>
 */
class CrudableServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['crudable.templates_dir'] = __DIR__.'/../Resources/views/Crudable';
    }

    public function boot(Application $app)
    {
        if (!isset($app['crudable.config_dir'])) {
            throw new RuntimeException("The crudable.config_dir parameter is undefined. It's necessary for running the crudable service provider.", 1);
        }

        if (!isset($app['form.extensions'])) {
            throw new RuntimeException("Please, register the Form Service Provider.", 1);
        }

        if (!isset($app['twig.path'])) {
            throw new RuntimeException("Please, register the Twig Service Provider.", 1);
        }

        $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
            $extensions[] = new \C2is\Form\Extension();

            return $extensions;
        }));

        $app['twig.path'] = array_merge($app['twig.path'], array(__DIR__.'/../Resources/views/'));
        $app['twig.form.templates'] = array_merge($app['twig.form.templates'], array('Form/form_c2is_layout.html.twig'));
    }
}
