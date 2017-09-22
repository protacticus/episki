<?php

/*
 * This file is part of episk core
 *
 * (c) Justin Leapline <justin@episki.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Controls;
use App\Form\ControlsType;
use App\Utils\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller used to manage contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/controls")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class ControlsController extends Controller
{
    /**
     * Lists all Controls entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'controls_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", defaults={"page": "1", "_format"="html"}, name="controls_index")
     * @Route("/page/{page}", defaults={"_format"="html"}, requirements={"page": "[1-9]\d*"}, name="controls_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($page, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $controls = $em->getRepository(Controls::class)->findLatest($page);
        
        return $this->render('controls/index.html.twig', ['controls' => $controls]);
    }

    /**
     * Creates a new Controls entity.
     *
     * @Route("/new", name="controls_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $controls = new Controls();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(ControlsType::class, $controls)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            //$controls->setSlug($slugger->slugify($controls->getTitle()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($controls);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'controls.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('controls_new');
            }

            return $this->redirectToRoute('controls_index');
        }

        return $this->render('controls/new.html.twig', [
            'controls' => $controls,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Controls entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="controls_show")
     * @Method("GET")
     */
    public function showAction(Controls $controls)
    {
        // This security check can also be performed
        // using an annotation: @Security("is_granted('show', post)")
        //$this->denyAccessUnlessGranted('show', $controls, 'controlss can only be shown to their authors.');

        return $this->render('controls/show.html.twig', [
            'controls' => $controls,
        ]);
    }

    /**
     * Displays a form to edit an existing Controls entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="controls_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Controls $controls, Slugger $slugger)
    {
        //$this->denyAccessUnlessGranted('edit', $controls, 'controlss can only be edited by their authors.');

        $form = $this->createForm(ControlsType::class, $controls);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'controls.updated_successfully');

            return $this->redirectToRoute('controls_edit', ['id' => $controls->getId()]);
        }

        return $this->render('controls/edit.html.twig', [
            'controls' => $controls,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Controls entity.
     *
     * @Route("/{id}/delete", name="controls_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Controls $controls)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('controls_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($controls);
        $em->flush();

        $this->addFlash('success', 'controls.deleted_successfully');

        return $this->redirectToRoute('controls_index');
    }
    
    /**
     * @Route("/search", name="controls_search")
     * @Method("GET")
     *
     * @return Response|JsonResponse
     */
    public function searchAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('controls/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $controls = $this->getDoctrine()->getRepository(Controls::class)->findBySearchQuery($query);

        $results = [];
        foreach ($controls as $control) {
            $results[] = [
                'title' => htmlspecialchars(strlen($control->getRequirement()) > 80 ? substr($control->getRequirement(), 0, 80) . '...' : $control->getRequirement()),
                'summary' => htmlspecialchars(strlen($control->getDescription()) > 400 ? substr($control->getDescription(), 0, 400) . '...' : $control->getDescription()),
                'url' => $this->generateUrl('controls_show', ['id' => $control->getId()]),
            ];
        }

        return $this->json($results);
    }
}

