<?php
/**
 * Wesrc Copyright 2013
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Wesrc UG ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by WsSrc. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.wesrc.com/company/terms
 *
 * PHP Version 5
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Service;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Base for Service classes
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Base implements EventManagerAwareInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $evm;

    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $events;

    /**
     * @param  \Doctrine\ORM\EntityManager $em
     * @return self
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * @param  EventManagerInterface $eventManager
     * @return self
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array($this->eventIdentifier));
        $this->evm    = $eventManager;
        $this->events = $eventManager;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->evm;
    }

}
