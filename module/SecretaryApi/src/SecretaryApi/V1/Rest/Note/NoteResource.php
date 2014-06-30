<?php
namespace SecretaryApi\V1\Rest\Note;

use Secretary\Entity;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;

class NoteResource extends DoctrineResource
{
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data = (array) $data;

        $note = new Entity\Note;
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($data, $note);

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_PRE, $note);
        $this->getObjectManager()->persist($note);
        $this->getObjectManager()->flush();

        if (!empty($data['userId']) && is_numeric($data['userId'])) {
            /** @var Entity\User $user */
            $user = $this->getObjectManager()->getRepository('Secretary\Entity\User')->find($data['userId']);
            if ($user === null) {
                throw new \InvalidArgumentException('Given user could not been found.');
            }

            $user2Note = new Entity\User2Note();
            $user2Note->setUser($user)
                ->setUserId($user->getId())
                ->setNote($note)
                ->setNoteId($note->getId())
                ->setEkey($data['eKey'])
                ->setOwner(true)
                ->setReadPermission(true)
                ->setWritePermission(true);

            $note->addUser2Note($user2Note);

            $this->getObjectManager()->persist($note);
            $this->getObjectManager()->flush();
        }

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_POST, $note);

        return $note;
    }

    /**
     * Fetch a note
     *
     * @param int $noteId
     * @return ApiProblem|mixed
     */
    public function fetch($noteId)
    {
        $userId = $this->event->getRouteMatch()->getParam('user_id');
        if (empty($noteId) || empty($userId) || !is_numeric($noteId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please follow get route /note/:note_id/user/:user_id.');
        }

        $data = $this->getObjectManager()->getRepository($this->getEntityClass())
            ->fetchNoteWithUserData($noteId, $userId);
        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_FETCH_POST, $data);

        return $data;
    }
}
