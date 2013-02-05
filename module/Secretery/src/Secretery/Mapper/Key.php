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

use Secretery\Entity\Key as KeyEntity;
use Secretery\Entity\User as UserEntity;

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
class Key extends BaseMapper
{
    /**
     * @param  \Secretery\Entity\Key  $key
     * @param  \Secretery\Entity\User $user
     * @return \Secretery\Entity\Key
     */
    public function saveKey(KeyEntity $key, UserEntity $user, $pubKey)
    {
        $key->setPubKey($pubKey);
        $key->setUserId($user->getId());
        $key->setUser($user);
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
