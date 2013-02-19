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
 * User Repository
 *
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class User extends EntityRepository
{
    /**
     * @param  int $noteId
     * @param  int $groupId
     * @param  int $userId
     * @return array
     */
    public function fetchNoteGroupMembers($noteId, $groupId, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from($this->_entityName, 'u', 'u.id')
            ->select('u.id, u.displayName, u.email')
            ->join('u.groups', 'g')
            ->where('g.id = :groupId')
            ->andWhere('u.id != :userId')
            ->andWhere('EXISTS (SELECT u2n.noteId FROM Secretery\Entity\User2Note u2n WHERE u2n.userId = u.id AND u2n.noteId = :noteId)')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->addGroupBy('u.id')
            ->setParameter('groupId', $groupId)
            ->setParameter('noteId', $noteId)
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param  int $noteId
     * @param  int $groupId
     * @param  int $userId
     * @return array
     */
    public function fetchNoteGroupMembersUnselected($noteId, $groupId, $userId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from($this->_entityName, 'u', 'u.id')
            ->select('u.id, u.displayName, u.email')
            ->join('u.groups', 'g')
            ->where('g.id = :groupId')
            ->andWhere('u.id != :userId')
            ->andWhere('NOT EXISTS (SELECT u2n.noteId FROM Secretery\Entity\User2Note u2n WHERE u2n.userId = u.id AND u2n.noteId = :noteId)')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->addGroupBy('u.id')
            ->setParameter('noteId', $noteId)
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }
    /**
     * @param  int $userId
     * @param  int $groupId
     * @return ArrayCollection
     */
    public function getSelectUser($userId, $groupId = null)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.roles', 'r')
            ->where('u.id != :userId')
            ->andWhere('r.roleId = :role')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->setParameter('userId', $userId)
            ->setParameter('role', 'keyuser');
        if (!empty($groupId)) {
            $memberIds = $this->getEntityManager()->getRepository('Secretery\Entity\Group')
                ->fetchGroupMemberIds($groupId);
            $qb->andWhere($qb->expr()->notIn('u.id', $memberIds));
        }

        return $qb->getQuery()->getResult();
    }
}
