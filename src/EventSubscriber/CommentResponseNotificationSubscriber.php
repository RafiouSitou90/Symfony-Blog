<?php

namespace App\EventSubscriber;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\User;
use App\Event\CommentsResponsesCreatedEvent;
use App\Traits\EmailAddressTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommentResponseNotificationSubscriber implements EventSubscriberInterface
{
    use EmailAddressTrait {
        EmailAddressTrait::__construct as private eatConstruct;
    }

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * CommentNotificationSubscriber constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param MailerInterface $mailer
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct (
        UrlGeneratorInterface $urlGenerator,
        MailerInterface $mailer,
        ParameterBagInterface $parameterBag)
    {
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
        $this->eatConstruct($parameterBag);
    }

    /**
     * @param CommentsResponsesCreatedEvent $commentResponseCreatedEvent
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function onCommentResponseCreated (CommentsResponsesCreatedEvent $commentResponseCreatedEvent)
    {
        $commentResponse = $commentResponseCreatedEvent->getCommentsResponse();

        /** @var Comments $comment */
        $comment = $commentResponse->getComment();

        /** @var Articles $article */
        $article = $comment->getArticle();

        /** @var User $commentAuthor */
        $commentAuthor = $commentResponse->getAuthor();

        $linkToArticle = $this->urlGenerator->generate('app_articles_show', [
            'slug' => $article->getSlug(),
            '_fragment' => 'comment_response_' . $commentResponse->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from(new Address($this->getNoReplyAddress(), 'Symfony Blog'))
            ->to((string)$commentAuthor->getEmail())
            ->subject('You have a new comment response published')
            ->htmlTemplate('emails/comments/comment_response_created_email.html.twig')
            ->context([
                'article' => $article,
                'linkToArticle' => $linkToArticle
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            CommentsResponsesCreatedEvent::class => 'onCommentResponseCreated',
        ];
    }
}
