<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Client;
use AppBundle\Entity\Project;
use AppBundle\Entity\Task;

class ReportController extends Controller
{
    const TYPE_CLIENT = 0;
    const TYPE_PROJECT = 1;
    const TYPE_USER = 2;
    /**
     * Lists menu for generating XLS report.
     *
     * @Route("/dashboard/generate-invoice", name="generate_invoice_list")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('generation/index.html.twig');
    }

    /**
     * Generates a form according to an url param
     *
     * @Route("/dashboard/generate-form", name="generate_form")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function generateFormAction(Request $request)
    {
        $formType = $request->get('formType');

        if (!$formType) {
            throw new AccessDeniedHttpException('No `formType` parameter provided');
        }

        $editForm = $this->createForm('AppBundle\Form\EntitySelectType');

        return $this->render('reports/forms.html.twig', [
            'formType' => $formType,
            'editForm' => $editForm->createView()
        ]);
    }

    /**
     * Lists menu for generating XLS report.
     *
     * @Route("/dashboard/export-report", name="generate_export_report")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function exportReportAction(Request $request)
    {
        $data = $request->request->all();

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        $name = 'export_';

        $from = $request->request->get('date-from');
        $to = $request->request->get('date-to');
        $useSingle = $request->request->get('use-single', false);
        $entitySelect = $request->request->get('entity_select');

        if ($request->request->get('sort-by') == self::TYPE_CLIENT) {
            $name .= 'client_';
        } else if ($request->request->get('sort-by') == self::TYPE_PROJECT) {
            $name .= 'project_';
        } else {
            $name .= 'user_';
        }

        if ($request->request->get('sort-by') == self::TYPE_CLIENT) {
            $phpExcelObject = $this->generateClientReport($phpExcelObject, $from, $to, $useSingle, $entitySelect);
        } else if ($request->request->get('sort-by') == self::TYPE_PROJECT) {
            $phpExcelObject = $this->generateProjectReport($phpExcelObject, $from, $to, $useSingle, $entitySelect);
        } else {
            $phpExcelObject = $this->generateUserReport($phpExcelObject, $from, $to, $useSingle, $entitySelect);
        }

        $name .= time();

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $name . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function generateUserReport($phpExcelObject, $from, $to, $useSingle, $entitySelect)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository(User::class);
        $taskRepository = $em->getRepository(Task::class);

        if ($useSingle) {
            $users = $userRepository->findBy(['id' => $entitySelect['user']]);
        } else {
            $users = $userRepository->findAll();
        }

        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(14);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(11);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(18);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $translator->trans('user'))
            ->setCellValue('B1', $translator->trans('project'))
            ->setCellValue('C1', $translator->trans('client'))
            ->setCellValue('D1', $translator->trans('task_description'))
            ->setCellValue('E1', $translator->trans('arrival_time'))
            ->setCellValue('F1', $translator->trans('departure_time'))
            ->setCellValue('G1', $translator->trans('timespent'))
            ->setCellValue('H1', $translator->trans('distance'));

        $row = 2;

        $totalTimespent = 0;
        $totalDistance = 0;

        foreach ($users as $user) {
            if ($from || $to) {
                $tasks = $taskRepository->findTasksByDateAndUser($from, $to, $user->getId());
            } else {
                $tasks = $taskRepository->findBy(['user' => $user->getId()]);
            }

            foreach ($tasks as $task) {
                $timeSpent = 0;

                if ($task->getDepartureTime()) {
                    $timeSpent = $task->getDepartureTime()->getTimestamp() - $task->getArrivalTime()->getTimestamp();

                    $totalTimespent += $timeSpent;

                    $timeSpent = $this->convertTimestampToString($timeSpent);
                }

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$row, $user->getFirstName() . ' ' . $user->getLastName())
                    ->setCellValue('B'.$row, $task->getProject()->getTitle())
                    ->setCellValue('C'.$row, $task->getClient() ? $task->getClient()->getName() : '-')
                    ->setCellValue('D'.$row, $task->getDescription())
                    ->setCellValue('E'.$row, $task->getArrivalTime() ? $task->getArrivalTime() : '-')
                    ->setCellValue('F'.$row, $task->getDepartureTime() ? $task->getDepartureTime() : '-')
                    ->setCellValue('G'.$row, $timeSpent)
                    ->setCellValue('H'.$row, $task->getDistance());

                $totalDistance += $task->getDistance();

                ++$row;
            }
        }

        $row++;

        $phpExcelObject->setActiveSheetIndex()
            ->setCellValue('A'.$row, $translator->trans('total'))
            ->setCellValue('G'.$row, $this->convertTimestampToString($totalTimespent))
            ->setCellValue('H'.$row, $totalDistance);

        $phpExcelObject->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);

        return $phpExcelObject;
    }

    public function generateClientReport($phpExcelObject, $from, $to, $useSingle, $entitySelect)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $clientRepository = $em->getRepository(Client::class);
        $projectRepository = $em->getRepository(Project::class);
        $taskRepository = $em->getRepository(Task::class);

        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(20);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $translator->trans('client'))
            ->setCellValue('B1', $translator->trans('timespent'))
            ->setCellValue('C1', $translator->trans('distance'));

        $row = 2;

        $totalTimespent = 0;
        $totalDistance = 0;

        if ($useSingle) {
            $clients = $clientRepository->findBy(['id' => $entitySelect['client']]);
        } else {
            $clients = $clientRepository->findAll();
        }

        foreach ($clients as $client) {
            if ($from || $to) {
                $tasks = $taskRepository->findTasksByDateAndClient($from, $to, $client->getId());
            } else {
                $tasks = $client->getTasks();
            }

            $timeSpent = 0;
            $distance = 0;

            foreach ($tasks as $task) {
                if ($task->getDepartureTime()) {
                    $timeSpent += ($task->getDepartureTime()->getTimestamp() - $task->getArrivalTime()->getTimestamp());
                }

                $distance += $task->getDistance();
            }

            $phpExcelObject->setActiveSheetIndex()
                ->setCellValue('A'.$row, $client->getName())
                ->setCellValue('B'.$row, $this->convertTimestampToString($timeSpent))
                ->setCellValue('C'.$row, $distance);

            $totalTimespent += $timeSpent;
            $totalDistance += $distance;
            ++$row;
        }

        $row++;

        $phpExcelObject->setActiveSheetIndex()
            ->setCellValue('A'.$row, $translator->trans('total'))
            ->setCellValue('B'.$row, $this->convertTimestampToString($totalTimespent))
            ->setCellValue('C'.$row, $totalDistance);

        $phpExcelObject->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);

        return $phpExcelObject;
    }

    public function generateProjectReport($phpExcelObject, $from, $to, $useSingle, $entitySelect)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $translator->trans('project'))
            ->setCellValue('B1', $translator->trans('timespent'))
            ->setCellValue('C1', $translator->trans('distance'));

        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(20);

        $row = 2;

        $projectRepository = $em->getRepository(Project::class);
        $taskRepository = $em->getRepository(Task::class);

        $totalTimespent = 0;
        $totalDistance = 0;

        if ($useSingle) {
            $projects = $projectRepository->findBy(['id' => $entitySelect['project']]);
        } else {
            $projects = $projectRepository->findAll();
        }

        foreach ($projects as $project) {
            if ($from || $to) {
                $tasks = $taskRepository->findTasksByDateAndProject($from, $to, $project->getId());
            } else {
                $tasks = $taskRepository->findBy(['project' => $project]);
            }

            $timeSpent = 0;
            $distance = 0;

            foreach ($tasks as $task) {
                if ($task->getDepartureTime()) {
                    $timeSpent += $task->getDepartureTime()->getTimestamp() - $task->getArrivalTime()->getTimestamp();
                }

                $distance += $task->getDistance();
            }

            $phpExcelObject->setActiveSheetIndex()
                ->setCellValue('A'.$row, $project->getTitle())
                ->setCellValue('B'.$row, $this->convertTimestampToString($timeSpent))
                ->setCellValue('C'.$row, $distance);

            $totalTimespent += $timeSpent;
            $totalDistance += $distance;
            ++$row;
        }

        $row++;

        $phpExcelObject->setActiveSheetIndex()
            ->setCellValue('A'.$row, $translator->trans('total'))
            ->setCellValue('B'.$row, $this->convertTimestampToString($totalTimespent))
            ->setCellValue('C'.$row, $totalDistance);

        $phpExcelObject->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);

        return $phpExcelObject;
    }

    public function convertTimestampToString($timestamp) {
        $timeSpentHours = floor($timestamp/60/60);

        $timestamp = $timestamp - $timeSpentHours*60*60;

        $timeSpentMinutes = floor($timestamp/60);

        $timestamp = $timestamp - $timeSpentMinutes*60;

        $timeSpentSeconds = $timestamp;

        if ($timeSpentHours > 0) {
            return $timeSpentHours . 'h ' . $timeSpentMinutes . 'm ' . $timeSpentSeconds . 's';
        } else if ($timeSpentMinutes > 0) {
            return $timeSpentMinutes . 'm ' . $timeSpentSeconds . 's';
        } else {
            return $timeSpentSeconds . 's';
        }
    }
}
