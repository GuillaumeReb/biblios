<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Pagerfanta\Pagerfanta;

#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    #[Route('', name: 'app_admin_author_index', methods: ['GET'])]
    public function index(Request $request, AuthorRepository $repository): Response
    {
        $dates = [];
        if ($request->query->has('start')) {
            $dates['start'] = $request->query->get('start');
        }
        
        if ($request->query->has('end')) {
            $dates['end'] = $request->query->get('end');
        }
        
        $authors = Pagerfanta::createForCurrentPageWithMaxPerpage(
            new QueryAdapter($repository->findByDateOfBirth()),
            $request->query->get('page', 1),
            maxPerPage: 10
        );

        // $authors = $repository->findAll();

        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'authors' => $authors,
        ]);
    }

    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
{
    $author = new Author();
    $form = $this->createForm(AuthorType::class, $author);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $manager->persist($author);
        $manager->flush();

        return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
    }

    return $this->render('admin/author/new.html.twig', [
        'form' => $form,
    ]);
}
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Author $author, Request $request, EntityManagerInterface $manager): Response
{
    $form = $this->createForm(AuthorType::class, $author);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $manager->flush();

        return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
    }

    return $this->render('admin/author/edit.html.twig', [
        'form' => $form,
        'author' => $author,
    ]);
}
#[Route('/{id}', name: 'app_admin_author_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
public function delete(Request $request, Author $author, EntityManagerInterface $manager): Response
{
    if ($this->isCsrfTokenValid('delete_author_' . $author->getId(), $request->request->get('_token'))) {
        $manager->remove($author);
        $manager->flush();
    }

    return $this->redirectToRoute('app_admin_author_index');
}


    // public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
    // {
    //     $author ??= new Author();
    //     $author = new Author();
    //     $form = $this->createForm(AuthorType::class, $author);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //          $manager->persist($author);
    //          $manager->flush();

    //          return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
    //     }
        
    //     return $this->render('admin/author/new.html.twig', [
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'],methods: ['GET'])]
    public function show(?Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', [
            'author' => $author,
        ]);
    }
}
