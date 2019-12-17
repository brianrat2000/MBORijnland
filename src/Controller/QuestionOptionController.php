<?php

namespace App\Controller;

use App\Entity\QuestionOption;
use App\Form\QuestionOptionType;
use App\Repository\QuestionOptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/questionoption")
 */
class QuestionOptionController extends AbstractController
{
    /**
     * @Route("/", name="question_option_index", methods={"GET"})
     */
    public function index(QuestionOptionRepository $questionOptionRepository): Response
    {
        return $this->render('question_option/index.html.twig', [
            'question_options' => $questionOptionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="question_option_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $questionOption = new QuestionOption();
        $form = $this->createForm(QuestionOptionType::class, $questionOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($questionOption);
            $entityManager->flush();

            return $this->redirectToRoute('question_option_index');
        }

        return $this->render('question_option/new.html.twig', [
            'question_option' => $questionOption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_option_show", methods={"GET"})
     */
    public function show(QuestionOption $questionOption): Response
    {
        return $this->render('question_option/show.html.twig', [
            'question_option' => $questionOption,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_option_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, QuestionOption $questionOption): Response
    {
        $form = $this->createForm(QuestionOptionType::class, $questionOption);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_option_index');
        }

        return $this->render('question_option/edit.html.twig', [
            'question_option' => $questionOption,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_option_delete", methods={"DELETE"})
     */
    public function delete(Request $request, QuestionOption $questionOption): Response
    {
        if ($this->isCsrfTokenValid('delete'.$questionOption->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($questionOption);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_option_index');
    }
}
