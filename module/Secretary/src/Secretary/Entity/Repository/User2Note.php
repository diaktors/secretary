<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5
 *
 * @category Repository
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * User2Note Repository
 *
 * @category Repository
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */
class User2Note extends EntityRepository
{
    /**
     * @param  int $userId
     * @param  int $noteId
     * @return \Secretary\Entity\User2Note
     */
    public function fetchUserNote($userId, $noteId)
    {
        return $this->findOneBy(array('userId' => $userId, 'noteId' => $noteId));
    }

    /**s
     * @param  int $noteId
     * @return bool
     */
    public function removeUsersFromNote($noteId)
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
     * @return \Secretary\Entity\User2Note
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
     * @return \Secretary\Entity\User2Note
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
