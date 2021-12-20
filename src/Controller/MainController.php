<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\AjouterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $rep = $doctrine->getRepository(Personne::class)->findAll();

        return $this->render('main/index.html.twig', [
            'personnes' => $rep
        ]);
    }

    #[Route('/ajouter', name: 'ajouter')]
    public function ajout(Request $request, ManagerRegistry $doctrine)
    {
        $humain = new Personne();

        $form = $this->createForm(AjouterType::class, $humain);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($humain);
            $em->flush();
            return $this->redirectToRoute("main");
        }
        return $this->render('main/ajout.html.twig', [
            "formulaire" => $form->CreateView()
        ]);
    }

    #[Route('/calculer', name: 'calculer')]
    public function AffichageCalcul(ManagerRegistry $doctrine): Response
    {
        $historique = $this->Calculer($doctrine);
        return $this->render('main/calcul.html.twig', [
            'historique' => $historique
        ]);
    }

    public function Calculer(ManagerRegistry $doctrine)
    {
        $everyone = $doctrine->getRepository(Personne::class)->findAll();
        $total = 0;
        foreach ($everyone as $humain) {//
            $total += $humain->getargent();
        }

        $historique = array();
        $moyenne = $total / count($everyone);
        $donneur = array();
        $receveur = array();
        $PersonnesRembourse = array();
        foreach ($everyone as $humain) {//calcul des dettes
            $humain->setARembourser(-($humain->getargent() - $moyenne));
            if ($humain->getARembourser() > 0) {
                array_push($donneur, $humain);
            } else {
                array_push($receveur, $humain);
            }
        }
        //while (count($everyone) != count($PersonnesRembourse)) {
        for ($i = 0; $i < 60; $i++) {
            foreach ($donneur as $donateur) {
                foreach ($receveur as $recepteur) {
                    if ($recepteur->getARembourser() < 0 and $donateur->getARembourser() > 0) {
                        if ($donateur->getARembourser() > abs($recepteur->getARembourser())) {
                            $transaction = abs($recepteur->getARembourser());
                            $donateur->setARembourser($donateur->getARembourser() - $transaction);
                            $recepteur->setARembourser($recepteur->getARembourser() + $transaction);
                            array_push($historique, $donateur->getnom() . " devra " . $transaction . "€ a " . $recepteur->getnom());
                        } else {
                            $ecart = $donateur->getARembourser();
                            $donateur->setARembourser($donateur->getARembourser() - $ecart);
                            $recepteur->setARembourser($recepteur->getARembourser() + $ecart);
                            array_push($historique, $donateur->getnom() . " devra " . $ecart . "€ a " . $recepteur->getnom());
                        }
                        if ($recepteur->getARembourser() == 0)// ICI
                        {
                            array_push($PersonnesRembourse, $recepteur);
                        }
                        if ($donateur->getARembourser() == 0) {
                            array_push($PersonnesRembourse, $donateur);
                        }
                    }
                }
            }
        }
        return $historique;
    }
}
