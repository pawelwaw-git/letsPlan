<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TaskDto;
use App\Entity\TaskCalendar;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'update_task', methods: ['PATCH'])]
    #[ParamConverter('task', converter: 'fos_rest.request_body')]
    public function update(
        TaskDto $task,
        ConstraintViolationListInterface $validationErrors,
        EntityManagerInterface $entity_manager
    ): JsonResponse {
        if ($validationErrors->count() > 0) {
            return $this->json($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        // TODO this logic should extracted to service probably, but this is a simple crud
        $task_calendar = $entity_manager->getRepository(TaskCalendar::class)->find($task->id);
        $task_calendar->setIsDone($task->status);
        $entity_manager->flush();

        return new JsonResponse([]);
    }

    //    show list of tasks with pagination and filters - method list() probably

    //    TODO zerknać dalej i dokończyć jeszcze to api.

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
    //    public function create(): JsonResponse
    //    {
    //
    //    }
    //
    //    public function delete(): JsonResponse
    //    {
    //
    //    }
}
