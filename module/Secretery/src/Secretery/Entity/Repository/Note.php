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

/**
 * Note Repository
 *
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Note extends EntityRepository
{
    /**
     * @param  int $id
     * @return \Secretery\Entity\Note
     */
    public function fetchNote($id)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->addSelect('g')
            ->leftJoin('n.group', 'g')
            ->where('n.id = :noteId')
            ->setParameter('noteId', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param  int $noteId
     * @param  int $userId
     * @return \Secretery\Entity\Note
     */
    public function fetchNoteWithUserData($noteId, $userId)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->select(array('n.id', 'n.title', 'n.content', 'n.private', 'n.dateCreated', 'n.dateUpdated'))
            ->addSelect(array('u2n.owner', 'u2n.readPermission', 'u2n.writePermission', 'u2n.eKey'))
            ->leftJoin('n.user2note', 'u2n')
            ->where('n.id = :noteId')
            ->andWhere('u2n.userId = :userId')
            ->andWhere('u2n.noteId = :noteId')
            ->setParameter('noteId', $noteId)
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param  int $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchUserNotes($userId)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->select(array('n.id', 'n.title', 'n.content', 'n.private', 'n.dateCreated', 'n.dateUpdated'))
            ->addSelect(array('u2n.owner', 'u2n.readPermission', 'u2n.writePermission'))
            ->leftJoin('n.user2note', 'u2n')
            ->leftJoin('u2n.user', 'u')
            ->where('u2n.userId = :userId')
            ->andWhere('u.id = :userId')
            ->andWhere('n.private = :private')
            ->addOrderBy('n.title', 'ASC')
            ->setParameter('userId', $userId)
            ->setParameter('private', 1);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param  int $userId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function fetchGroupNotes($userId, $groupId = null)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->select(array('n.id', 'n.title', 'n.content', 'n.private', 'n.dateCreated', 'n.dateUpdated'))
            ->addSelect(array('u2n.owner', 'u2n.readPermission', 'u2n.writePermission'))
            ->addSelect(array('g.name as groupName', 'g.id as groupId'))
            ->leftJoin('n.user2note', 'u2n')
            ->leftJoin('u2n.user', 'u')
            ->leftJoin('n.group', 'g')
            ->where('u2n.userId = :userId')
            ->andWhere('u.id = :userId')
            ->andWhere('n.private = :private')
            ->addOrderBy('n.title', 'ASC')
            ->setParameter('userId', $userId)
            ->setParameter('private', 0);

        if (!empty($groupId) && is_numeric($groupId)) {
            $qb->andWhere('IDENTITY(n.group) = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $qb->getQuery()->getArrayResult();
    }
}
