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
 * @category Mapper
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Mapper;

use Doctrine\ORM\EntityManager;
use Secretery\Entity\Key as KeyEntity;

/**
 * Key Mapper
 *
 * @category Mapper
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Key
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return $em
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param  \Secretery\Entity\Key $key
     * @return \Secretery\Entity\Key
     */
    public function saveKey(KeyEntity $key)
    {
        $this->em->persist($key);
        $this->em->flush();
        return $key;
    }

    /**
     * @param $id
     * @return \Secretery\Entity\Key
     */
    public function fetchKey($id)
    {
        return $this->em->getRepository('Secretery\Entity\Key')->find($id);
    }
}
