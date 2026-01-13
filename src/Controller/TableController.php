<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TableRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class TableController extends AbstractController
{
    #[Route('/table', name: 'app_table_index')]
    #[IsGranted('ROLE_SERVER')]
    public function index(TableRepository $tableRepository): Response
    {   
        $tables = $tableRepository->findAll();
        return $this->render('table/index.html.twig', [
            'tables' => $tables,
        ]);
    }

    #[Route('/table/new', name: 'app_table_new')]
    #[IsGranted('ROLE_ADMIN')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $table = new Table();
    $form = $this->createForm(TableType::class, $table);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($table);
        $entityManager->flush();

        // إضافة رسالة نجاح بعد إنشاء الطاولة
        // $this->addFlash('success', 'La table a été ajoutée avec succès !');

        return $this->redirectToRoute('app_table_index');
    }

    return $this->render('table/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/table/edit/{id}', name: 'app_table_edit')]
#[IsGranted('ROLE_SERVER')]
public function edit(Table $table, Request $request, EntityManagerInterface $entityManager): Response
{
    // كنخدمو بنفس الـ FormType اللي صاوبنا
    $form = $this->createForm(TableType::class, $table);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush(); // هنا غير flush كافية بلا persist حيت الطاولة أصلا كاينا
        
        $this->addFlash('success', 'Table mise à jour avec succès !');
        return $this->redirectToRoute('app_table_index');
    }

    return $this->render('table/edit.html.twig', [
        'form' => $form->createView(),
        'table' => $table
    ]);
}

#[Route('/table/delete/{id}', name: 'app_table_delete')]
#[IsGranted('ROLE_ADMIN')]
public function delete(Table $table, EntityManagerInterface $entityManager): Response
{    
    $entityManager->remove($table);
    $entityManager->flush();

    $this->addFlash('danger', 'La table a été supprimée.');
    return $this->redirectToRoute('app_table_index');
}
}
