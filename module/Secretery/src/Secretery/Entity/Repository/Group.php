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

namespace Secretery\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group Repository
 *
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Group extends EntityRepository
{
    /**
     * @param  int $groupId
     * @param  int $userId
     * @return bool
     */
    public function checkGroupMembership($groupId, $userId)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('u.id')
            ->join('g.users', 'u')
            ->where('g.id = :groupId')
            ->andWhere('u.id = :userId')
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getOneOrNullResult();
        if (empty($result)) {
            return false;
        }
        return true;
    }

    /**
     * @param  int $groupId
     * @return \Secretery\Entity\Group
     */
    public function fetchGroup($groupId)
    {
        return $this->findOneBy(array('id' => $groupId));
    }

    /**
     * @param  int $groupId
     * @param  int $userId
     * @return array
     */
    public function fetchGroupMembers($groupId, $userId = null)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('u.id, u.displayName, u.email')
            ->join('g.users', 'u')
            ->where('g.id = :groupId')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->setParameter('groupId', $groupId);

        if (!empty($userId)) {
            $qb->andWhere('u.id != :userId')
                ->setParameter('userId', $userId);
        }

        $result = $qb->getQuery()->getArrayResult();
        $users  = array();
        foreach ($result as $user) {
            $users[] = $user;
        }

        return $users;
    }

    /**
     * @param  int $groupId
     * @return array
     */
    public function fetchGroupMemberIds($groupId)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('u.id')
            ->join('g.users', 'u')
            ->where('g.id = :groupId')
            ->setParameter('groupId', $groupId);

        $result = $qb->getQuery()->getArrayResult();
        $ids    = array();
        foreach ($result as $id) {
            array_push($ids, $id['id']);
        }

        return $ids;
    }

    /**
     * @param  int $userId
     * @return ArrayCollection
     */
    public function fetchUserGroups($userId)
    {
        $qb = $this->createQueryBuilder('g')
            ->join('g.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->addOrderBy('g.name', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
