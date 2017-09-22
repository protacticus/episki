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

use App\Entity\Message;
use App\Form\MessageType;
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
 * @Route("/message")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class MessageController extends Controller
{
    /**
     * Lists all Message entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'message_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/", name="message_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository(Message::class)->findByUser($this->getUser());
        
        return $this->render('message/index.html.twig', ['messages' => $messages]);
    }

    /**
     * Creates a new Message entity.
     *
     * @Route("/new", name="message_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $message = new Message();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(MessageType::class, $message)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {
            //$message->setSlug($slugger->slugify($message->getTitle()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'message.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('message_new');
            }

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Message entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="message_show")
     * @Method("GET")
     */
    public function showAction(Message $message)
    {
    	// change the message to being read now
    	$message->setUnread(false);
    	$this->getDoctrine()->getManager()->flush();

        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * Displays a form to edit an existing Message entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="message_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Message $message, Slugger $slugger)
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('message_edit', ['id' => $message->getId()]);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Message entity.
     *
     * @Route("/{id}/delete", name="message_delete")
     * @Method("POST")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Message $message)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('message_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        $this->addFlash('success', 'message.deleted_successfully');

        return $this->redirectToRoute('message_index');
    }
    
    /**
     * Pull the unread messages
     *
     */
    public function unreadMessages($limit = 5)
    {   
        $em = $this->getDoctrine()->getManager();
        
        $messages = $em->getRepository(Message::class)->findBy(['user' => $this->getUser(),'unread' => true]);

       
        return $this->render(
            'message/_unread_messages.html.twig',
            array('unread' => $messages)
        );
    }
}

