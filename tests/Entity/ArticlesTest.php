<?php


namespace App\Tests\Entity;


use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\User;
use App\Tests\Traits\AssertionErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ArticlesTest
 *
 * @package App\Tests\Entity
 */
class ArticlesTest extends KernelTestCase
{
    use AssertionErrors;
    use FixturesTrait;

    /**
     * @return Articles
     */
    public function getArticle ()
    {
        /** @var User[] $user */
        $user = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UserFixturesTest.yaml'
        ]);

        /** @var Categories[] $category */
        $category = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/CategoryFixturesTest.yaml'
        ]);

        $imageFile = new UploadedFile(
            dirname(__DIR__) . '/DataFixtures/Images/Article.jpg', 'Article.jpg'
        );

        return (new Articles())
            ->setAuthor($user['user'])
            ->setCategory($category['category'])
            ->setTitle('New article title')
            ->setSummary('New article summary')
            ->setContent('New article content text')
            ->setContent('New article content text')
            ->setImageFile($imageFile)
            ->setArticleStatus(Articles::DRAFT())
            ->setCommentsStatus(Articles::COMMENT_OPENED())
        ;
    }

    /**
     * @return void
     */
    public function testValidArticle ()
    {
        $this->assertHasErrors($this->getArticle(), 0);
    }

    /**
     * @return void
     */
    public function testInvalidTitle ()
    {
        $this->assertHasErrors($this->getArticle()->setTitle('title'), 1);
    }

    /**
     * @return void
     */
    public function testBlankTitle ()
    {
        $this->assertHasErrors($this->getArticle()->setTitle(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidSummary ()
    {
        $this->assertHasErrors($this->getArticle()->setSummary('summary'), 1);
    }

    /**
     * @return void
     */
    public function testBlankSummary ()
    {
        $this->assertHasErrors($this->getArticle()->setSummary(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidContent ()
    {
        $this->assertHasErrors($this->getArticle()->setContent('summary'), 1);
    }

    /**
     * @return void
     */
    public function testBlankContent ()
    {
        $this->assertHasErrors($this->getArticle()->setContent(''), 2);
    }

    /**
     * @return void
     */
    public function testNullCategory ()
    {
        $this->assertHasErrors($this->getArticle()->setCategory(null), 0);
    }

    /**
     * @return void
     */
    public function testNullAuthor ()
    {
        $this->assertHasErrors($this->getArticle()->setAuthor(null), 0);
    }

}
