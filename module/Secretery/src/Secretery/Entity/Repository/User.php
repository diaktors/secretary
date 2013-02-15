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
     * @param  int $userId
     * @param  int $groupId
     * @return ArrayCollection
     */
    public function getSelectUser($userId, $groupId = null)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.id != :userId')
            ->addOrderBy('u.displayName', 'ASC')
            ->addOrderBy('u.email', 'ASC')
            ->setParameter('userId', $userId);
        if (!empty($groupId)) {
            $memberIds = $this->getEntityManager()->getRepository('Secretery\Entity\Group')
                ->fetchGroupMemberIds($groupId);
            $qb->andWhere($qb->expr()->notIn('u.id', $memberIds));
        }

        return $qb->getQuery()->getResult();
    }
}
