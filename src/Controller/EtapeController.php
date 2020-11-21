<?php

namespace App\Controller;

use App\Entity\Etape;
use App\Form\EtapeType;
use App\Repository\EtapeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/etape")
 */
class EtapeController extends AbstractController
{
    /**
     * @Route("/", name="etape_index", methods={"GET"})
     */
    public function index(EtapeRepository $etapeRepository): Response
    {
        return $this->render('etape/index.html.twig', [
            'etapes' => $etapeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="etape_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $etape = new Etape();
        $form = $this->createForm(EtapeType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($etape);
            $entityManager->flush();

            return $this->redirectToRoute('etape_index');
        }

        return $this->render('etape/new.html.twig', [
            'etape' => $etape,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="etape_show", methods={"GET"})
     */
    public function show(Etape $etape): Response
    {
        return $this->render('etape/show.html.twig', [
            'etape' => $etape,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="etape_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Etape $etape): Response
    {
        $form = $this->createForm(EtapeType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('etape_index');
        }

        return $this->render('etape/edit.html.twig', [
            'etape' => $etape,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="etape_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Etape $etape): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etape->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($etape);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recette_new_etapes', [
            'id' => $etape->getRecette()->getId(),
            'idEtape' => $etape->getId()
        ]);
    }
}
