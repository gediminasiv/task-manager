<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskLog;
use AppBundle\Form\TaskEnterType;
use AppBundle\Form\PasswordChangeType;
use AppBundle\Form\TaskDelayType;

class ArchiveController extends Controller {
    /**
     * Lists all archive entities.
     *
     * @Route("/dashboard/archive", name="archive_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Task')->findBy(['archived' => true]);

        return $this->render('archive/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Lists all archive entities.
     *
     * @Route("/dashboard/archive/edit/{id}", name="archive_edit")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Task $task)
    {
        $statusBefore = $task->getStatus();

        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('AppBundle\Form\ArchiveEditType', $task);

        $taskLogsRepository = $this->getDoctrine()->getManager()->getRepository(TaskLog::class);

        $taskLogs = $taskLogsRepository->findBy([
            'task' => $task
        ], [
            'created' => 'DESC'
        ]);


        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $translator = $this->get('translator');

            if ($task->getStatus() != $statusBefore) {
                $editTask = $this->getDoctrine()->getManager()->getRepository(Task::class)->find($task->getId());
                $editTask->setArchived(false);

                $this->getDoctrine()->getManager()->persist($editTask);
                $this->getDoctrine()->getManager()->flush();

                $task = $editTask;
            }

            $request->getSession()->getFlashBag()->add('task_success', $translator->trans('information_updated_successfully'));
        }

        return $this->render('archive/edit.html.twig', [
            'task' => $task,
            'edit_form' => $editForm->createView(),
            'taskLogs' => $taskLogs
        ]);
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/dashboard/archive/delete/{id}", name="archive_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $em = $this->task()->getManager();
        $em->remove($client);
        $em->flush();

        return $this->redirectToRoute('archive_list');
    }
}
