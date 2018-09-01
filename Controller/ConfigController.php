<?php

/*
 * This file is part of the HuaPayPlugin
 *
 * Copyright (C) 2018 HuaPay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\HuaPayPlugin\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{

    /**
     * HuaPayPlugin用設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

	$PaymentRepository = $app['eccube.plugin.repository.payment'];
        $form = $app['form.factory']->createBuilder('huapayplugin_config')->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

	    $payment = $PaymentRepository->find(1);
	    if (is_null($payment)) {
	        var_dump('null');
	    }

            // add code...
	    $app['monolog.logger.huapayplugin']->debug($data);
	    var_dump($data);
        }

        return $app->render('HuaPayPlugin/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }

}
