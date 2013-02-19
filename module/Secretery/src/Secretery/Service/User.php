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

namespace Secretery\Service;

/**
 * User Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class User extends Base
{
    /**
     * @param  \Zend\EventManager\Event $e
     * @return void
     * @throws \LogicException If needed role can not be found
     */
    public function saveUserRole(\Zend\EventManager\Event $e)
    {
        $roleRecord = $this->em->getRepository('Secretery\Entity\Role')
            ->findOneBy(array('roleId' => 'user'));
        if (empty($roleRecord)) {
            throw new \LogicException('Roles are missing, please configure them');
        }
        $user = $e->getParam('user');
        $user->addRole($roleRecord);
        $this->em->persist($roleRecord);
        $this->em->flush();
        return;
    }
}
