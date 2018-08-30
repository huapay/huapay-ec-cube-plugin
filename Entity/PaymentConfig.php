<?php

namespace Plugin\HuaPayPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentConfig
 */
class PaymentConfig extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $api_token;

    /**
     * @var integer
     */
    private $is_testing;

    /**
     * @var string
     */
    private $reference_prefix;


    /**
     * Set id
     *
     * @param integer $id
     * @return PaymentConfig
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
     * Set api_token
     *
     * @param string $apiToken
     * @return PaymentConfig
     */
    public function setApiToken($apiToken)
    {
        $this->api_token = $apiToken;

        return $this;
    }

    /**
     * Get api_token
     *
     * @return string 
     */
    public function getApiToken()
    {
        return $this->api_token;
    }

    /**
     * Set is_testing
     *
     * @param integer $isTesting
     * @return PaymentConfig
     */
    public function setIsTesting($isTesting)
    {
        $this->is_testing = $isTesting;

        return $this;
    }

    /**
     * Get is_testing
     *
     * @return integer 
     */
    public function getIsTesting()
    {
        return $this->is_testing;
    }

    /**
     * Set reference_prefix
     *
     * @param string $referencePrefix
     * @return PaymentConfig
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
