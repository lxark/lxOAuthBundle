<?php

namespace LX\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OAuthRemoteServer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="LX\OAuthBundle\Repository\OAuthRemoteServerRepository")
 */
class OAuthRemoteServer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="baseUrl", type="string", length=255)
     */
    private $baseUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_key", type="string", length=255)
     */
    private $consumerKey;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_secret", type="string", length=255)
     */
    private $consumerSecret;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


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
     * Set name
     *
     * @param string $name
     * @return OAuthRemoteServer
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
     * Set baseUrl
     *
     * @param string $baseUrl
     * @return OAuthRemoteServer
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    
        return $this;
    }

    /**
     * Get baseUrl
     *
     * @return string 
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return OAuthRemoteServer
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return OAuthRemoteServer
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status name
     *
     * @param array $statuses array of statuses names
     *
     * @return mixed
     */
    public function getStatusName($statuses = array())
    {

        return (isset($statuses[$this->status]))
            ? $statuses[$this->status]
            : $this->status;
    }

    /**
     * Set consumerKey
     *
     * @param string $consumerKey
     * @return OAuthRemoteServer
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    
        return $this;
    }

    /**
     * Get consumerKey
     *
     * @return string 
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * Set consumerSecret
     *
     * @param string $consumerSecret
     * @return OAuthRemoteServer
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    
        return $this;
    }

    /**
     * Get consumerSecret
     *
     * @return string 
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return OAuthRemoteServer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return OAuthRemoteServer
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
