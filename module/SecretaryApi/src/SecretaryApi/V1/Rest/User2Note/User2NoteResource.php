<?php
namespace SecretaryApi\V1\Rest\User2Note;

use Secretary\Entity;
use Secretary\Service;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;

class User2NoteResource extends DoctrineResource
{
    /**
     * Fetch a user2note
     *
     * @param int $noteId
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function fetch($noteId)
    {
        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        $user = $userService->getUserByMail($this->getIdentity()->getName());

        $note = $this->fetchUser2NoteRecord($user->getId(), $noteId);
        if ($note === null) {
            throw new \InvalidArgumentException('Note does not exists.', 404);
        }

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_FETCH_POST, $note);

        return $note;
    }

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

        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        /** @var Service\Note $noteService */
        $noteService = $this->getServiceManager()->get('note-service');

        $note = $noteService->fetchNote($data['noteId']);
        if ($note === null) {
            throw new \InvalidArgumentException('Note does not exists.', 404);
        }

        $user = $userService->getUserById($data['userId']);
        if ($note === null) {
            throw new \InvalidArgumentException('Note does not exists.', 404);
        }

        $user2NoteCheck = $this->fetchUser2NoteRecord($user->getId(), $note->getId());
        if ($user2NoteCheck !== null) {
            throw new \InvalidArgumentException('User2Note does already exists.', 400);
        }


        $user2Note = new Entity\User2Note();
        $user2Note->setNote($note)
            ->setUser($user);

        $hydrator = $this->getHydrator();
        $hydrator->hydrate($data, $user2Note);

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_PRE, $user2Note);

        $this->getObjectManager()->persist($user2Note);
        $this->getObjectManager()->flush();

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_POST, $user2Note);

        return $user2Note;
    }

    /**
     * @param int $userId
     * @param int $noteId
     * @return Entity\User2Note
     */
    private function fetchUser2NoteRecord($userId, $noteId)
    {
        return $this->getObjectManager()->getRepository('Secretary\Entity\User2Note')->findOneBy([
            'userId' => $userId,
            'noteId' => $noteId
        ]);
    }
}
