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

use App\Entity\Finding;
use App\Form\FindingType;
use App\Utils\Slugger;
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
 * @Route("/finding")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class FindingController extends Controller
{
    /**
     * Lists all Finding entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'finding_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", name="finding_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $findings = $em->getRepository(Finding::class)->findAll();
        
        return $this->render('finding/index.html.twig', ['findings' => $findings]);
    }

    /**
     * Creates a new Finding entity.
     *
     * @Route("/new", name="finding_new")
     * @Method({"GET", "POST"})
     *
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $finding = new Finding();

        $form = $this->createForm(FindingType::class, $finding);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($finding);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'finding.created_successfully');

            return $this->redirectToRoute('finding_index');
        }

        return $this->render('finding/new.html.twig', [
            'finding' => $finding,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Finding entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="finding_show")
     * @Method("GET")
     */
    public function showAction(Finding $finding)
    {
        // This security check can also be performed
        // using an annotation: @Security("is_granted('show', post)")
        //$this->denyAccessUnlessGranted('show', $finding, 'findings can only be shown to their authors.');

        return $this->render('finding/show.html.twig', [
            'finding' => $finding,
        ]);
    }

    /**
     * Displays a form to edit an existing Finding entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="finding_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Finding $finding, Slugger $slugger)
    {
        //$this->denyAccessUnlessGranted('edit', $finding, 'findings can only be edited by their authors.');

        $form = $this->createForm(FindingType::class, $finding);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'finding.updated_successfully');

            return $this->redirectToRoute('finding_edit', ['id' => $finding->getId()]);
        }

        return $this->render('finding/edit.html.twig', [
            'finding' => $finding,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Finding entity.
     *
     * @Route("/{id}/delete", name="finding_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Finding $finding)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('finding_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($finding);
        $em->flush();

        $this->addFlash('success', 'finding.deleted_successfully');

        return $this->redirectToRoute('finding_index');
    }
}

