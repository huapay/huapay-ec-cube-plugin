<?php

namespace Plugin\HuaPayPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentMethodConfig
 */
class PaymentMethodConfig extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $payment_config_id;

    /**
     * @var integer
     */
    private $eccube_payment_id;

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
     * @return PaymentMethodConfig
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
     * Set payment_config_id
     *
     * @param integer $paymentConfigId
     * @return PaymentMethodConfig
     */
    public function setPaymentConfigId($paymentConfigId)
    {
        $this->payment_config_id = $paymentConfigId;

        return $this;
    }

    /**
     * Get payment_config_id
     *
     * @return integer 
     */
    public function getPaymentConfigId()
    {
        return $this->payment_config_id;
    }

    /**
     * Set eccube_payment_id
     *
     * @param integer $eccubePaymentId
     * @return PaymentMethodConfig
     */
    public function setEccubePaymentId($eccubePaymentId)
    {
        $this->eccube_payment_id = $eccubePaymentId;

        return $this;
    }

    /**
     * Get eccube_payment_id
     *
     * @return integer 
     */
    public function getEccubePaymentId()
    {
        return $this->eccube_payment_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PaymentMethodConfig
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
     * @return PaymentMethodConfig
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
