<?php

namespace C2is\Provider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use \Exception;

/**
 * @author Morgan Brunot <brunot.morgan@gmail.com>
 */
class CrudController implements ControllerProviderInterface
{
    protected $model, $form;

    public function __construct($name, $model, $form)
    {
        $this->name  = $name;
        $this->model = $model;
        $this->form  = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection($app['route_factory'] );

        $controllers->match('/', function (Request $request) use ($app) {
            $modelQuery = sprintf('%sQuery', $this->model);
            $objects    = $modelQuery::create()->find();

            return $app->render('crud/list.html.twig', array(
                'objects' => $objects,
            ));
        })
        ->bind(sprintf('%s_crud_list', $this->name));

        $controllers->match('/create', function (Request $request) use ($app) {
            $modelObject = new $this->model();

            $formObject = $app['form.factory']->create(new $this->form(), $modelObject);

            if ('POST' == $request->getMethod()) {
                $formObject->bindRequest($request);

                if ($formObject->isValid()) {
                    $modelObject->saveFromCrud($app, $formObject);

                    return $app->redirect($app->path(sprintf('%s_crud_list', $this->name)));
                }
            }

            return $app->render('crud/create.html.twig', array(
                'form'  => $formObject->createView(),
            ));
        })
        ->bind(sprintf('%s_crud_create', $this->name));

        return $controllers;
    }
}
