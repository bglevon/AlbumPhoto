<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use App\Entity\Photo;
use App\Form\AddPhotoType;
use App\Form\RemovePhotoType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Knp\Component\Pager\PaginatorInterface;

class AppController extends AbstractController
{
    //Variables of resize images
    protected $imgWidth = 1000;
    protected $imgHeight = 500;
    protected $imgThumbWidth = 250;
    protected $imgThumbHeight = 125;

    /**
     * Home page
     *
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * Display page all photos
     *
     * @Route("/photos", name="app_photos")
     *
     * @param Request               $request
     * @param PaginatorInterface    $paginator
     *
     * @return Response
     */
    public function photos(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $allMyPhotos = $entityManager->getRepository(Photo::class)->findByUser($this->getUser());

        $pagination = $paginator->paginate(
            $allMyPhotos,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('photos/photos.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Resizing images
     *
     * @param string     $photoFile
     * @param integer    $width
     * @param integer    $height
     * @param string     $path
     *
     */
    private function resizeImage($photoFile, $width, $height, $path)
    {
        // On créé l'image source et l'image de destination
        $source = imagecreatefromjpeg($photoFile);
        $destination = imagecreatetruecolor($width, $height);

        // On récupère la taille de l'image source
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);

        // On redimensionne tout !
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $largeur_source, $hauteur_source);
        imagejpeg($destination, $this->getParameter('photos_directory') . $path);
    }

    /**
     * Add photo
     *
     * @Route("/photos/add", name="app_add_photos")
     *
     * @param Request               $request
     * @param SluggerInterface      $slugger
     *
     * @return Response
     */
    public function addPhoto(Request $request, SluggerInterface $slugger): Response
    {
        $photo = new Photo();
        $form = $this->createForm(AddPhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $photoFile = $form->get('photo')->getData();

            //If the file exists
            if($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);

                //Create slug by title
                $slugByTitle = $slugger->slug($form->get('title')->getData())->lower();

                //If the title of the photo already exists in the database, then we will add a number or increment
                if(count($entityManager->getRepository(Photo::class)->findBySlug($slugByTitle)) > 0) {
                    $slugByTitleArray = explode('-', $slugByTitle);
                    $lastElementSlug = $slugByTitleArray[count($slugByTitleArray) - 1];

                    //If it is numeric, then we increment, or we add '-1' to increment if necessary later
                    if(is_numeric($lastElementSlug)) {
                        $lastElementSlug++;
                    }
                    else {
                        $lastElementSlug .= '-1';
                    }

                    $slugByTitleArray[count($slugByTitleArray) - 1] = $lastElementSlug;
                    $slugByTitle = implode('-', $slugByTitleArray);
                }

                //Create new file name from slugged title and uniqid
                $newFilename = $slugByTitle.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $this->resizeImage($photoFile, $this->imgThumbWidth, $this->imgThumbHeight, '/thumbnails/' . $newFilename);
                    $this->resizeImage($photoFile, $this->imgWidth, $this->imgHeight, '/' . $newFilename);

                    //Without resize
                    // $photoFile->move(
                    //     $this->getParameter('photos_directory'),
                    //     $newFilename
                    // );
                } catch (FileException $e) {}

                $photo->setSlug($slugByTitle);
                $photo->setUrl($slugByTitle);
                $photo->setFilename($newFilename);
                $photo->setUser($this->getUser());

                $entityManager->persist($photo);
                $entityManager->flush();

                return $this->redirectToRoute('app_photos');
            }
        }

        return $this->render('photos/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * View photo
     *
     * @Route("/photos/{slug}", name="app_photo")
     *
     * @param string       $slug
     * @param Request      $request
     *
     * @return Response
     */
    public function photo(string $slug, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $photoBySlug = $entityManager->getRepository(Photo::class)->findOneBySlug([$slug]);

        //Delete a photo
        //There is a possibility to do the deletion in a separate method (like CRUD).
        $formRemove = $this->createForm(RemovePhotoType::class);
        $formRemove->handleRequest($request);
        if ($formRemove->isSubmitted() && $formRemove->isValid()) {
            $filesystem = new Filesystem();

            $entityManager->remove($photoBySlug);
            $entityManager->flush();

            try {
                $filePath = $this->getParameter('photos_directory') . '/' . $photoBySlug->getFilename();
                $filesystem->remove([$filePath]);

                $filePathThumb = $this->getParameter('photos_directory') . '/thumbnails/' . $photoBySlug->getFilename();
                $filesystem->remove([$filePathThumb]);
            } catch (IOExceptionInterface $exception) {
                echo "Il y a un problème de suppression, merci de recommencer ultérieurement (".$exception->getPath().')';
            }

            return $this->redirectToRoute('app_photos');
        }

        return $this->render('photos/photo.html.twig', [
            'photoBySlug' => $photoBySlug,
            'formRemove' => $formRemove->createView()
        ]);
    }
}
