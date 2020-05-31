<?php

namespace App\Tests\Entity;

use App\Entity\Comments;
use App\Entity\CommentsResponses;
use App\Entity\User;
use App\Tests\Traits\AssertionErrors;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentsResponsesTest extends KernelTestCase
{
    use AssertionErrors;

    /**
     * @return CommentsResponses
     * @throws Exception
     */
    public function getCommentResponse (): CommentsResponses
    {
        return (new CommentsResponses())
            ->setAuthor(new User())
            ->setComment(new Comments())
            ->setContent('article comment response')
            ->setPublishedAt(new DateTime('now', new DateTimeZone('America/Sao_Paulo')))
        ;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testValidComment ()
    {
        $this->assertHasErrors($this->getCommentResponse(), 0);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidCommentResponse ()
    {
        $this->assertHasErrors($this->getCommentResponse()->setContent('response'), 1);
        $this->assertHasErrors($this->getCommentResponse()->setContent(''), 2);
    }

}
