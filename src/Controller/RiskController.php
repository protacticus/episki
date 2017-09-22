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

use App\Entity\Risk;
use App\Form\RiskType;
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
 * @Route("/risk")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class RiskController extends Controller
{
    /**
     * Lists all Risk entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'risk_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", name="risk_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $risks = $em->getRepository(Risk::class)->findAll();
        
        return $this->render('risk/index.html.twig', ['risks' => $risks]);
    }

    /**
     * Creates a new Risk entity.
     *
     * @Route("/new", name="risk_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $risk = new Risk();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(RiskType::class, $risk)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($risk);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'risk.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('risk_new');
            }

            return $this->redirectToRoute('risk_index');
        }

        return $this->render('risk/new.html.twig', [
            'risk' => $risk,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Risk entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="risk_show")
     * @Method("GET")
     */
    public function showAction(Risk $risk)
    {
        return $this->render('risk/show.html.twig', [
            'risk' => $risk,
        ]);
    }

    /**
     * Displays a form to edit an existing Risk entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="risk_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Risk $risk, Slugger $slugger)
    {
        $form = $this->createForm(RiskType::class, $risk);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'risk.updated_successfully');

            return $this->redirectToRoute('risk_edit', ['id' => $risk->getId()]);
        }

        return $this->render('risk/edit.html.twig', [
            'risk' => $risk,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Risk entity.
     *
     * @Route("/{id}/delete", name="risk_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Risk $risk)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('risk_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($risk);
        $em->flush();

        $this->addFlash('success', 'risk.deleted_successfully');

        return $this->redirectToRoute('risk_index');
    }
}

