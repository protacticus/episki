<?php

/*
 * This file is part of episki core.
 *
 * (c) Justin Leapline <justin@episki.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Risk;
use App\Entity\Controls;
use App\Entity\Finding;
use App\Events;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Justin Leapline <justin@episki.org>
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard_index")
     * @Method("GET")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     * See https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     */
    public function indexAction()
    {
        //$em = $this->getDoctrine()->getManager();
        //$posts = $em->getRepository(Post::class)->findLatest($page);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('dashboard/index.html.twig');
    }
    
    /**
     * Pull the assigned controls of the user
     *
     */
    public function assignedControls($limit = 5)
    {   
        $em = $this->getDoctrine()->getManager();
        
        $controls = $em->getRepository(Controls::class)->findAll();
        
        $usercontrols = array();
        
        foreach ($controls as $control) {
	        if ( $control->getOwners()->contains($this->getUser()) )
	        	$usercontrols[] = $control;
        }
       
        return $this->render(
            'dashboard/_assigned_controls.html.twig',
            array('controls' => $usercontrols)
        );
    }
    
    /**
     * Pull the assigned controls of the user
     *
     */
    public function topRisks($limit = 5)
    {   
        $em = $this->getDoctrine()->getManager();
        
        $risks = $em->getRepository(Risk::class)->findBy([]);

       
        return $this->render(
            'dashboard/_top_risks.html.twig',
            array('risks' => $risks)
        );
    }
    
    /**
     * Pull the assigned findings of the user
     *
     */
    public function assignedFindings($limit = 5)
    {   
        $em = $this->getDoctrine()->getManager();
        
        $findings = $em->getRepository(Finding::class)->findAll();
        
        $userfindings = array();
        
        foreach ($findings as $finding) {
	        if ( $finding->getOwners()->contains($this->getUser()) )
	        	$userfindings[] = $finding;
        }
       
        return $this->render(
            'dashboard/_assigned_findings.html.twig',
            array('findings' => $userfindings)
        );
    }
    
    

}
