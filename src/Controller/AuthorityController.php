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

use App\Entity\Authority;
use App\Form\AuthorityType;
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
 * @Route("/authority")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class AuthorityController extends Controller
{
    /**
     * Lists all Authority entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'authority_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", name="authority_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $authoritys = $em->getRepository(Authority::class)->findAll();
        
        return $this->render('authority/index.html.twig', ['authoritys' => $authoritys]);
    }

    /**
     * Creates a new Authority entity.
     *
     * @Route("/new", name="authority_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $authority = new Authority();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(AuthorityType::class, $authority);
        
        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($authority);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'authority.created_successfully');

            return $this->redirectToRoute('authority_index');
        }

        return $this->render('authority/new.html.twig', [
            'authority' => $authority,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Authority entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="authority_show")
     * @Method("GET")
     */
    public function showAction(Authority $authority)
    {
        // This security check can also be performed
        // using an annotation: @Security("is_granted('show', post)")
        //$this->denyAccessUnlessGranted('show', $authority, 'authoritys can only be shown to their authors.');

        return $this->render('authority/show.html.twig', [
            'authority' => $authority,
        ]);
    }

    /**
     * Displays a form to edit an existing Authority entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="authority_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Authority $authority, Slugger $slugger)
    {
        //$this->denyAccessUnlessGranted('edit', $authority, 'authoritys can only be edited by their authors.');

        $form = $this->createForm(AuthorityType::class, $authority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'authority.updated_successfully');

            return $this->redirectToRoute('authority_edit', ['id' => $authority->getId()]);
        }

        return $this->render('authority/edit.html.twig', [
            'authority' => $authority,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Authority entity.
     *
     * @Route("/{id}/delete", name="authority_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Authority $authority)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('authority_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($authority);
        $em->flush();

        $this->addFlash('success', 'authority.deleted_successfully');

        return $this->redirectToRoute('authority_index');
    }
}

