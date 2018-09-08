<?php
namespace Plugin\HuaPayPlugin\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Common\Constant;

class HuaPayPluginController
{
    private $sessionOrderKey = 'eccube.front.shopping.order.id';
    
    public function index(\Eccube\Application $app)
    {
        $pre_order_id = $app['eccube.service.cart']->getPreOrderId();
        $order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $pre_order_id));
        
        if (is_null($order)) {
            $error_title = 'エラー';
            $error_message = "注文情報の取得が出来ませんでした。管理者にお問い合わせください。";
            return $app['view']->render('error.twig', array('error_message' => $error_message, 'error_title'=> $error_title));
        }
        
        $order_id = $order->getId();
        $payment = $order->getPayment();
        $payment_id = $payment->getId();
        $payment_total = $order->getPaymentTotal();
        $payment_config = $app['eccube.plugin.repository.payment']->find(1);
        $payment_methods = $app['eccube.plugin.repository.paymentmethod']->findBy(array('plugin_payment_id' => 1));
        
        $reference_prefix = "REF_HUAPAYPLUGIN_";
        //$api_reference = substr(uniqid("{$reference_prefix}{$order_id}_"), 0, 30); 
        $api_reference = "{$reference_prefix}{$order_id}"; 
	$api_vender = "";

	foreach ($payment_methods as $payment_method) {
	    if ($payment_id == $payment_method->getPaymentId()) {
		$api_vender = $payment_method->getName();
		break;
	    }
	}

        $api_token = $payment_config->getApiToken();
        
        if (empty($api_vender) || empty($api_token)) {
            $error_title = 'エラー';
            $error_message = "決済エラーです。管理者にお問い合わせください。";
            return $app['view']->render('error.twig', array('error_message' => $error_message, 'error_title'=> $error_title));
        }
        
	return $this->process_curl($app, $payment_config->getIsTesting(), $api_token, $api_vender, $api_reference, $pre_order_id, $payment_total);
    }

    private function process_curl(\Eccube\Application $app, $is_testing, $api_token, $api_vender, $api_reference, $pre_order_id, $payment_total) {
        if ($is_testing) {
	    $nihao_api = 'https://apitest.nihaopay.com';
	} else {
	    $nihao_api = 'https://api.nihaopay.com';
        }
        
        // curl
        $handle = curl_init($nihao_api.'/v1.1/transactions/securepay');
        $params = array(
            'amount' => $payment_total,
            'currency' => 'JPY',
            'vendor' => $api_vender,
            'reference' => $api_reference,
            'note' => $pre_order_id,
            'ipn_url' => $app->url('huapayplugin_ipn'),
            'callback_url' => $app->url('huapayplugin_callback'),
        );
        $options = array(
            CURLOPT_HTTPGET => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => array(
	        "Authorization: Bearer {$api_token}",
            ),
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($handle, $options);
        $ret = curl_exec($handle);
        curl_close($handle);
        
        if (!$ret) {
            $error_title = 'エラー';
            $error_message = "決済エラーです。管理者にお問い合わせください。";
            return $app['view']->render('error.twig', array('error_message' => $error_message, 'error_title'=> $error_title));
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

		$MailTemplate = $app['eccube.repository.mail_template']->find(1);

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

	    return $app->redirect($app->url('shopping_complete'));
	}

	return $app->redirect($app->url('shopping'));
    }
}
