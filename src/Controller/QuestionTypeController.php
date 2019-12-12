<?php

namespace App\Controller;

use App\Entity\QuestionType;
use App\Form\QuestionTypeType;
use App\Repository\QuestionTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/questiontype")
 */
class QuestionTypeController extends AbstractController
{
    /**
     * @Route("/", name="question_type_index", methods={"GET"})
     */
    public function index(QuestionTypeRepository $questionTypeRepository): Response
    {
        return $this->render('question_type/index.html.twig', [
            'question_types' => $questionTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="question_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $questionType = new QuestionType();
        $form = $this->createForm(QuestionTypeType::class, $questionType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($questionType);
            $entityManager->flush();

            return $this->redirectToRoute('question_type_index');
        }

        return $this->render('question_type/new.html.twig', [
            'question_type' => $questionType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_type_show", methods={"GET"})
     */
    public function show(QuestionType $questionType): Response
    {
        return $this->render('question_type/show.html.twig', [
            'question_type' => $questionType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, QuestionType $questionType): Response
    {
        $form = $this->createForm(QuestionTypeType::class, $questionType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_type_index');
        }

        return $this->render('question_type/edit.html.twig', [
            'question_type' => $questionType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, QuestionType $questionType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$questionType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($questionType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_type_index');
    }
}
