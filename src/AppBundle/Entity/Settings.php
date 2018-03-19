<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="refreshInterval", type="integer")
     */
    private $refreshInterval;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set refreshInterval
     *
     * @param integer $refreshInterval
     *
     * @return Settings
     */
    public function setRefreshInterval($refreshInterval)
    {
        $this->refreshInterval = $refreshInterval;

        return $this;
    }

    /**
     * Get refreshInterval
     *
     * @return int
     */
    public function getRefreshInterval()
    {
        return $this->refreshInterval;
    }
}
