<?php

namespace App\Event;

use App\Entity\CommentsResponses;
use Symfony\Contracts\EventDispatcher\Event;

class CommentsResponsesCreatedEvent extends Event
{
    /**
     * @var CommentsResponses
     */
    private CommentsResponses $commentsResponse;

    /**
     * CommentsResponsesCreatedEvent constructor.
     * @param CommentsResponses $commentsResponse
     */
    public function __construct (CommentsResponses $commentsResponse)
    {
        $this->commentsResponse = $commentsResponse;
    }

    /**
     * @return CommentsResponses
     */
    public function getCommentsResponse(): CommentsResponses
    {
        return $this->commentsResponse;
    }
}
