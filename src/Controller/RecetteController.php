<?php

namespace App\Controller;

use App\Entity\Etape;
use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Form\EtapeType;
use App\Form\IngredientType;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/recette")
 */
class RecetteController extends AbstractController
{
    /**
     * @Route("/", name="recette_index", methods={"GET"})
     */
    public function index(RecetteRepository $recetteRepository): Response
    {
        return $this->render('recette/index.html.twig', [
            'entrees' => $recetteRepository->findByCategory('Entrée'),
            'plats' => $recetteRepository->findByCategory('Plat'),
            'desserts' => $recetteRepository->findByCategory('Dessert'),
            'currentUser' => $this->getUser()
        ]);
    }

    /**
     * @Route("/creation/titre", name="recette_new_title", methods={"GET","POST"})
     */
    public function newTitle(Request $request): Response
    {
        $recette = new Recette();
        $formRecette = $this->createForm(RecetteType::class, $recette);
        $formRecette->handleRequest($request);
        if ($formRecette->isSubmitted() && $formRecette->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $recette->setAuthor($this->getUser());
            $recette->setUpdatedAt(date('d/m/Y'));
            $entityManager->persist($recette->getCategory());
            $entityManager->persist($recette->getSeason());
            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('recette_edit', ['id'=>$recette->getId()]);
        }

        return $this->render('recette/new_title.html.twig', [
            'recette' => $recette,
            'formRecette' => $formRecette->createView(),
        ]);
    }

    /**
     * @Route("/{id}/ingredients", name="recette_new_ingredients", methods={"GET","POST"})
     */
    public function newIngredient(Request $request, Recette $recette): Response
    {
        $ingredient = new Ingredient();
        $formIngredient = $this->createForm(IngredientType::class, $ingredient);
        $formIngredient->handleRequest($request);
        if ($formIngredient->isSubmitted() && $formIngredient->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $recette->addIngredient($ingredient);
            $entityManager->persist($ingredient);
            $entityManager->flush();

            return $this->redirectToRoute('recette_new_ingredients', ['id'=>$recette->getId()]);
        }

        return $this->render('recette/new_ingredient.html.twig', [
            'recette' => $recette,
            'formIngredient' => $formIngredient->createView(),
        ]);
    }

    /**
     * @Route("/{id}/etapes/{idEtape?}", name="recette_new_etapes", methods={"GET","POST"})
     * @param Request $request
     * @param Recette $recette
     * @return Response
     */
    public function newEtape(Request $request, Recette $recette, ?int $idEtape): Response
    {
        $etape = $idEtape
            ? $this->getDoctrine()
                ->getRepository(Etape::class)
                ->find($idEtape)
            : new Etape();
        $formEtape = $this->createForm(EtapeType::class, $etape);
        $formEtape->handleRequest($request);
        if ($formEtape->isSubmitted() && $formEtape->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $etape->setContent(nl2br($etape->getContent()));
            $recette->addEtape($etape);
            $entityManager->persist($etape);
            $entityManager->flush();

            return $this->redirectToRoute('recette_new_etapes', ['id'=>$recette->getId()]);
        }

        return $this->render('recette/new_etape.html.twig', [
            'recette' => $recette,
            'formEtape' => $formEtape->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recette_show", methods={"GET"})
     */
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="recette_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Recette $recette): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette->setUpdatedAt(date('d/m/Y'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recette_edit', ['id'=>$recette->getId()]);
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'formRecette' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recette_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Recette $recette): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recette_index');
    }

    /**
     * @Route("/search", name="recette_search", methods={"GET","POST"})
     */
    public function search(Request $request): Response
    {
        if ($this->isCsrfTokenValid('search', $request->request->get('_token'))) {
            $keyword = $request->request->get('keyword');
            $recettes = $this->getDoctrine()->getRepository(Recette::class)->findByKeyword($keyword);
            return $this->render('recette/search_result.twig', [
                'recettes' => $recettes,
                'keyword' => $keyword
            ]);
        }
    }
}
