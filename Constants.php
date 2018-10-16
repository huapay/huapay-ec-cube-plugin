<?php
namespace Plugin\HuapayPlugin;

class Constants {
    const HUAPAY_APITOKEN_DEFAULT = 'HUAPAY_API_TOKEN_DEFAULT';
    const PAYMENT_METHOD_INFO = [
        [ 'shortname' => 'unionpay', 'name' => 'UnionPay(銀聯)'],
        [ 'shortname' => 'alipay', 'name' => 'AliPay(支付宝)'],
        [ 'shortname' => 'wechatpay', 'name' => 'WeChatPay(微信支付)'],
    ];

    const DEFAULT_MAIL_TEMPLATE_ID = 1;
    const DEFAULT_PLUGIN_PAYMENT_ID = 1;
}

?>
