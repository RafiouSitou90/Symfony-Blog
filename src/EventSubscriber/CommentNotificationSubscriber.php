<?php

namespace App\EventSubscriber;

use App\Entity\Articles;
use App\Entity\User;
use App\Event\CommentsCreatedEvent;
use App\Traits\EmailAddressTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommentNotificationSubscriber implements EventSubscriberInterface
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
     * @param CommentsCreatedEvent $commentsCreatedEvent
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function onCommentCreated (CommentsCreatedEvent $commentsCreatedEvent)
    {
        $comment = $commentsCreatedEvent->getComment();

        /** @var Articles $article */
        $article = $comment->getArticle();

        /** @var User $author */
        $author = $comment->getAuthor();

        $linkToArticle = $this->urlGenerator->generate('app_articles_show', [
            'slug' => $article->getSlug(),
            '_fragment' => 'comment_' . $comment->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from(new Address($this->getNoReplyAddress(), 'Symfony Blog'))
            ->to((string)$author->getEmail())
            ->subject('You have a new comment published')
            ->htmlTemplate('emails/comments/comment_created_email.html.twig')
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
            CommentsCreatedEvent::class => 'onCommentCreated',
        ];
    }
}
