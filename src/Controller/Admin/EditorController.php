<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EditorRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor_index', methods: ['GET'])]
    public function index(Request $request, EditorRepository $repository): Response
    {
        $editors = new Pagerfanta(new QueryAdapter($repository->findByName()));
        $editors->setMaxPerPage(10);
        $editors->setCurrentPage($request->query->getInt('page', 1));


        return $this->render('admin/editor/index.html.twig', [
            'controller_name' => 'EditorController',
            'editors' => $editors,
        ]);
    }

    #[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_editor_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $editor = new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $manager->persist($editor);
             $manager->flush();

             return $this->redirectToRoute('app_admin_editor_new');
        }
        
        return $this->render('admin/editor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_editor_show', requirements: ['id' => '\d+'],methods: ['GET'])]
    public function show(?Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }
}
