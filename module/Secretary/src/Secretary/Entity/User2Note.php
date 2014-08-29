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

/**
 * User2Note Entity
 *
 * @ORM\Table(name="user2note")
 * @ORM\Entity(repositoryClass="Secretary\Entity\Repository\User2Note")
 */
class User2Note //implements InputFilterAwareInterface
{
    /**
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     */
    protected $userId;

    /**
     * @ORM\Column(name="note_id", type="integer")
     * @ORM\Id
     */
    protected $noteId;

    /**
     * @ORM\Column(name="ekey", type="text")
     */
    protected $eKey;

    /**
     * @ORM\Column(name="read_permission", type="boolean")
     */
    protected $readPermission = false;

    /**
     * @ORM\Column(name="write_permission", type="boolean")
     */
    protected $writePermission = false;

    /**
     * @ORM\Column(name="owner", type="boolean")
     */
    protected $owner = false;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user2note", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Note", inversedBy="user2note", cascade={"persist"})
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $note;

    /**
     * @param  Note $note
     * @return self
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return Note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param  int $noteId
     * @return self
     */
    public function setNoteId($noteId)
    {
        $this->noteId = $noteId;
        return $this;
    }

    /**
     * @return int
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * @param  User $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param  int $userId
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param  string $eKey
     * @return self
     */
    public function setEKey($eKey)
    {
        $this->eKey = $eKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getEKey()
    {
        return $this->eKey;
    }

    /**
     * @param  bool $owner
     * @return self
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return bool
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param  bool $readPermission
     * @return self
     */
    public function setReadPermission($readPermission)
    {
        $this->readPermission = $readPermission;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReadPermission()
    {
        return $this->readPermission;
    }

    /**
     * @param  bool $writePermission
     * @return self
     */
    public function setWritePermission($writePermission)
    {
        $this->writePermission = $writePermission;
        return $this;
    }

    /**
     * @return bool
     */
    public function getWritePermission()
    {
        return $this->writePermission;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
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
        unset($array['user']);
        unset($array['note']);
        return $array;
    }

}