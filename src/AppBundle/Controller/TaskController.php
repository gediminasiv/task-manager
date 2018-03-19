<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\Client;
use AppBundle\Entity\TaskLog;
use AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


class TaskController extends Controller
{
    /**
     * Creates a new task entity.
     *
     * @Route("/dashboard/task/new", name="task_new")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $task = new Task();
        $task->setCreated(new \DateTime());
        $form = $this->createForm('AppBundle\Form\TaskType', $task);

        $form->remove('arrivalTime');
        $form->remove('departureTime');
        $form->remove('status');
        $form->remove('cancelledComment');
        $form->remove('distance');
        $form->remove('imageFile');
        $form->remove('created');
        $form->remove('receiptNo');
        $form->remove('finishedComment');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setStatus(0);
            $task->setArchived(false);
            $task->setDistance(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $translator = $this->get('translator');

            $subject = $translator->trans('new_task_created_for_you');

            if ($task->getProject()) {
                $clientName = $task->getProject()->getClient() ? $task->getProject()->getClient()->getName() : null;
            } else {
                $clientName = '';
            }


            $headers = "From: " . strip_tags('bris@bris.lt') . "\r\n";
            $headers .= "Reply-To: ". strip_tags('bris@bris.lt') . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";

            if ($task->getProject()) {
                $projectTitle = $task->getProject()->getTitle();
                $projectAddress = $task->getProject()->getAddress();
                $projectPhone = $task->getProject()->getTelephone();
                $projectEmail = $task->getProject()->getEmail();
            } else {
                $projectTitle = '-';
                $projectAddress = '-';
                $projectPhone = '-';
                $projectEmail = '-';
            }

            $message = '<html><body>';
            $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
            $message .= "<tr style='background: #eee;'><td><strong>Užduotis:</strong> </td><td>" . strip_tags($task->getDescription()) . "</td></tr>";
            $message .= "<tr style='background: #eee;'><td><strong>Projektas:</strong> </td><td>" .  $projectTitle . "</td></tr>";

            if ($clientName) {
                $message .= "<tr style='background: #eee;'><td><strong>Klientas:</strong> </td><td>" . strip_tags($clientName) . "</td></tr>";
            }

            $message .= "<tr><td><strong>Adresas:</strong> </td><td>" . $projectAddress . "</td></tr>";
            $message .= "<tr><td><strong>Telefonas:</strong> </td><td>" . $projectPhone . "</td></tr>";
            $message .= "<tr><td><strong>El.paštas:</strong> </td><td>" . $projectEmail . "</td></tr>";

            $message .= "<tr><td>Užduotį galite peržiūrėti <a href='http://bris.lt'>http://bris.lt</a></tr>";
            $message .= "</table>";
            $message .= "</body></html>";


            mail($task->getUser()->getEmail(), $subject, $message, $headers);

            $request->getSession()->getFlashBag()->add('task_success', $translator->trans('task_addedd_successfully'));

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('task/new.html.twig', array(
            'task' => $task,
            'form' => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing task entity.
     *
     * @Route("/dashboard/task/edit/{id}", name="task_edit")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Task $task)
    {
        $editForm = $this->createForm('AppBundle\Form\TaskType', $task);

        $archiveForm = $this->createForm('AppBundle\Form\TaskArchiveType', $task);

        $logger = $this->get('appbundle.logger_service');

        $logger->setStatusBefore($task->getStatus());
        $logger->setTask($task);
        $logger->setUserBefore($task->getUser());
        $logger->setUserChanged($this->getUser());

        if ($task->getStatus() != 3) {
            $editForm->remove('arrivalTime');
            $editForm->remove('departureTime');
        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $translator = $this->get('translator');

            $logger->setUserAfter($task->getUser());
            $logger->setStatusAfter($task->getStatus());

            $logger->processLog();

            $request->getSession()->getFlashBag()->add('task_success', $translator->trans('information_updated_successfully'));

            return $this->redirectToRoute('task_edit', array('id' => $task->getId()));
        }

        $taskLogsRepository = $this->getDoctrine()->getManager()->getRepository(TaskLog::class);

        $taskLogs = $taskLogsRepository->findBy([
            'task' => $task
        ], [
            'created' => 'DESC'
        ]);

        $previousLog = null;

        $taskLogs = array_reverse($taskLogs);

        foreach ($taskLogs as $key => $log) {
            if ($previousLog) {
                $timespent = $log->getCreated()->getTimestamp() - $previousLog->getCreated()->getTimestamp();

                $taskLogs[$key]->timespent = date('H:i', $timespent);

                $previousLog = $log;
            } else {
                $taskLogs[$key]->timespent = 0;
            }

            $previousLog = $log;
        }

        return $this->render('task/edit.html.twig', array(
            'task' => $task,
            'edit_form' => $editForm->createView(),
            'taskLogs' => array_reverse($taskLogs),
            'archive_form' => $archiveForm->createView()
        ));
    }

    /**
     * Archives a task.
     *
     * @Route("/dashboard/tasks/archive/{id}", name="archive_task")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function archiveTaskAction(Request $request, Task $task)
    {
        $archiveForm = $this->createForm('AppBundle\Form\TaskArchiveType', $task);

        $archiveForm->handleRequest($request);

        $translator = $this->get('translator');

        if ($archiveForm->isSubmitted() && $archiveForm->isValid()) {
            $task->setArchived(true);

            $this->getDoctrine()->getManager()->flush();

            $request->getSession()->getFlashBag()->add('archive_success', $translator->trans('archived_successfully'));

            return $this->redirectToRoute('dashboard');
        }

        return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
    }

    /**
     * Adds projects by client
     *
     * @Route("/project-row/{id}", name="project_row")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function projectRowAction(Request $request, Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $projectRepo = $em->getRepository(Project::class);

        $_projects = $projectRepo->findBy([
            'client' => $client
        ]);

        $projects = [];

        if ($_projects) {
            foreach ($_projects as $project) {
                $projects[] = [
                    'id' => $project->getId(),
                    'title' => $project->getTitle()
                ];
            }
        }

        return $this->json([
            'success' => true,
            'projects' => $projects
        ]);
    }

    /**
     * Adds projects by client
     *
     * @Route("/project-row/", name="project_row_all")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function projectRowAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projectRepo = $em->getRepository(Project::class);


        $_projects = $projectRepo->findAll();

        $projects = [];

        if ($_projects) {
            foreach ($_projects as $project) {
                $projects[] = [
                    'id' => $project->getId(),
                    'title' => $project->getTitle()
                ];
            }
        }

        return $this->json([
            'success' => true,
            'projects' => $projects
        ]);
    }

    /**
     * Deletes a task entity.
     *
     * @Route("/dashboard/tasks/delete/{id}", name="task_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }
}
