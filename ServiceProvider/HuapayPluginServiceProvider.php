<?php

/*
 * This file is part of the HuapayPlugin
 *
 * Copyright (C) 2018 Huapay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\HuapayPlugin\ServiceProvider;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\HuapayPlugin\Form\Type\HuapayPluginConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class HuapayPluginServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
	$app->match('/shopping/huapay', '\\Plugin\\HuapayPlugin\\Controller\\HuapayPluginController::index')->bind('huapayplugin');
	$app->match('/shopping/huapay/ipn', '\\Plugin\\HuapayPlugin\\Controller\\HuapayPluginController::api_ipn')->bind('huapayplugin_ipn');
	$app->match('/shopping/huapay/callback', '\\Plugin\\HuapayPlugin\\Controller\\HuapayPluginController::api_callback')->bind('huapayplugin_callback');

        // プラグイン用設定画面
        $app->match('/'.$app['config']['admin_route'].'/plugin/HuapayPlugin/config', 'Plugin\HuapayPlugin\Controller\ConfigController::index')->bind('plugin_HuapayPlugin_config');

        // 独自コントローラ
        $app->match('/plugin/huapayplugin/hello', 'Plugin\HuapayPlugin\Controller\HuapayPluginController::index')->bind('plugin_HuapayPlugin_hello');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new HuapayPluginConfigType();

            return $types;
        }));

        // Repository
        $app['eccube.plugin.repository.payment'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\HuapayPlugin\Entity\Payment');
        });
        $app['eccube.plugin.repository.paymentmethod'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\HuapayPlugin\Entity\PaymentMethod');
        });

        // Service

        // メッセージ登録
        // $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
        // $app['translator']->addResource('yaml', $file, $app['locale']);

        // load config
        // プラグイン独自の定数はconfig.ymlの「const」パラメータに対して定義し、$app['huapaypluginconfig']['定数名']で利用可能
        // if (isset($app['config']['HuapayPlugin']['const'])) {
        //     $config = $app['config'];
        //     $app['huapaypluginconfig'] = $app->share(function () use ($config) {
        //         return $config['HuapayPlugin']['const'];
        //     });
        // }

        // ログファイル設定
        $app['monolog.logger.huapayplugin'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('huapayplugin');

            $filename = $app['config']['root_dir'].'/app/log/huapayplugin.log';
            $RotateHandler = new RotatingFileHandler($filename, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'huapayplugin_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::ERROR),
                    0,
                    true,
                    true,
                    Logger::INFO
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }

}
