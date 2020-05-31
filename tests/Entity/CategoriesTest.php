<?php


namespace App\Tests\Entity;


use App\Entity\Categories;
use App\Tests\Traits\AssertionErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoriesTest extends KernelTestCase
{
    use AssertionErrors;
    use FixturesTrait;

    /**
     * @return Categories
     */
    public function getCategory (): Categories
    {
        return (new Categories())
            ->setName('New category')
        ;
    }

    /**
     * @return void
     */
    public function testValidCategory ()
    {
        $this->assertHasErrors($this->getCategory(), 0);
    }

    /**
     * @return void
     */
    public function testValidName ()
    {
        $this->assertHasErrors($this->getCategory()->setName('category'), 0);
        $this->assertHasErrors($this->getCategory()->setName('Another category'), 0);
        $this->assertHasErrors($this->getCategory()->setName('Another one again'), 0);
    }

    /**
     * @return void
     */
    public function testInvalidName ()
    {
        $this->assertHasErrors($this->getCategory()->setName('cate'), 1);
        $this->assertHasErrors($this->getCategory()->setName(''), 2);
    }

    /**
     * @return void
     */
    public function testCategoryWithDuplicatesSlug ()
    {
        $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/CategoryFixturesTest.yaml'
        ]);

        $this->assertHasErrors(
            $this->getCategory()->setName('First category')->setSlug('first-category'), 1);
    }

}