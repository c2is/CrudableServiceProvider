<?php

namespace C2is\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

class CrudableServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
            $extensions[] = new \C2is\Form\Extension();

            return $extensions;
        }));

        $app['twig.path'] = array_merge($app['twig.path'], array(__DIR__.'/../Resources/views/'));
        $app['twig.form.templates'] = array_merge($app['twig.form.templates'], array('Form/form_c2is_layout.html.twig'));
    }

    public function boot(Application $app)
    {
    }
}
