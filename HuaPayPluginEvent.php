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
use Eccube\Event\EventArgs;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

class HuaPayPluginEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    /**
     * HuaPayPluginEvent constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRouteShoppingConfirmRequest(GetResponseEvent $event)
    {
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRouteAdminSettingShopPaymentDeleteRequest(GetResponseEvent $event)
    {
    }

}
