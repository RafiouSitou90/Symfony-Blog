<?php

namespace App\Tests\Form;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\User;
use App\Form\CommentsFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentsFormTypeTest extends TypeTestCase
{
    public function testValidSubmitData()
    {
        $formData = [
            'content' => 'Article comment content'
        ];

        $comment = (new Comments())
            ->setContent('Article comment content')
            ->setAuthor(new User())
            ->setArticle(new Articles())
        ;

        $form = $this->factory->create(CommentsFormType::class, $comment);
        $expected = (new Comments())
            ->setContent('Article comment content')
            ->setAuthor(new User())
            ->setArticle(new Articles())
        ;

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $comment);

        $children = $form->createView()->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
