<?php
namespace App\Controller;

use App\Entity\Documents;
use App\Form\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DocumentController extends AbstractController
{
    public function __invoke(Request $request): Documents
    {
        $document = new Documents();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();

            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(
                        $this->getParameter('documents_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new BadRequestHttpException('Erreur lors de l\'upload du document.');
                }
                
                $document->setNomFichier($newFilename);
                $document->setFormat($uploadedFile->guessExtension());
            }

            return $document;
        }

        throw new BadRequestHttpException('Données invalides ou fichier manquant. Vérifiez vos champs.');
    }
}
