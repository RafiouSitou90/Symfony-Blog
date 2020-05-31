<?php

namespace App\Tests\Entity;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\User;
use App\Tests\Traits\AssertionErrors;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentsTest extends KernelTestCase
{
    use AssertionErrors;

    /**
     * @return Comments
     * @throws Exception
     */
    public function getComment (): Comments
    {
        return (new Comments())
            ->setAuthor(new User())
            ->setArticle(new Articles())
            ->setContent('article comment')
            ->setPublishedAt(new DateTime('now', new DateTimeZone('America/Sao_Paulo')))
        ;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testValidComment ()
    {
        $this->assertHasErrors($this->getComment(), 0);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidComment ()
    {
        $this->assertHasErrors($this->getComment()->setContent('comment'), 1);
        $this->assertHasErrors($this->getComment()->setContent(''), 2);
    }

}
