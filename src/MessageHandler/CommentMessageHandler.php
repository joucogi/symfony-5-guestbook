<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CommentMessageHandler implements MessageHandlerInterface {
    private $spamChecker;
    private $entityManager;
    private $commentRepository;
    private $bus;
    private $workflow;
    private $mailer;
    private $adminEmail;
    private $logger;

    public function __construct(
        SpamChecker $spamChecker,
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        MessageBusInterface $bus,
        WorkflowInterface $commentStateMachine,
        MailerInterface $mailer,
        string $adminEmail,
        LoggerInterface $logger = null
    ) {
        $this->spamChecker       = $spamChecker;
        $this->entityManager     = $entityManager;
        $this->commentRepository = $commentRepository;
        $this->bus               = $bus;
        $this->workflow          = $commentStateMachine;
        $this->logger            = $logger;
        $this->mailer            = $mailer;
        $this->adminEmail        = $adminEmail;
    }

    public function __invoke(CommentMessage $message) {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }

        if ($this->workflow->can($comment, 'accept')) {
            $score = $this->spamChecker->getSpamScore($comment, $message->getContext());

            $transition = 'accept';
            if (2 === $score) {
                $transition = 'reject_spam';
            } else if (1 === $score) {
                $transition = 'might_be_spam';
            }

            $this->applyWorkflow($comment, $transition);
            $this->bus->dispatch($message);

        } else if ($this->workflow->can($comment, 'publish') || $this->workflow->can($comment, 'publish_ham')) {
            $this->mailer->send((new NotificationEmail()))
                         ->subject('New comment posted')
                         ->htmlTemplate('emails/comment_notification.html.twig')
                         ->from($this->adminEmail)
                         ->to($this->adminEmail)
                         ->context(['comment' => $comment]);

        } else if ($this->logger) {
            $this->logger->debug('Dropping comment message',
                [
                    'comment' => $comment->getId(),
                    'state'   => $comment->getState()
                ]);
        }
    }

    private function applyWorkflow($comment, $transition): void {
        $this->workflow->apply($comment, $transition);
        $this->entityManager->flush();
    }
}