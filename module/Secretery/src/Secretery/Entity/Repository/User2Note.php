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
 * User2Note Repository
 *
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class User2Note extends EntityRepository
{
    /**
     * @param  int $userId
     * @param  int $noteId
     * @return \Secretery\Entity\User2Note
     */
    public function fetchUserNote($userId, $noteId)
    {
        return $this->findOneBy(array('userId' => $userId, 'noteId' => $noteId));
    }

    /**s
     * @param  int $noteId
     * @return bool
     */
    public function removeUserFromNote($noteId)
    {
        $qb = $this->createQueryBuilder('u2n')
                ->delete()
                ->where('u2n.noteId = :noteId')
                ->setParameter('noteId', $noteId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param  array $note
     * @param  int   $userId
     * @param  int   $groupOwnerId
     * @return \Secretery\Entity\User2Note
     */
    public function checkNoteOwnershipForLeavingUser(array $note, $userId, $groupOwnerId)
    {
        $user2noteRecord = $this->findOneBy(
            array('userId' => $userId, 'noteId' => $note['id'])
        );
        if (false == $note['owner']) {
            $this->getEntityManager()->remove($user2noteRecord);
            $this->getEntityManager()->flush();
            return;
        }
        $newUser2NoteOwnerRecord = $this->getNewNoteOwner($note['id'], $userId, $groupOwnerId);
        $newUser2NoteOwnerRecord->setOwner(true)
            ->setReadPermission(true)
            ->setWritePermission(true);
        $this->getEntityManager()->persist($newUser2NoteOwnerRecord);
        $this->getEntityManager()->remove($user2noteRecord);
        $this->getEntityManager()->flush();
        return;
    }

    /**
     * @param  int $noteId
     * @param  int $deleteUserId
     * @param  int $groupOwnerId
     * @return \Secretery\Entity\User2Note
     */
    protected function getNewNoteOwner($noteId, $deleteUserId, $groupOwnerId)
    {
        $user2noteOwnerRecord = $this->findOneBy(
            array('userId' => $groupOwnerId, 'noteId' => $noteId)
        );
        if (!empty($user2noteOwnerRecord)) {
            return $user2noteOwnerRecord;
        }
        $qb = $this->createQueryBuilder('u2n')
            ->where('u2n.noteId = :noteId')
            ->andWhere('u2n.userId != :userId')
            ->setParameter('noteId', $noteId)
            ->setParameter('userId', $deleteUserId)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
