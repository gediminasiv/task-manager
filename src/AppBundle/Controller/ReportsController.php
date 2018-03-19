<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\Client;
use AppBundle\Entity\Project;
use AppBundle\Entity\TaskLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


class ReportsController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/dashboard/reports", name="reports_list")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository(Client::class)->findAll();

        $projects = $em->getRepository(Project::class)->findAll();

        $users = $em->getRepository('AppBundle:User')->findAll();

        $tasks = $em->getRepository(Task::class)->findBy([
            'status' => 3
        ]);

        $spentHours = 0;

        foreach ($tasks as $task) {
            $spentHours += strtotime($task->getDepartureTime()->format('Y-m-d H:i:s')) - strtotime($task->getArrivalTime()->format('Y-m-d H:i:s'));
        }

        $spentHours = $spentHours/60/60;

        $totalDistance = $em->getRepository(Task::class)->findTotalDistance();

        $userList = [];

        foreach ($users as $user) {
            $userList[] = $em->getRepository(Task::class)->findUsersForReport($user);
        }

        return $this->render('reports/index.html.twig', array(
            'users' => $users,
            'clients' => $clients,
            'projects' => $projects,
            'spentHours' => $spentHours,
            'totalDistance' => $totalDistance['distance'],
            'userTasks' => $userList
        ));
    }
}
