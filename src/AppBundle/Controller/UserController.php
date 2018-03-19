<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/dashboard/users", name="user_list")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/dashboard/users/new", name="user_new")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);

        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $form->remove('roles');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $roleArray = $user->getRoles();

            if ($form->has('roles')) {
                $roles = $form->get('roles')->getData();

                if (in_array('ROLE_SUPER_ADMIN', $roles)) {
                    $roleArray = [
                        'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'
                    ];
                } else if (in_array('ROLE_ADMIN', $roles)) {
                    $roleArray = [
                        'ROLE_USER', 'ROLE_ADMIN'
                    ];
                } else {
                    $roleArray = [
                        'ROLE_USER'
                    ];
                }
            }

            $user->setRoles($roleArray);

            $user->setPlainPassword($user->getPassword());

            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updatePassword($user);

            $user->setUsername($user->getEmail());
            $user->setEnabled(true);

            $em->persist($user);
            $em->flush();

            $translator = $this->get('translator');

            $request->getSession()->getFlashBag()->add('user_success', $translator->trans('user_created_successfully'));

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/dashboard/users/edit/{id}", name="user_edit")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);

        $editForm->handleRequest($request);

        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $editForm->remove('roles');
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $roleArray = $user->getRoles();

            if ($editForm->has('roles')) {
                $roles = $editForm->get('roles')->getData();

                if (in_array('ROLE_SUPER_ADMIN', $roles)) {
                    $roleArray = [
                        'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'
                    ];
                } else if (in_array('ROLE_ADMIN', $roles)) {
                    $roleArray = [
                        'ROLE_USER', 'ROLE_ADMIN'
                    ];
                } else {
                    $roleArray = [
                        'ROLE_USER'
                    ];
                }
            }

            $user->setRoles($roleArray);

            $user->setPlainPassword($user->getPassword());

            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updatePassword($user);

            $user->setUsername($user->getEmail());

            $this->getDoctrine()->getManager()->persist($user);

            $this->getDoctrine()->getManager()->flush();

            $translator = $this->get('translator');

            $request->getSession()->getFlashBag()->add('user_success', $translator->trans('user_information_updated_successfully'));

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/dashboard/users/delete/{id}", name="user_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_list');
    }
}
