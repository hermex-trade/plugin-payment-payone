<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Order\Models\Order;

/**
 * Class RefundDataProvider
 */
class RefundDataProvider extends DataProviderAbstract implements DataProviderOrder
{

    /**
     * {@inheritdoc}
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['context']['sequencenumber'] = $this->getSequenceNumber($order);

        $requestParams['order'] = $this->getBasketDataFromOrder($order);

        $requestParams['referenceId'] = $requestReference;

        $this->validator->validate($requestParams);

        return $requestParams;
    }

    /**
     * @param $paymentCode
     * @param Order $order
     * @param Order $refund
     * @param $preAuthUniqueId
     * @return array
     */
    public function getPartialRefundData($paymentCode, Order $order, Order $refund, $preAuthUniqueId)
    {
        $requestParams = $this->getDataFromOrder($paymentCode, $order, $preAuthUniqueId);

        $requestParams['order'] = $this->getBasketDataFromOrder($refund);
        $requestParams['context']['transactionId'] = 'order-' . $order->id;

        $this->validator->validate($requestParams);

        return $requestParams;
    }
}
