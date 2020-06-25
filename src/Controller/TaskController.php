<?php

/**
 * (c) Adrien PIERRARD
 */

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController.
 */
class TaskController extends AbstractController
{
    /**
     * Show the Tasks list.
     *
     * @return Response
     *
     * @Route("/tasks", name="task_list")
     */
    public function tasksList(): Response
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('App:Task')
            ->findAll()
        ;

        return $this->render(
            'task/list.html.twig',
            ['tasks' => $tasks]
        );
    }

    /**
     * Add a new Task.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/tasks/create", name="task_create")
     */
    public function createTask(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($task);
            $manager->flush();

            $this->addFlash(
                'success',
                'La tâche a été bien été ajoutée.'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Update a Task.
     *
     * @param Task    $task
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editTask(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'La tâche a bien été modifiée.'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/edit.html.twig',
            ['form' => $form->createView(), 'task' => $task]
        );
    }

    /**
     * Toggle a Task to do as Task done.
     *
     * @param Task $task
     *
     * @return Response
     *
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTask(Task $task): Response
    {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash(
            'success',
            sprintf(
                'La tâche %s a bien été marquée comme faite.',
                $task->getTitle()
            )
        );

        return $this->redirectToRoute('task_list');
    }

    /**
     * Delete a Task.
     *
     * @param Task $task
     *
     * @return Response
     *
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTask(Task $task): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($task);
        $manager->flush();

        $this->addFlash(
            'success',
            'La tâche a bien été supprimée.'
        );

        return $this->redirectToRoute('task_list');
    }
}
