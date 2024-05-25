<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TaskDto;
use App\Repository\TaskCalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
    #[Route('/tasks/{id}', name: 'update_task', requirements: ['id' => '^[1-9][0-9]*$'], methods: ['PATCH'])]
    public function update(
        int $id,
        #[MapRequestPayload]
        TaskDto $task,
        ValidatorInterface $validator,
        TaskCalendarRepository $taskCalendarRepository,
    ): JsonResponse {
        $taskCalendar = $taskCalendarRepository->find($id);

        if ($taskCalendar === null) {
            throw new NotFoundHttpException();
        }

        $taskCalendar->setIsDone($task->status);
        $taskCalendarRepository->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws \JsonException
     */
    #[Route('tasks', name: 'get_tasks', methods: 'GET')]
    public function list(TaskCalendarRepository $taskCalendarRepository, Request $request): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $per_page = (int) $request->query->get('per_page', 10);
        $sort = $request->query->get('sort', null);
        $filter = $request->query->get('filter', null);

        $tasks = $taskCalendarRepository->getPaginatedWithFilterAndSort($page, $per_page, $sort, $filter);

        return $this->json(
            $tasks,
            Response::HTTP_OK,
            [],
            [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
            ]
        );
    }

    #[Route('tasks/{id}', name: 'task_single', requirements: ['id' => '^[1-9][0-9]*$'], methods: 'GET')]
    public function single(
        int $id,
        TaskCalendarRepository $taskCalendarRepository
    ): JsonResponse {
        $task = $taskCalendarRepository->find($id);

        if ($task === null) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse([
            'id' => $task->getId(),
            'goal_id' => $task->getGoal()->getId(),
            'date' => $task->getDate()->format('Y-m-d'),
            'is_done' => $task->isIsDone(),
        ], Response::HTTP_OK);
    }
}
