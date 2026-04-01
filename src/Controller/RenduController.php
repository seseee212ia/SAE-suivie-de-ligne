<?php
namespace App\Controller;

use App\Entity\Rendu;
use App\Form\RenduType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RenduController extends AbstractController
{
    public function __invoke(Request $request): Rendu
    {
        $rendu = new Rendu();
        $form = $this->createForm(RenduType::class, $rendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();

            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(
                        $this->getParameter('rendus_directory'),
                        $newFilename
                    );

                    $rendu->setFileName($newFilename);
                    $rendu->setFile($uploadedFile);

                } catch (FileException $e) {
                    throw new BadRequestHttpException('Erreur lors de l\'upload du rendu.');
                }
            }

            return $rendu;
        }

        throw new BadRequestHttpException('Données invalides ou fichier manquant. Vérifiez vos champs.');
    }
}
