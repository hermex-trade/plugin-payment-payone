<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\RequestFactoryContract;

class GenericPaymentRequestFactory
{

    /**
     * @param string $paymentMethod
     * @param array $data
     * @param null $referenceId
     * @return mixed
     */
    public static function create(string $paymentMethod, array $data, $referenceId = null)
    {
        $context = $data['context'];
        $config = new Config(
            $context['aid'],
            $context['mid'],
            $context['portalid'],
            $context['key'],
            $context['mode']
        );

        $systemInfoData = $data['systemInfo'];
        $systemInfo = new SystemInfo(
            $systemInfoData['vendor'],
            Version::getVersion(),
            $systemInfoData['module'],
            $systemInfoData['module_version']
        );

        switch ($data['add_paydata']['action']) {
            case 'getconfiguration':
                // Other configs can be added here. Just add an if-condition for $paymentMethod
                return new AmazonPayConfigurationRequest($config, $systemInfo, $data['currency']);
            case 'getorderreferencedetails':
                return new AmazonPayGetOrderReferenceRequest(
                    $config,
                    $systemInfo,
                    $data['add_paydata']['amazon_reference_id'],
                    $data['add_paydata']['amazon_address_token'],
                    $data['workorderid'],
                    $data['amount'],
                    $data['currency']);
            case 'setorderreferencedetails':
                return new AmazonPaySetOrderReferenceRequest(
                    $config,
                    $systemInfo,
                    $data['add_paydata']['amazon_reference_id'],
                    $data['workorderid'],
                    $data['amount'],
                    $data['currency']);
            case 'confirmorderreference':
                return new AmazonPayConfirmOrderReferenceRequest(
                    $config,
                    $systemInfo,
                    $data['add_paydata']['amazon_reference_id'],
                    $data['add_paydata']['reference'],
                    $data['workorderid'],
                    $data['amount'],
                    $data['currency'],
                    $data['successurl'],
                    $data['errorurl']);
        }


    }
}
