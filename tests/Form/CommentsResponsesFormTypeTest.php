<?php

namespace App\Tests\Form;
use App\Entity\Comments;
use App\Entity\CommentsResponses;
use App\Entity\User;
use App\Form\CommentsResponsesFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentsResponsesFormTypeTest extends TypeTestCase
{
    public function testValidSubmitData()
    {
        $formData = [
            'content' => 'Article comment response content'
        ];

        $commentResponse = (new CommentsResponses())
            ->setContent('Article comment response content')
            ->setAuthor(new User())
            ->setComment(new Comments())
        ;

        $form = $this->factory->create(CommentsResponsesFormType::class, $commentResponse);
        $expected = (new CommentsResponses())
            ->setContent('Article comment response content')
            ->setAuthor(new User())
            ->setComment(new Comments())
        ;

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $commentResponse);

        $children = $form->createView()->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
