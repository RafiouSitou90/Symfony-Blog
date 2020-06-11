<?php

namespace App\Event;

use App\Entity\Comments;
use Symfony\Contracts\EventDispatcher\Event;

class CommentsCreatedEvent extends Event
{
    /**
     * @var Comments
     */
    private Comments $comment;

    /**
     * CommentsCreatedEvent constructor.
     * @param Comments $comment
     */
    public function __construct (Comments $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return Comments
     */
    public function getComment(): Comments
    {
        return $this->comment;
    }
}
