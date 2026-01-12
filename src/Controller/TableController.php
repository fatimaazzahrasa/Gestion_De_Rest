<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\TableRepository;


final class TableController extends AbstractController
{
    #[Route('/table', name: 'app_table')]
    public function index(TableRepository $tableRepository): Response
    {   
        $tables = $tableRepository->findAll();
        return $this->render('table/index.html.twig', [
            'tables' => $tables,
        ]);
    }
}
