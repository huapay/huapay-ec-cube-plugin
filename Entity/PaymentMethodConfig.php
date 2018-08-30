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
    private $enabled;

    /**
     * @var string
     */
    private $reference_prefix;


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
     * Set enabled
     *
     * @param integer $enabled
     * @return PaymentMethodConfig
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return integer 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set reference_prefix
     *
     * @param string $referencePrefix
     * @return PaymentMethodConfig
     */
    public function setReferencePrefix($referencePrefix)
    {
        $this->reference_prefix = $referencePrefix;

        return $this;
    }

    /**
     * Get reference_prefix
     *
     * @return string 
     */
    public function getReferencePrefix()
    {
        return $this->reference_prefix;
    }
}
