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
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 */

namespace Secretery\Service;

use Secretery\Entity\User as UserEntity;

/**
 * User Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 */
class User extends Base
{
    /**
     * @param  $id
     * @return \Secretery\Entity\User
     */
    public function getUserById($id)
    {
        return $this->getUserRepository()->find($id);
    }

    /**
     * @param  \Zend\EventManager\Event $e
     * @return void
     * @throws \LogicException If needed role can not be found
     */
    public function saveUserRole(\Zend\EventManager\Event $e)
    {
        $roleRecord = $this->getRoleRepository()->findOneBy(array('roleId' => 'user'));
        if (empty($roleRecord)) {
            throw new \LogicException('Roles are missing, please configure them');
        }
        $user = $e->getParam('user');
        $user->addRole($roleRecord);
        $this->em->persist($user);
        $this->em->flush();

        $this->events->trigger('logInfo', 'Registration', array(
            'message' => sprintf('User: %s', $user->getEmail())
        ));
        $this->events->trigger('sendMail', 'Registration', array(
            'user' => $user
        ));
        return;
    }

    /**
     * @param  \Secretery\Entity\User $user
     * @param  array                  $values
     * @return \Secretery\Entity\User
     */
    public function updateUserSettings(UserEntity $user, array $values)
    {
        $user->setDisplayName($values['display_name'])
            ->setLanguage($values['language'])
            ->setNotifications($values['notifications']);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @param  \Secretery\Entity\User $user
     * @return void
     * @throws \LogicException If needed role can not be found
     */
    public function updateUserToKeyRole(UserEntity $user)
    {
        $roleRecord = $this->getRoleRepository()->findOneBy(array('roleId' => 'keyuser'));
        if (empty($roleRecord)) {
            throw new \LogicException('Roles are missing, please configure them');
        }
        $user->getRoles()->clear();
        $user->addRole($roleRecord);
        $this->em->persist($user);
        $this->em->flush();
        return;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRoleRepository()
    {
        return $this->em->getRepository('Secretery\Entity\Role');
    }

    /**
     * @return \Secretery\Entity\Repository\User
     */
    protected function getUserRepository()
    {
        return $this->em->getRepository('Secretery\Entity\User');
    }
}