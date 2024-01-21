<?php

namespace App\MessageHandler;

use App\Entity\Comment;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Services\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CommentMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpamChecker            $spamChecker,
        private CommentRepository      $commentRepository,
        private MessageBusInterface    $bus,
        private WorkflowInterface      $commentStateMachine,
        private ?LoggerInterface       $logger = null,
    )
    {
        /**
         * WorkflowInterface Имя аргумента $commentStateMachine,
         * которое должно состоять из названия бизнес-процесса (comment)
         * и его типа (state_machine).
        */
    }

    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }

        if ($this->commentStateMachine->can($comment, 'accept')) {
            $score = $this->spamChecker->getSpamScore($comment, $message->getContext());
            $transition = match ($score) {
                2 => 'reject_spam',
                1 => 'might_be_spam',
                default => 'accept',
            };
            /**
             * Вызываем метод apply(), чтобы обновить состояние для объекта Comment,
             * который в свою очередь вызывает в этом объекте метод setState();
             */
            $this->commentStateMachine->apply($comment, $transition);
            /**
             * Сохраняем данные в базе данных
             */
            $this->entityManager->flush();
            /**
             * Повторно отправляем сообщение на шину, чтобы ещё раз запустить бизнес-процесс
             * комментария для определения следующего перехода.
             */
            $this->bus->dispatch($message);
        } elseif ($this->commentStateMachine->can($comment, 'publish')
            || $this->commentStateMachine->can($comment, 'publish_ham')
        ) {
            $this->commentStateMachine->apply($comment, $this->commentStateMachine->can($comment, 'publish') ? 'publish' : 'publish_ham');
            $this->entityManager->flush();
        } elseif ($this->logger) {
            $this->logger->debug('Dropping comment message', ['comment' => $comment->getId(), 'state' => $comment->getState()]);
        }
    }
}