<?php
/**
 * 微信支付H5支付示例
 */

require __DIR__ . '/../../vendor/autoload.php';
@header('Content-Type: text/html; charset=UTF-8');
$hostInfo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

//引入配置文件
$wechatpay_config = require('config.php');

//构造支付参数
$params = [
    'description' => 'sample body', //商品名称
    'out_trade_no' => date("YmdHis").rand(111,999), //商户订单号
    'notify_url' => $hostInfo.dirname($_SERVER['SCRIPT_NAME']).'/notify.php', //异步回调地址
    'amount' => [
        'total' => 150, //支付金额，单位：分
        'currency' => 'CNY'
    ],
    'scene_info' => [
        'payer_client_ip' => $_SERVER['REMOTE_ADDR'], //支付用户IP
        'h5_info' => [ //H5支付场景信息
            'type' => 'Wap',
            'app_name' => '在线商城',
            'app_url' => $hostInfo,
        ],
    ]
];

//H5支付成功跳转地址
$redirect_url = $hostInfo.dirname($_SERVER['SCRIPT_NAME']).'/return.php';

//发起支付请求
try {
    $client = new \tinymeng\WeChatPay\V3\PaymentService($wechatpay_config);
    $result = $client->h5Pay($params);
    
    //H5支付跳转地址
    $url=$result['mweb_url'].'&redirect_url='.urlencode($redirect_url);
    ob_clean();
    header('Location: '.$url);

} catch (Exception $e) {
    echo '微信支付下单失败！'.$e->getMessage();
}
