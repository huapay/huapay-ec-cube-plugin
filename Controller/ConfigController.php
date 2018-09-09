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
use Eccube\Common\Constant;

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
	$Payment = $PaymentRepository->find(1);

	$PaymentMethodMap = array();

	$PaymentMethodRepository = $app['eccube.plugin.repository.paymentmethod'];
	$PaymentMethodMap['unionpay'] = $PaymentMethodRepository->findOneBy(array('plugin_payment_id' => $Payment->getId(), 'name' => 'unionpay'));

	$PaymentMethodRepository = $app['eccube.plugin.repository.paymentmethod'];
	$PaymentMethodMap['alipay'] = $PaymentMethodRepository->findOneBy(array('plugin_payment_id' => $Payment->getId(), 'name' => 'alipay'));

	$PaymentMethodRepository = $app['eccube.plugin.repository.paymentmethod'];
	$PaymentMethodMap['wechatpay'] = $PaymentMethodRepository->findOneBy(array('plugin_payment_id' => $Payment->getId(), 'name' => 'wechatpay'));

        $form = $app['form.factory']->createBuilder('huapayplugin_config', $Payment)->getForm();

	$form->get('uses_unionpay')->setData($PaymentMethodMap['unionpay']->getIsEnabled());
	$form->get('uses_alipay')->setData($PaymentMethodMap['alipay']->getIsEnabled());
	$form->get('uses_wechatpay')->setData($PaymentMethodMap['wechatpay']->getIsEnabled());

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

		$data->setId(1);
		$PaymentMethodMap['unionpay']->setIsEnabled($form->get('uses_unionpay')->getData());
		$PaymentMethodMap['alipay']->setIsEnabled($form->get('uses_alipay')->getData());
		$PaymentMethodMap['wechatpay']->setIsEnabled($form->get('uses_wechatpay')->getData());
		$app['orm.em']->persist($PaymentMethodMap['unionpay']);
		$app['orm.em']->persist($PaymentMethodMap['alipay']);
		$app['orm.em']->persist($PaymentMethodMap['wechatpay']);
		$app['orm.em']->persist($data);

		$usesMethodsMap = array(
		    $PaymentMethodMap['unionpay']->getPaymentId() => $form->get('uses_unionpay')->getData(),
		    $PaymentMethodMap['alipay']->getPaymentId() => $form->get('uses_alipay')->getData(),
		    $PaymentMethodMap['wechatpay']->getPaymentId() => $form->get('uses_wechatpay')->getData(),
		);

                $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
                $softDeleteFilter->setExcludes(array(
                    'Eccube\Entity\Payment'
                ));

                foreach ($usesMethodsMap as $payment_id => $uses_method){
                    $payment = $app['eccube.repository.payment']->find($payment_id);
                    if ($uses_method) {
                        $payment->setDelFlg(Constant::DISABLED);
                    } else {
                        $payment->setDelFlg(Constant::ENABLED);
                    }
                    $app['orm.em']->persist($payment);
                }
                
                $app['orm.em']->flush();

                $app->addSuccess('admin.register.complete', 'admin');
                return $app->redirect($app['url_generator']->generate('plugin_HuaPayPlugin_config'));
            }
	}

        return $app->render('HuaPayPlugin/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }

}
