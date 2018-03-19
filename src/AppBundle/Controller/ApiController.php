<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use AppBundle\Entity\Client;
use AppBundle\Entity\Task;
use AppBundle\Entity\File;

class ApiController extends Controller {
    /**
     * Search for users
     *
     * @Route("/api/users", name="user_search_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function authorListAction(Request $request)
    {
        $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

        $users = $userRepository->searchByName($request->get('term'));

        $result = [];

        foreach ($users as $user) {
            $result[] = [
                'id' => $user['id'],
                'label' => $user['firstName'].' '.$user['lastName'],
                'value' => $user['id']
            ];
        }

        return $this->json($result);
    }

    /**
     * Fetch single user
     *
     * @Route("/api/fetch-single/{user}", name="user_select_api")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function fetchSingleAction(Request $request, User $user)
    {
        return $this->json($user->getFirstName() . ' ' . $user->getLastName());
    }

    /**
     * Search for projects
     *
     * @Route("/api/projects", name="project_search_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function projectListAction(Request $request)
    {
        $projectRepository = $this->getDoctrine()->getManager()->getRepository(Project::class);

        $projects = $projectRepository->searchByTitle($request->get('term'));

        $result = [];

        foreach ($projects as $project) {
            $result[] = [
                'id' => $project['id'],
                'label' => $project['title'],
                'value' => $project['id']
            ];
        }

        return $this->json($result);
    }

    /**
     * Fetch single project
     *
     * @Route("/api/fetch-single-project/{project}", name="project_select_api")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function fetchSingleProjectAction(Request $request, Project $project)
    {
        return $this->json($project->getTitle());
    }

    /**
     * Search for clients
     *
     * @Route("/api/clients", name="client_search_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function clientListAction(Request $request)
    {
        $clientRepository = $this->getDoctrine()->getManager()->getRepository(Client::class);

        $clients = $clientRepository->searchByName($request->get('term'));

        $result = [];

        foreach ($clients as $client) {
            $result[] = [
                'id' => $client['id'],
                'label' => $client['name'],
                'value' => $client['id']
            ];
        }

        return $this->json($result);
    }

    /**
     * Fetch single client
     *
     * @Route("/api/fetch-single-client/{client}", name="client_select_api")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function fetchSingleClientAction(Request $request, Client $client)
    {
        return $this->json($client->getName());
    }

    /**
     * Uploads a file
     *
     * @Route("/api/upload-file/{task}", name="upload_file")
     */
    public function uploadFileAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();

        $result = ['files' => []];

        foreach ($request->files->get('files') as $file) {
            $name = $file->getClientOriginalName();
            $extension = $file->guessClientExtension();

            $name = str_replace('.'.$extension, '', $name);

            $productionName = $name.'_'.time().'.'.$extension;

            $path = $file->move('uploads/', $productionName);

            $fileEntity = new File();

            $fileEntity->setTask($task);
            $fileEntity->setName($name);
            $fileEntity->setPath($path);

            $em->persist($fileEntity);

            $em->flush();

            $result['files'][] = [
                'name' => $name,
                'size' => $file->getClientSize(),
                'url' => '/' . $path,
                'thumbnailUrl' => '/' . $path,
                'deleteUrl' => $this->generateUrl('delete_file', ['file' => $fileEntity->getId()]),
                'deleteType' => 'GET'
            ];
        }

        return $this->json($result);
    }

    /**
     * List files
     *
     * @Route("/api/list-files/{task}", name="list_files")
     */
    public function listFilesAction(Request $request, Task $task)
    {
        $filesRepository = $this->getDoctrine()->getManager()->getRepository(File::class);

        $files = [];

        foreach ($filesRepository->findBy(['task' => $task->getId()]) as $file) {
            $files[] = [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'path' => $file->getPath()
            ];
        }

        return $this->json($files);
    }

    /**
     * Deletes a file
     *
     * @Route("/api/delete-file/{file}", name="delete_file")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteFileAction(Request $request, File $file)
    {
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');

        $task = $file->getTask();

        $em->remove($file);

        $em->flush();

        $request->getSession()->getFlashBag()->add('file_deleted_successfully', $translator->trans('file_deleted_successfully'));

        return $this->redirectToRoute($this->generateUrl('task_edit', ['id' => $task->getId()]));
    }
}
