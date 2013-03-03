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
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 */

namespace Secretery\Entity\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

/**
 * Note Repository
 *
 * @category Repository
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
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
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function fetchUserNotes($userId)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->select(array('n.id', 'n.title', 'n.private', 'n.dateCreated', 'n.dateUpdated'))
            ->addSelect(array('u2n.owner', 'u2n.readPermission', 'u2n.writePermission'))
            ->leftJoin('n.user2note', 'u2n')
            ->leftJoin('u2n.user', 'u')
            ->where('u2n.userId = :userId')
            ->andWhere('u.id = :userId')
            ->andWhere('n.private = :private')
            ->setParameter('userId', $userId)
            ->setParameter('private', 1);

        return $qb;
    }

    /**
     * @param  int $userId
     * @param  int $groupId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function fetchGroupNotes($userId, $groupId = null)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->select(array('n.id', 'n.title', 'n.private', 'n.dateCreated', 'n.dateUpdated'))
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

        return $qb;
    }
}
