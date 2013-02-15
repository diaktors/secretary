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

use Secretery\Entity\User;
use Secretery\Entity\Group as GroupEntity;

/**
 * Group Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Group extends Base
{
    /**
     * @param  int $userId
     * @param  int $groupId
     * @return bool
     * @throws \InvalidArgumentException If GroupID is invalid
     * @throws \InvalidArgumentException If UserID is invalid
     */
    public function checkGroupMembership($groupId, $userId)
    {
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        return $this->em->getRepository('Secretery\Entity\Group')
            ->checkGroupMembership($groupId, $userId);
    }

    /**
     * @param  int $groupId
     * @return GroupEntity
     * @throws \InvalidArgumentException If GroupID is invalid
     */
    public function fetchGroup($groupId)
    {
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        return $this->em->getRepository('Secretery\Entity\Group')
            ->find($groupId);
    }

    /**
     * @param  int $groupId
     * @param  int $userId
     * @return array
     * @throws \InvalidArgumentException If GroupID is invalid
     */
    public function fetchGroupMembers($groupId, $userId = null)
    {
        if (empty($groupId) || !is_numeric($groupId)) {
            throw new \InvalidArgumentException('Please provide a valid GroupID');
        }
        return $this->em->getRepository('Secretery\Entity\Group')
            ->fetchGroupMembers($groupId, $userId);
    }

    /**
     * @param  int $userId
     * @return array
     * @throws \InvalidArgumentException If UserID is invalid
     */
    public function fetchUserGroups($userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        return $this->em->getRepository('Secretery\Entity\Group')
            ->fetchUserGroups($userId);
    }

    /**
     * @param  User   $user
     * @param  string $groupname
     * @return GroupEntity
     */
    public function addUserGroup(User $user, $groupname)
    {
        $groupRecord = new GroupEntity();
        $groupRecord->setName($groupname)
            ->setOwner($user->getId());
        $user->addGroup($groupRecord);
        $this->em->persist($user);
        $this->em->flush();
        return $groupRecord;
    }


    /**
     * @param  GroupEntity $group
     * @param  int         $userId
     * @return User
     * @throws \InvalidArgumentException If UserID is invalid
     * @throws \LogicException           If User could not been found
     */
    public function addGroupMember(GroupEntity $group, $userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please provide a valid UserID');
        }
        $userRecord = $this->em->getRepository('Secretery\Entity\User')->find($userId);
        if (empty($userRecord)) {
            throw new \LogicException('User could not been found');
        }
        $userRecord->addGroup($group);
        $this->em->persist($userRecord);
        $this->em->flush();
        return $userRecord;
    }

    /**
     * @param  User        $user
     * @param  GroupEntity $group
     * @return User
     */
    public function deleteUserGroup(User $user, GroupEntity $group)
    {
        $user->getGroups()->removeElement($group);
        $this->em->persist($user);
        $this->em->flush();
        if ($group->getUsers()->count() == 0) {
            $this->em->remove($group);
            $this->em->flush();
        }
        return $user;
    }

    /**
     * @param  GroupEntity $group
     * @param  string      $groupname
     * @return GroupEntity
     */
    public function updateGroup(GroupEntity $group, $groupname)
    {
        $group->setName($groupname);
        $this->em->persist($group);
        $this->em->flush();
        return $group;
    }

}
