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

use App\Entity\Artifact;
use App\Form\ArtifactType;
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
 * @Route("/artifact")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class ArtifactController extends Controller
{
    /**
     * Lists all Artifact entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'artifact_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", name="artifact_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $artifacts = $em->getRepository(Artifact::class)->findAll();
        
        return $this->render('artifact/index.html.twig', ['artifacts' => $artifacts]);
    }

    /**
     * Creates a new Artifact entity.
     *
     * @Route("/new", name="artifact_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $artifact = new Artifact();
        

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(ArtifactType::class, $artifact);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            $artifact->setUploaduser($this->getUser());
            $artifact->setUploaddate(new \DateTime());
            
            $file = $artifact->getFile();
            
            $artifact->setFilename($file->getClientOriginalName());

            // Generate a unique name for the file before saving it
            $fileName = sha1(uniqid()).'.'.$file->guessExtension();
            
            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('upload_directory'),
                $fileName
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($artifact);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'artifact.created_successfully');

            return $this->redirectToRoute('artifact_index');
        }

        return $this->render('artifact/new.html.twig', [
            'artifact' => $artifact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Artifact entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="artifact_show")
     * @Method("GET")
     */
    public function showAction(Artifact $artifact)
    {
        // This security check can also be performed
        // using an annotation: @Security("is_granted('show', post)")
        //$this->denyAccessUnlessGranted('show', $artifact, 'artifacts can only be shown to their authors.');

        return $this->render('artifact/show.html.twig', [
            'artifact' => $artifact,
        ]);
    }

    /**
     * Displays a form to edit an existing Artifact entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="artifact_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Artifact $artifact, Slugger $slugger)
    {
        //$this->denyAccessUnlessGranted('edit', $artifact, 'artifacts can only be edited by their authors.');

        $form = $this->createForm(ArtifactType::class, $artifact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artifact->setSlug($slugger->slugify($artifact->getTitle()));
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'artifact.updated_successfully');

            return $this->redirectToRoute('artifact_edit', ['id' => $artifact->getId()]);
        }

        return $this->render('artifact/edit.html.twig', [
            'artifact' => $artifact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Artifact entity.
     *
     * @Route("/{id}/delete", name="artifact_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Artifact $artifact)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('artifact_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($artifact);
        $em->flush();

        $this->addFlash('success', 'artifact.deleted_successfully');

        return $this->redirectToRoute('artifact_index');
    }
}

