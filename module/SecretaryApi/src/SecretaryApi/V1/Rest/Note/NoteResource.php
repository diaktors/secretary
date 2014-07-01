<?php
namespace SecretaryApi\V1\Rest\Note;

use Secretary\Entity;
use Secretary\Service;
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

        if (empty($data['userId']) || !is_numeric($data['userId'])) {
            throw new \InvalidArgumentException('Please provide a valid "userId" value.', 400);
        }

        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        /** @var Service\Note $noteService */
        $noteService = $this->getServiceManager()->get('note-service');

        /** @var Entity\User $user */
        $user = $userService->getUserById($data['userId']);
        if ($user === null) {
            throw new \InvalidArgumentException('Given user could not been found.', 404);
        }

        $note = new Entity\Note;
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($data, $note);

        if ($data['private'] == 0) {
            $groupId = $data['groupId'];
            /** @var Service\Group $groupService */
            $groupService = $this->getServiceManager()->get('group-service');
            if (empty($groupId) || !is_numeric($groupId)) {
                throw new \InvalidArgumentException('Please provide a valid "groupId" value.', 400);
            }

            $groupMemberCheck = $groupService->checkGroupMembership($groupId, $data['userId']);
            if (false === $groupMemberCheck) {
                $this->events->trigger('logViolation', __METHOD__ . '::l42', array(
                    'message' => sprintf('User: %s wants to add note for GroupID: %s',
                        $user->getEmail(),
                        $groupId
                    )
                ));
                throw new \InvalidArgumentException('You are not allowed to add notes to this group.', 403);
            }

            if (empty($data['members'])) {
                throw new \InvalidArgumentException('Please provide a valid "members" value.', 400);
            }

            // @todo we need to add a param to not enccrypt already encrypted stuff
            $note = $noteService->saveGroupNote(
                $user,
                $note,
                $groupId,
                $data['members']
            );
        } else {
            if (empty($data['eKey'])) {
                throw new \InvalidArgumentException('Please provide a "eKey" value.', 400);
            }

            $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_PRE, $note);

            $this->getObjectManager()->persist($note);
            $this->getObjectManager()->flush();

            $note = $noteService->saveUser2NoteRelation($user, $note, $data['eKey']);
        }

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_CREATE_POST, $note);

        return $note;
    }

    /**
     * Fetch a note
     *
     * @param int $noteId
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function fetch($noteId)
    {
        $userId = $this->event->getRouteMatch()->getParam('user_id');
        if (empty($noteId) || empty($userId) || !is_numeric($noteId) || !is_numeric($userId)) {
            throw new \InvalidArgumentException('Please follow get route /note/:note_id/user/:user_id.');
        }

        /** @var Service\Note $noteService */
        $noteService = $this->getServiceManager()->get('note-service');
        $data = $noteService->fetchNoteWithUserData($noteId, $userId);

        $this->triggerDoctrineEvent(DoctrineResourceEvent::EVENT_FETCH_POST, $data);

        return $data;
    }
}
