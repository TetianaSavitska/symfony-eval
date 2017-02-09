<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use AppBundle\Form\PublicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AppController
 * @package AppBundle\Controller
 */
class AppController extends Controller
{
    /**
     * Home page action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
    	$publications = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Publication')
            ->findBy([], ["publishedAt" => 'DESC'], 3);

        return $this->render('AppBundle:App:home.html.twig', [
        	'publications' => $publications,
        ]);
    }

    public function sciencesAction()
    {
    	$sciences = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Science')
            ->findBy([], ["title" => 'ASC']);

        return $this->render('AppBundle:App:sciences.html.twig', [
        	'sciences' => $sciences,
        ]);
    }

    public function scienceAction($scienceId)
    {
    	$science = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Science')
            ->find($scienceId);

        $publications = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Publication')
            ->findBy(['science' => $science], ["publishedAt" => 'DESC']);

        return $this->render('AppBundle:App:science.html.twig', [
        	'science' => $science,
        	'publications' => $publications
        ]);
    }

    public function publicationAction($publicationId)
    {
    	$publication = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Publication')
            ->find($publicationId);

        return $this->render('AppBundle:App:publication.html.twig', [
        	'publication' => $publication,
        ]);
    }

    public function publishAction(Request $request)
    {
    	$publication = new Publication();
        $form = $this->createForm('AppBundle\Form\PublicationType', $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	//$publishedAt = \DateTime date();
        	//$publication->setPublishedAt($publishedAt);
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $em->flush($publication);

            return $this->redirectToRoute('app_home', array('id' => $publication->getId()));
        }

        return $this->render('publication/new.html.twig', array(
            'publication' => $publication,
            'form' => $form->createView(),
        ));
    }


}
