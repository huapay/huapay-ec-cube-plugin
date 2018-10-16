<?php
namespace Plugin\HuapayPlugin;

class Constants {
    const HUAPAY_APITOKEN_DEFAULT = 'HUAPAY_API_TOKEN_DEFAULT';
    const PAYMENT_METHOD_INFO = [
        [ 'shortname' => 'unionpay', 'name' => '银联（UnionPay）'],
        [ 'shortname' => 'alipay', 'name' => '支付宝（Alipay）'],
        [ 'shortname' => 'wechatpay', 'name' => '微信支付（WeChatPay）'],
    ];

    const DEFAULT_MAIL_TEMPLATE_ID = 1;
    const DEFAULT_PLUGIN_PAYMENT_ID = 1;
}

?>
