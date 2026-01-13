<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository): Response
    {   
        $menu = $menuRepository->findAll();
        return $this->render('menu/index.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/menu/new', name: 'app_menu_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash('success', 'Le menu a été ajouté avec succès !');
            return $this->redirectToRoute('app_menu');
        }

        return $this->render('menu/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/menu/edit/{id}', name: 'app_menu_edit')]
    public function edit(Menu $menu, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le menu a été modifié avec succès !');
            return $this->redirectToRoute('app_menu');
        }

        return $this->render('menu/edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    #[Route('/menu/delete/{id}', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menu);
            $entityManager->flush();
            $this->addFlash('danger', 'Le menu a été supprimé !');
        }

        return $this->redirectToRoute('app_menu');
    }
}

