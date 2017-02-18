<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use AppBundle\Entity\Comment;
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
            ->findBy(["validated" => true], ["publishedAt" => 'DESC'], 3);

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

    public function publicationAction($publicationId, $scienceId, Request $request)
    {
    	$science = $this
    		->getDoctrine()
            ->getRepository('AppBundle:Science')
            ->find($scienceId);

        if(!$science){
        	throw $this->createNotFoundException('Science not found');
        }

    	$publication = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Publication')
            ->find($publicationId);

        if(!$publication){
        	throw $this->createNotFoundException('Publication not found');
        }

        $comments = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findBy(['publication' => $publication ], ['id' => 'DESC']);

        $comment = new Comment();
        $form = $this->createForm('AppBundle\Form\CommentType', $comment, [
            'admin' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setPublication($publication);
            $em->persist($comment);
            $em->flush($comment);

            return $this->redirectToRoute('app_publication', ['publicationId' => $publicationId, 'scienceId'=>$scienceId ] );
        }

        return $this->render('AppBundle:App:publication.html.twig', [
        	'publication' => $publication,
        	'science' => $science,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    public function publishAction(Request $request)
    {
    	$publication = new Publication();
        $form = $this->createForm('AppBundle\Form\PublicationType', $publication, [
            'validated_field' => false,
            'admin' => false,
        ]);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $em->flush($publication);

            return $this->redirectToRoute('app_home', array('id' => $publication->getId()));
        }

        return $this->render('AppBundle:App:publier.html.twig', array(
            'publication' => $publication,
            'form' => $form->createView(),
        ));
    }

    public function sidebarAction()
    {
        $sciences = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Science')
            ->findBy([], ['title' => 'ASC']);

        if(!$sciences){
            throw $this->createNotFoundException('Sciences not found');
        }

        $publications = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Publication')
             ->findBy([], ['publishedAt' => 'DESC']);

        if(!$publications){
            throw $this->createNotFoundException('Publications not found');
        }

        return $this->render('AppBundle:Layout:sidebar.html.twig', [
            'publications' => $publications,
            'sciences' => $sciences,
        ]);
    }


}
