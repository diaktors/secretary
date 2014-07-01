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
 * @category Entity
 * @package  Secretary
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ZfcUser\Entity\UserInterface;
use BjyAuthorize\Provider\Role\ProviderInterface;

/**
 * User Entity
 *
 * @ORM\Table(
 *   name="user",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="username", columns={"username"}),
 *     @ORM\UniqueConstraint(name="email", columns={"email"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Secretary\Entity\Repository\User")
 */
class User implements UserInterface, ProviderInterface
{
    /**
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="display_name", type="string", length=50, nullable=true)
     */
    protected $displayName;

    /**
     * @ORM\Column(name="password", type="string", length=128)
     */
    protected $password;

    /**
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    protected $state;

    /**
     * @ORM\Column(name="language", type="string", length=5)
     */
    protected $language = 'en_US';

    /**
     * @ORM\Column(name="notifications", type="boolean")
     */
    protected $notifications = true;

    /**
     * @ORM\OneToOne(targetEntity="Key", mappedBy="user")
     */
    protected $key;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime")
     */
    protected $dateUpdated;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Secretary\Entity\Role", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="user2role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="User2Note", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $user2note;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="user2group",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $groups;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param  string $displayName
     * @return self
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param  int $state
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language.
     *
     * @param  string $language
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get notifications.
     *
     * @return bool
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set notifications.
     *
     * @param  bbol $notifications
     * @return self
     */
    public function setNotifications($notifications)
    {
        $this->notifications = (bool) $notifications;
        return $this;
    }

    /**
     * Get Date Created.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Get Date Updated.
     *
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Get role.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role to the user.
     *
     * @param  Role $role
     * @return void
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }

    /**
     * Get User2Note collection
     *
     * @return ArrayCollection
     */
    public function getUser2Note()
    {
        return $this->user2note;
    }

    /**
     * Add user 2 note relation
     *
     * @param  User2Note $user2note
     * @return $this
     */
    public function addUser2Note(User2Note $user2note)
    {
        $this->getUser2Note()->add($user2note);
        return $this;
    }

    /**
     * Get Key Relation
     *
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get groups.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add group relation
     *
     * @param  Group $group
     * @return $this
     */
    public function addGroup(Group $group)
    {
        $this->getGroups()->add($group);
        return $this;
    }


    /**
     * return void
     */
    public function __construct()
    {
        $this->groups    = new ArrayCollection();
        $this->roles     = new ArrayCollection();
        $this->user2note = new ArrayCollection();
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array                = get_object_vars($this);
        $array['dateCreated'] = $this->getDateCreated()->format('Y-m-d H:i:s');
        $array['dateUpdated'] = $this->getDateUpdated()->format('Y-m-d H:i:s');
        $array['role']        = $this->getRoles()->first()->getRoleId();
        unset($array['key']);
        unset($array['user2note']);
        unset($array['groups']);
        unset($array['roles']);
        unset($array['password']);
        return $array;
    }
}