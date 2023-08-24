<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Repository\InscriptionRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ZoneRepository $zoneRepository, InscriptionRepository $inscriptionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->request->all();

        if (count($data) > 0) {
            $inscription = new Inscription();
            $inscription->setAddress($data['address']);
            $inscription->setApproved($data['approved'] === 'y' ? true : false);
            $inscription->setChilds($data['childs']);
            $inscription->setContact($data['contact']);
            $inscription->setEmail($data['email']);
            $inscription->setFirstname($data['firstname']);
            $inscription->setLastname($data['lastname']);
            $inscription->setPrice($data['price']);
            $inscription->setWhatsapp($data['whatsapp']);
            $inscription->setAmount($data['amount'] !== '' ? $data['amount'] : NULL);

            $zoneId = explode("-", $data['zone']);
            $zone = $zoneRepository->findOneBy(['id' => $zoneId[0]]);
            $inscription->setZone($zone);

            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash("success", "Inscription Effectué !");

            return $this->redirectToRoute("app_index");
        }

        $zones = $zoneRepository->findAll();
        $datas = $inscriptionRepository->findAll();

        $stat1 = 0;
        $stat2 = 0;
        $stat3 = 0;

        foreach ($datas as $v) {
            $stat1++;

            if ($v->getAmount() !== null) {
                $stat2++;
                $stat3 += $v->getAmount();
            }
        }

        return $this->render('index.html.twig', ["zones" => $zones, "datas" => $datas, "stat1" => $stat1, "stat2" => $stat2, "stat3" => $stat3]);
    }

    #[Route('/update/{id}', name: 'app_update')]
    public function update(ZoneRepository $zoneRepository, InscriptionRepository $inscriptionRepository, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $data = $request->request->all();

        $inscription = $inscriptionRepository->findOneBy(['id' => $id]);

        $inscription->setAddress($data['address']);
        $inscription->setApproved($data['approved'] === 'y' ? true : false);
        $inscription->setChilds($data['childs']);
        $inscription->setContact($data['contact']);
        $inscription->setEmail($data['email']);
        $inscription->setFirstname($data['firstname']);
        $inscription->setLastname($data['lastname']);
        $inscription->setPrice($data['price']);
        $inscription->setWhatsapp($data['whatsapp']);
        $inscription->setAmount($data['amount'] !== '' ? $data['amount'] : NULL);

        $zoneId = explode("-", $data['zone']);
        $zone = $zoneRepository->findOneBy(['id' => $zoneId[0]]);
        $inscription->setZone($zone);

        $entityManager->flush();

        $this->addFlash("success", "Inscription Modifiée !");

        return $this->redirectToRoute("app_index");
    }

    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(InscriptionRepository $inscriptionRepository, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $inscription = $inscriptionRepository->findOneBy(['id' => $id]);

        $entityManager->remove($inscription);
        $entityManager->flush();

        $this->addFlash("success", "Inscription Supprimée !");

        return $this->redirectToRoute("app_index");
    }
}
