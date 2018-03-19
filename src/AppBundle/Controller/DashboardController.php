<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Task;
use AppBundle\Entity\Settings;
use AppBundle\Form\TaskEnterType;
use AppBundle\Form\PasswordChangeType;
use AppBundle\Form\TaskDelayType;

class DashboardController extends Controller {

    /**
     * Lists all task entities.
     *
     * @Route("/dashboard", name="dashboard")
     * @Security("has_role('ROLE_USER')")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Task')->findBy(['archived' => false], ['created' => 'DESC']);

        $openTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 0, 'archived' => false], ['created' => 'DESC']);
        $arrivalTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 1, 'archived' => false], ['created' => 'DESC']);
        $delayedTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 2, 'archived' => false], ['created' => 'DESC']);
        $finishedTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 3, 'archived' => false], ['created' => 'DESC']);

        if ($this->getUser()->hasRole('ROLE_ADMIN')) {
            $settings = $em->getRepository(Settings::class)->find(1);

            return $this->render('dashboard/index.html.twig', array(
                'tasks' => $tasks,
                'openTasks' => $openTasks,
                'arrivalTasks' => $arrivalTasks,
                'delayedTasks' => $delayedTasks,
                'finishedTasks' => $finishedTasks,
                'settings' => $settings
            ));
        }

        $userTasks = $em->getRepository('AppBundle:Task')->findUserTasks($this->getUser()->getId());

        return $this->render('dashboard/user-index.html.twig', [
            'userTasks' => $userTasks,
        ]);
    }

    /**
     * List my tasks
     *
     * @Route("/my-tasks", name="my_tasks")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function myTasksAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userTasks = $em->getRepository('AppBundle:Task')->findUserTasks($this->getUser()->getId());

        return $this->render('dashboard/user-index.html.twig', [
            'userTasks' => $userTasks,
        ]);
    }

    /**
     * @Route("/dashboard/start-task/{id}", name="start_task")
     * @Security("has_role('ROLE_USER')")
     */
    public function startTask(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->redirectToRoute('dashboard');
        }

        $logger = $this->get('appbundle.logger_service');
        $logger->logStatus($task, 1, $this->getUser(), $this->getUser());

        $task->setStatus(1);
        $task->setArrivalTime(new \DateTime());

        $em->persist($task);

        $em->flush();

        if ($this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->redirectToRoute('my_tasks');
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/dashboard/enter-task/{id}", name="enter_task")
     * @Security("has_role('ROLE_USER')")
     */
    public function enterTask(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(TaskEnterType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setDistance($task->getDistance() + $form->get('distance')->getData());

            $em = $this->getDoctrine()->getManager();

            $logger = $this->get('appbundle.logger_service');

            $logger->logStatus($task, 3, $this->getUser(), $this->getUser());
            $task->setStatus(3);

            $task->setDepartureTime(new \DateTime());

            $em->persist($task);
            $em->flush();

            $translator = $this->get('translator');

            $request->getSession()->getFlashBag()->add('task_update_success', $translator->trans('task_updated_successfully'));

            if ($this->getUser()->hasRole('ROLE_ADMIN')) {
                return $this->redirectToRoute('my_tasks');
            }

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/enter-task.html.twig', [
            'edit_form' => $form->createView(),
            'task' => $task
        ]);
    }

    /**
     * @Route("/dashboard/delay-task/{id}", name="delay_task")
     * @Security("has_role('ROLE_USER')")
     */
    public function delayTaskAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(TaskDelayType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $logger = $this->get('appbundle.logger_service');

            $task->setDistance($task->getDistance() + $form->get('distance')->getData());

            $taskLog = $logger->logStatus($task, 2, $this->getUser(), $this->getUser());

            $cancelledComment = $form->get('cancelledComment')->getData();

            $taskLog->setComment($cancelledComment);
            $em->persist($taskLog);


            $task->setStatus(2);

            $em->persist($task);
            $em->flush();

            $translator = $this->get('translator');

            $request->getSession()->getFlashBag()->add('task_update_success', $translator->trans('task_updated_successfully'));

            if ($this->getUser()->hasRole('ROLE_ADMIN')) {
                return $this->redirectToRoute('my_tasks');
            }

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/delay-task.html.twig', [
            'edit_form' => $form->createView(),
            'task' => $task
        ]);
    }

    /**
     * @Route("/dashboard/change-password", name="change_password")
     * @Security("has_role('ROLE_USER')")
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(PasswordChangeType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $factory = $this->get('security.encoder_factory');

            $encoder = $factory->getEncoder($user);

            $password = $form->get('old_password')->getData();

            $bool = ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt()));

            $translator = $this->get('translator');

            if (!$bool) {
                $request->getSession()->getFlashBag()->add('password_update_error', $translator->trans('old_password_is_incorrect'));
            } else {
                $newPassword = $form->get('new_password')->getData();
                $newPasswordRepeat = $form->get('repeat_new_password')->getData();

                if ($newPassword != $newPasswordRepeat) {
                    $request->getSession()->getFlashBag()->add('password_update_error', $translator->trans('passwords_do_not_match'));
                } else {
                    $userManager = $this->get('fos_user.user_manager');

                    $user->setPlainPassword($newPassword);

                    $userManager->updatePassword($user);

                    $em->persist($user);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('password_update_success', $translator->trans('password_successfully_updated'));
                }
            }
        }

        return $this->render('dashboard/change-password.html.twig', [
            'edit_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update-partial", name="update_partial")
     */
    public function updatePartialAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Task')->findBy(['archived' => false], ['created' => 'DESC']);

        $openTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 0, 'archived' => false], ['created' => 'DESC']);
        $arrivalTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 1, 'archived' => false], ['created' => 'DESC']);
        $delayedTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 2, 'archived' => false], ['created' => 'DESC']);
        $finishedTasks = $em->getRepository('AppBundle:Task')->findBy(['status' => 3, 'archived' => false], ['created' => 'DESC']);

        $settings = $em->getRepository(Settings::class)->find(1);

        return $this->render('dashboard/partial-tables.html.twig', array(
            'tasks' => $tasks,
            'openTasks' => $openTasks,
            'arrivalTasks' => $arrivalTasks,
            'delayedTasks' => $delayedTasks,
            'finishedTasks' => $finishedTasks,
            'settings' => $settings
        ));
    }

    /**
     * @Route("/dashboard/edit-profile", name="edit_profile")
     */
    public function editProfileAction(Request $request)
    {
        return $this->render('dashboard/index.html.twig');
    }

    /**
     * @Route("/dashboard/settings", name="settings")
     */
    public function settingsAction(Request $request)
    {
        return $this->redirectToRoute('dashboard');
    }
}
