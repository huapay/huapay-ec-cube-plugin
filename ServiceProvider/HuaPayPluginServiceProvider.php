<?php

/*
 * This file is part of the HuaPayPlugin
 *
 * Copyright (C) 2018 HuaPay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\HuaPayPlugin\ServiceProvider;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\HuaPayPlugin\Form\Type\HuaPayPluginConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class HuaPayPluginServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // プラグイン用設定画面
        $app->match('/'.$app['config']['admin_route'].'/plugin/HuaPayPlugin/config', 'Plugin\HuaPayPlugin\Controller\ConfigController::index')->bind('plugin_HuaPayPlugin_config');

        // 独自コントローラ
        $app->match('/plugin/huapayplugin/hello', 'Plugin\HuaPayPlugin\Controller\HuaPayPluginController::index')->bind('plugin_HuaPayPlugin_hello');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new HuaPayPluginConfigType();

            return $types;
        }));

        // Repository

        // Service

        // メッセージ登録
        // $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
        // $app['translator']->addResource('yaml', $file, $app['locale']);

        // load config
        // プラグイン独自の定数はconfig.ymlの「const」パラメータに対して定義し、$app['huapaypluginconfig']['定数名']で利用可能
        // if (isset($app['config']['HuaPayPlugin']['const'])) {
        //     $config = $app['config'];
        //     $app['huapaypluginconfig'] = $app->share(function () use ($config) {
        //         return $config['HuaPayPlugin']['const'];
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
