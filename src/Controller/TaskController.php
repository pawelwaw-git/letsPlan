<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TaskDto;
use App\Repository\TaskCalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TaskController extends AbstractController
{
    #[Route('/tasks/{id}', name: 'update_task', requirements: ['id' => '^[1-9][0-9]*$'], methods: ['PATCH'])]
    #[ParamConverter('task', options: ['strict' => true], converter: 'fos_rest.request_body')]
    public function update(
        int $id,
        TaskDto $task,
        ConstraintViolationListInterface $validationErrors,
        TaskCalendarRepository $taskCalendarRepository,
    ): JsonResponse {
        if ($validationErrors->count() > 0) {
            return $this->json($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $taskCalendar = $taskCalendarRepository->find($id);

        if ($taskCalendar === null) {
            throw new NotFoundHttpException();
        }

        $taskCalendar->setIsDone($task->status);
        $taskCalendarRepository->flush();

        return new JsonResponse([], 204);
    }

    //    public function list(): JsonResponse
    //    {
    //        // TODO implement
    //        $this->json();
    //    }
    //
    //    public function single(): JsonResponse
    //    {
    // //        TODO implement
    //    }
    //
}
