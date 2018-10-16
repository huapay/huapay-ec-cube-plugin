<?php
namespace Plugin\HuapayPlugin;

use Eccube\Util\EntityUtil;
use Eccube\Common\Constant;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HuapayPluginEvent
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function onControllerShoppingConfirmBefore($event = NULL)
    {
        $order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $this->app['eccube.service.cart']->getPreOrderId()));
        $form = $this->app['eccube.service.shopping']->getShippingForm($order);

        $request = $event->getRequest();
        $response = $event->getResponse();

        $payment_methods = $this->app['eccube.plugin.repository.paymentmethod']->findBy(array('plugin_payment_id' => 1));
	$payment_method_ids = array_map(function($payment_method) {
		return $payment_method->getPaymentId();
	}, $payment_methods);
        $payment = $order->getPayment();
        
        if (in_array($payment->getId(), $payment_method_ids, true)) {
	    $url = $this->app->url('huapayplugin');
          
	    if ($event instanceof \Symfony\Component\HttpKernel\Event\KernelEvent) {
                $response = $this->app->redirect($url);
                $event->setResponse($response);
                return;
            } else {
                header("Location: " . $url);
                exit;
            }
        }
    }
    
    public function onControllerAdminPaymentDeleteBefore($event = NULL)
    {
        if ($this->app->isGranted('ROLE_ADMIN')) {
            $request = $event->getRequest();
            $id = $request->get('id');

            $payment_methods = $this->app['eccube.plugin.repository.paymentmethod']->findBy(array('plugin_payment_id' => 1));
	    foreach ($payment_methods as $payment_method) {
	        if ($id == $payment_method->getPaymentId()) {
                    $payment_method->setIsEnabled(0);
                    $this->app['orm.em']->flush($payment_method);
		}
	    }
        }
    }
}
