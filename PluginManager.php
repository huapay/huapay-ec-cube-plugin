<?php

/*
 * This file is part of the HuaPayPlugin
 *
 * Copyright (C) 2018 HuaPay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\HuaPayPlugin;

use Eccube\Application;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\HuapayPlugin\Entity;

class PluginManager extends AbstractPluginManager
{

    /**
     * プラグインインストール時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function install($config, Application $app)
    {
    }

    /**
     * プラグイン削除時の処理
     *
     * @param $config
     * @param Application $app
     */
    public function uninstall($config, Application $app)
    {
        $this->disableAllPayment($app);

        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * プラグイン有効時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function enable($config, Application $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);

        $em = $app['orm.em'];
        $em->getConnection()->beginTransaction();
        try {
            $pay = $em->getRepository('Plugin\HuaPayPlugin\Entity\Payment')->find(1);

            if( !$pay ){
                $pay = new Entity\Payment();
                $pay->setId(1);
                $pay->setIsTesting(1);
		$pay->setApiToken('dummy');
                $em->persist($pay);
                $em->flush($pay);
            }

            // enable each payment methods

            $em->getConnection()->commit();

        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
	}
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function disable($config, Application $app)
    {
        $this->disableAllPayment($app);
    }

    /**
     * プラグイン更新時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function update($config, Application $app)
    {
    }

    private function createPayment($method, $app)
    {
        $em = $app['orm.em'];
        $Payment = new Payment();

        $rank = $app['eccube.repository.payment']->findOneBy(array(), array('rank' => 'DESC'))
                ->getRank() + 1;

        $Payment->setMethod($method);
        $Payment->setCharge(0);
        $Payment->setRuleMin(0);
        $Payment->setFixFlg(Constant::ENABLED);
        $Payment->setChargeFlg(Constant::ENABLED);
        $Payment->setRank($rank);
        $Payment->setDelFlg(Constant::DISABLED);

        $em->persist($Payment);
        $em->flush($Payment);

        return($Payment->getId());
    }
    private function enablePayment($payment_id, $app)
    {
        $em = $app['orm.em'];
        // soft_deleteを無効にする
        $softDeleteFilter = $em->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\Payment'
        ));

        $Payment = $app['eccube.repository.payment']->find($payment_id);
        if ($Payment) {
            $Payment->setDelFlg(Constant::DISABLED);
            $em->flush($Payment);
        }
    }

    private function disablePayment($payment_id, $app)
    {
        $em = $app['orm.em'];

        $Payment = $app['eccube.repository.payment']->find($payment_id);
        if ($Payment) {
            $Payment->setDelFlg(Constant::ENABLED);
            $em->flush($Payment);
        }
    }
    private function disableAllPayment($app)
    {
        $em = $app['orm.em'];
        $paymentMethodRepository = $em->getRepository('Plugin\HuaPayPlugin\Entity\PaymentMethod');

	$query = $paymentMethodRepository->createQueryBuilder('p')
            ->where('p.plugin_payment_id = (:plugin_payment_id)')
            ->orderBy('p.id', 'ASC')
            ->setParameter('plugin_payment_id', 1)
            ->getQuery();

	$paymentMethods = $query->getArrayResult();
	var_dump($paymentMethods);

	// loop and call each disablePayment
    }

}
