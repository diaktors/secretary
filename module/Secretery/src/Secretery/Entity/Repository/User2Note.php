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
}
