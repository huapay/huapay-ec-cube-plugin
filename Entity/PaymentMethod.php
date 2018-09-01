<?php

namespace Plugin\HuaPayPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentMethod
 */
class PaymentMethod extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $plugin_payment_id;

    /**
     * @var integer
     */
    private $payment_id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $is_enabled;


    /**
     * Set id
     *
     * @param integer $id
     * @return PaymentMethod
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set plugin_payment_id
     *
     * @param integer $pluginPaymentId
     * @return PaymentMethod
     */
    public function setPluginPaymentId($pluginPaymentId)
    {
        $this->plugin_payment_id = $pluginPaymentId;

        return $this;
    }

    /**
     * Get plugin_payment_id
     *
     * @return integer 
     */
    public function getPluginPaymentId()
    {
        return $this->plugin_payment_id;
    }

    /**
     * Set payment_id
     *
     * @param integer $paymentId
     * @return PaymentMethod
     */
    public function setPaymentId($paymentId)
    {
        $this->payment_id = $paymentId;

        return $this;
    }

    /**
     * Get payment_id
     *
     * @return integer 
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PaymentMethod
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set is_enabled
     *
     * @param integer $isEnabled
     * @return PaymentMethod
     */
    public function setIsEnabled($isEnabled)
    {
        $this->is_enabled = $isEnabled;

        return $this;
    }

    /**
     * Get is_enabled
     *
     * @return integer 
     */
    public function getIsEnabled()
    {
        return $this->is_enabled;
    }
}
