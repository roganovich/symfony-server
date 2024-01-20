<?php

namespace App\MessageHandler;

use App\Entity\Comment;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Services\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CommentMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpamChecker            $spamChecker,
        private CommentRepository      $commentRepository,
    )
    {
    }

    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }

        if (SpamChecker::STATE_SPAM === $this->spamChecker->getSpamScore($comment, $message->getContext())) {
            $comment->setState(Comment::STATE_SPAM);
        } else {
            $comment->setState(Comment::STATE_PUBLISHED);
        }

        $this->entityManager->flush();
    }
}