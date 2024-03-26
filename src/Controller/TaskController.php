<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    #[Route('/tasks/{id}', name: 'update_task', methods: ['PATCH'])]
    public function update(): JsonResponse
    {
        // change status - przekazać z body
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
////        TODO implement
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