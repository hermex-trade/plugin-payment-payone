<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Order\Models\Order;

/**
 * Class AuthDataProvider
 */
class AuthDataProvider extends DataProviderAbstract implements DataProviderOrder, DataProviderBasket
{
    /**
     * {@inheritdoc}
     */
    public function getDataFromBasket(string $paymentCode, Basket $basket, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);

        $requestParams['basket'] = $this->getBasketData($basket);

        $requestParams['basketItems'] = $this->getCartItemData($basket);
        $requestParams['shippingAddress'] = $this->addressHelper->getAddressData(
            $this->addressHelper->getBasketShippingAddress($basket)
        );
        $billingAddress = $this->addressHelper->getBasketBillingAddress($basket);
        $requestParams['billingAddress'] = $this->addressHelper->getAddressData(
            $billingAddress
        );
        $requestParams['customer'] = $this->getCustomerData($billingAddress, $basket->customerId);

        if ($this->paymentHasAccount($paymentCode)) {
            $requestParams['account'] = $this->getAccountData();
        }
        $requestParams['shippingProvider'] = $this->getShippingProvider($basket->shippingProviderId);
        $this->validator->validate($requestParams);

        return $requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);

        $requestParams['basket'] = $this->getBasketDataFromOrder($order);

        $requestParams['basketItems'] = $this->getOrderItemData($order);
        $requestParams['shippingAddress'] = $this->addressHelper->getAddressData(
            $this->addressHelper->getOrderShippingAddress($order)
        );
        $billingAddress = $this->addressHelper->getOrderBillingAddress($order);
        $requestParams['billingAddress'] = $this->addressHelper->getAddressData(
            $billingAddress
        );
        $requestParams['customer'] = $this->getCustomerData($billingAddress, $order->ownerId);

        if ($this->paymentHasAccount($paymentCode)) {
            $requestParams['account'] = $this->getAccountData();
        }

        $this->validator->validate($requestParams);

        return $requestParams;
    }

}
