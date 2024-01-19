<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Carbon\Carbon;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class ConferenceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/conferences', name: 'conferences.index')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]);
    }

    #[Route('/conferences/{slug}', name: 'conferences.show')]
    public function show(Request                           $request,
                         Conference                        $conference,
                         CommentRepository                 $commentRepository,
                         #[Autowire('%photo_dir%')] string $photoDir,
    ): Response
    {
        /*
        $session = $request->getSession();
        $session->set('attribute-name', 'attribute-value');
        */
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);
            if ($photo = $form['photo']->getData()) {
                $filesystem = new Filesystem();
                $subDir = Carbon::today()->format('Ymd');
                $photoDir .= DIRECTORY_SEPARATOR . $subDir;
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();

                try {
                    $filesystem->mkdir($photoDir.DIRECTORY_SEPARATOR);
                } catch (IOExceptionInterface $exception) {
                    echo "An error occurred while creating your directory at ".$exception->getPath();
                }

                $photo->move($photoDir, $filename);
                $filename = DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR. $filename;
                $comment->setPhotoFilename($filename);
            }
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('conferences.show', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentsPaginator($conference, $offset);
        $count = $commentRepository->getCommentsCount($conference);

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'count' => $count,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset, CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form,
        ]);
    }
}
