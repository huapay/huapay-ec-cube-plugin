<?php

namespace Plugin\HuaPayPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 */
class Payment extends \Eccube\Entity\AbstractEntity
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
     * Set id
     *
     * @param integer $id
     * @return Payment
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
     * @return Payment
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
     * @return Payment
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
}
