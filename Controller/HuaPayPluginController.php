<?php
namespace Plugin\HuaPayPlugin\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Common\Constant;
use Plugin\HuaPayPlugin;
use Plugin\HuaPayPlugin\Constants;

class HuaPayPluginController
{
    private $sessionOrderKey = 'eccube.front.shopping.order.id';
    
    public function index(\Eccube\Application $app)
    {
        $pre_order_id = $app['eccube.service.cart']->getPreOrderId();
        $order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $pre_order_id));
        if (is_null($order)) {
	    return $app['view']->render('error.twig', array('error_message' => Errors::MESSAGE_MISSING_ORDER_INFO,
		                                            'error_title'=> Errors::TITLE_DEFAULT));
        }
        
        $payment = $order->getPayment();
        $payment_config = $app['eccube.plugin.repository.payment']->find(Constants::DEFAULT_PLUGIN_PAYMENT_ID);
        
        $api_reference = "REF_HUAPAY_{$order->getId()}"; 
	$api_vender = $this->getPaymentVendor($app, $payment->getId());
        $api_token = $payment_config->getApiToken();

        if (empty($api_vender) || empty($api_token)) {
	    return $app['view']->render('error.twig', array('error_message' => Errors::MESSAGE_PAYMENT_FAILURE,
		                                            'error_title'=> Errors::TITLE_DEFAULT));
        }
        
	$ret = $this->processCurl($app, $payment_config->getIsTesting(), $api_token, $api_vender, $api_reference,
	                          $pre_order_id, $order->getPaymentTotal());
        if (!$ret) {
	    return $app['view']->render('error.twig', array('error_message' => Errors::MESSAGE_PAYMENT_FAILURE,
		                                            'error_title' => Errors::TITLE_DEFAULT));
        }
        
	return $ret;
    }

    public function api_ipn(\Eccube\Application $app)
    {
	return "api_huapay_ipn";
    }
    
    public function api_callback(\Eccube\Application $app)
    {
	$params = $app['request']->request->all();
	    
	if ($params['status'] == 'success') {
	    $app['orm.em']->getConnection()->beginTransaction();
        
	    $pre_order_id = $params['note'];
	    $order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $pre_order_id));
        
	    $app['eccube.service.order']->setStockUpdate($app['orm.em'], $order);

	    if ($app->isGranted('ROLE_USER')) {
		$app['eccube.service.order']->setCustomerUpdate($app['orm.em'], $order, $app->user());
	    }

	    $now = new \DateTime();
	    $order->setOrderDate($now);
	    $order->setPaymentDate($now);
	    $order->setOrderStatus($app['eccube.repository.order_status']->find($app['config']['order_pre_end']));
        
	    $app['orm.em']->flush();
	    $app['orm.em']->getConnection()->commit();

	    $app['session']->set($this->sessionOrderKey, $order->getId());
        
	    if (version_compare('3.0.10', Constant::VERSION, '<=')) {
		$app['eccube.service.shopping']->notifyComplete($order);
		$app['eccube.service.shopping']->sendOrderMail($order);
            } else {
		$app['eccube.service.mail']->sendOrderMail($order);
		$this->notifyCompleteNew($app, $order);
	    }

	    return $app->redirect($app->url('shopping_complete'));
	}

	return $app->redirect($app->url('shopping'));
    }

    private function notifyCompleteNew(\Eccube\Application $app, $order) {
	$MailTemplate = $app['eccube.repository.mail_template']->find(Constants::DEFAULT_MAIL_TEMPLATE_ID);

	$body = $app->renderView($MailTemplate->getFileName(), array(
	    'header' => $MailTemplate->getHeader(),
	    'footer' => $MailTemplate->getFooter(),
	    'Order' => $order,
	));

	$MailHistory = new MailHistory();
	$MailHistory
	    ->setSubject('[' . $app['eccube.repository.base_info']->get()->getShopName() . '] ' . $MailTemplate->getSubject())
	    ->setMailBody($body)
	    ->setMailTemplate($MailTemplate)
	    ->setSendDate(new \DateTime())
	    ->setOrder($order);
	$app['orm.em']->persist($MailHistory);
	$app['orm.em']->flush($MailHistory);
    }

    private function getPaymentVendor(\Eccube\Application $app, $payment_id) {
        $payment_methods = $app['eccube.plugin.repository.paymentmethod']->findBy(array('plugin_payment_id' => Constants::DEFAULT_PLUGIN_PAYMENT_ID));
	foreach ($payment_methods as $payment_method) {
	    if ($payment_id == $payment_method->getPaymentId()) {
		return $payment_method->getName();
	    }
	}
	return "";
    }

    private function getNihaoPayBaseUrl($is_testing) {
        if ($is_testing) {
	    $nihao_api = 'https://apitest.nihaopay.com';
	} else {
	    $nihao_api = 'https://api.nihaopay.com';
        }
	return $nihao_api;
    }

    private function processCurl(\Eccube\Application $app, $is_testing, $api_token, $api_vender, $api_reference, $pre_order_id, $payment_total) {
        $handle = curl_init($this->getNihaoPayBaseUrl($is_testing) . '/v1.1/transactions/securepay');
        $params = array(
            'amount' => $payment_total,
            'currency' => 'JPY',
            'vendor' => $api_vender,
            'reference' => $api_reference,
            'note' => $pre_order_id,
            'ipn_url' => $app->url('huapayplugin_ipn'),
            'callback_url' => $app->url('huapayplugin_callback'),
        );
        curl_setopt_array($handle, array(
            CURLOPT_HTTPGET => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => array(
	        "Authorization: Bearer {$api_token}",
            ),
            CURLOPT_RETURNTRANSFER => true,
        ));
        $ret = curl_exec($handle);
        curl_close($handle);
        
        return $ret;
    }
    
}
