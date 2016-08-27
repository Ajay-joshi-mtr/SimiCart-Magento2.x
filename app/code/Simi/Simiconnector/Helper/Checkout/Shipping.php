<?php

/**
 * Shipping helper
 */
namespace Simi\Simiconnector\Helper\Checkout;


class Shipping extends \Simi\Simiconnector\Helper\Data
{
    
    protected function _getCheckoutSession() {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }

    public function _getOnepage() {
        return $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage');
    }
    
    protected function _getCart() {
        return $this->_objectManager->get('Magento\Checkout\Model\Cart');
    }
    
    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }

    public function saveShippingMethod($method_code) {
        if (!isset($method_code->method))
            return;
        $method = $method_code->method;
        $cartExtension = $this->_getQuote()->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->_objectManager->get('Magento\Quote\Api\Data\CartExtension');
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            $shippingAssignment = $this->_objectManager->get('Magento\Quote\Model\ShippingAssignmentFactory');
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->getShippingFactory()->create();
        }
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        $quote =  $this->_getQuote()->setExtensionAttributes($cartExtension);
        
        $this->_objectManager->get('Magento\Quote\Api\CartRepositoryInterface')->save($quote);
    }

    public function getAddress() {
        return $this->_getCheckoutSession()->getShippingAddress();
    }

    public function getShippingPrice($price, $flag) {
        return $this->_objectManager->get('Simi\Simiconnector\Helper\Price')->convertPrice($this->_objectManager->get('Magento\Tax\Helper\Data')->getShippingPrice($price, $flag, $this->getAddress()), false);
    }

    public function getMethods() {
        $shipping = $this->_getCheckoutSession()->getQuote()->getShippingAddress();
        $methods = $shipping->getGroupedAllShippingRates();

        $list = array();
        foreach ($methods as $_ccode => $_carrier) {
            foreach ($_carrier as $_rate) {
                if ($_rate->getData('error_message') != NULL) {
                    continue;
                }
                $select = false;
                if ($shipping->getShippingMethod() != null && $shipping->getShippingMethod() == $_rate->getCode()) {
                    $select = true;
                }

                $s_fee = $this->getShippingPrice($_rate->getPrice(), $this->_objectManager->get('Magento\Tax\Helper\Data')->displayShippingPriceIncludingTax());
                $s_fee_incl = $this->getShippingPrice($_rate->getPrice(), true);
                
                if ($this->_objectManager->get('Magento\Tax\Helper\Data')->displayShippingBothPrices() && $s_fee != $s_fee_incl) {
                    $list[] = array(
                        's_method_id' => $_rate->getId(),
                        's_method_code' => $_rate->getCode(),
                        's_method_title' => $_rate->getCarrierTitle(),
                        's_method_fee' => $this->_objectManager->get('Simi\Simiconnector\Helper\Price')->convertPrice(floatval($s_fee), false),
                        's_method_fee_incl_tax' => $s_fee_incl,
                        's_method_name' => $_rate->getMethodTitle(),
                        's_method_selected' => $select,
                        's_carrier_code'=> $_rate->getCarrier(),
                        's_carrier_title'=> $_rate->getCarrierTitle(),
                    );
                } else {
                    $list[] = array(
                        's_method_id' => $_rate->getId(),
                        's_method_code' => $_rate->getCode(),
                        's_method_title' => $_rate->getCarrierTitle(),
                        's_method_fee' => $s_fee,
                        's_method_name' => $_rate->getMethodTitle(),
                        's_method_selected' => $select,
                        's_carrier_code'=> $_rate->getCarrier(),
                        's_carrier_title'=> $_rate->getCarrierTitle(),
                    );
                }
            }
        }
        return $list;
    }

}
