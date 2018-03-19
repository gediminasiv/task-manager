<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Settings;
use AppBundle\Form\SettingsType;

class SettingsController extends Controller {
    /**
     * Lists all archive entities.
     *
     * @Route("/change-timeout-values", name="change_timeout_values")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function changeTimeoutValuesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $settings = $em->getRepository(Settings::class)->find(1);

        $form = $this->createForm(SettingsType::class, $settings);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $translator = $this->get('translator');

            $request->getSession()->getFlashBag()->add('settings_update_success', $translator->trans('settings_updated_successfully'));
        }

        return $this->render('settings/interval.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings
        ]);
    }
}
