<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaNewType;
use App\Form\MediaEditType;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class GalleryController extends AbstractController
{
/*------------------------acces user---------------------------------------------*/
    /**
     * @Route("/membre/galerie/", name="gallery_index")
     */
    public function index(MediaRepository $mediarepo)
    {
        return $this->render('gallery/index.html.twig', ['medias' => $mediarepo->findAllOrderByTakenAt()]);
    }

/*------------------------acces admin uniquement-----------------------------------*/

    /**
     * @Route("/admin/galerie/ajout", name="admin_gallery_new")
     */
    public function new(Request $request)
    {

        $media = new Media();
        $form = $this->createForm(MediaNewType::class, $media);
        $form->handleRequest($request);

        if ( $media->getCaption() == '' ) {
            $media->setCaption('Légende non renseignée');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $media->getUrl();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('media_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                dump($e);
            }

            $media->setUrl($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($media);
            $entityManager->flush();
            $this->addFlash('success', 'L\'image a été enregistré');


            return $this->redirect($this->generateUrl('gallery_index'));
        }

        return $this->render('gallery/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/galerie/{id}/modification", name="admin_gallery_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Media $media)
    {
        $oldUrl = $media->getUrl();

        if(!empty($oldUrl)) {
            $media->setUrl(
                new File($this->getParameter('media_directory').'/'.$oldUrl)
            );
        }

        $form = $this->createForm(MediaEditType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //si je souhaite setté un nouveau alt pour un media existant => meme procedure que pour new
            if(!is_null($media->getUrl())){

                $file = $media->getUrl();
            
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('media_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    dump($e);
                }
                
                //je stocke le nouveau nom de fichier
                $media->setUrl($fileName);

                //si je remplace mon ancienne image par une nouvelle , je teste dans un premier temps si il y en avait deja une à supprimer ;)
                if(!empty($oldUrl)){
                    //fonction native a php qui supprime des fichiers
                    unlink(
                        $this->getParameter('media_directory') .'/'.$oldUrl
                    );
                }

            } else { //sinon je garde l'ancienne valeur que j'avais deja en BDD
                $media->setUrl($oldUrl);//ancien nom de fichier
            }
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'L\'image a bien été modifié');

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('gallery/edit.html.twig', [
            'media' => $media,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/galerie/{id}/suppression", name="admin_gallery_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Media $media)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($media); // et j'efface mon entrée en bdd
        $entityManager->flush();

        // je récupère le nom de mon fichier en bdd
        $fileName = $media->getUrl();
        // j'efface le fichier uploadé dans public/uploads/media
        unlink($this->getParameter('media_directory') .'/'.$fileName);
        
        $this->addFlash('success', 'L\'image a bien été supprimé');

        return $this->redirectToRoute('gallery_index');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

}
