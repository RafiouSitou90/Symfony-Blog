<?php

namespace App\Tests\Form;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoriesFormTypeTest extends TypeTestCase
{
    public function testValidSubmitData()
    {
        $formData = [
            'name' => 'category name'
        ];

        $category = (new Categories())->setName('category name');

        $form = $this->factory->create(CategoriesFormType::class, $category);
        $expected = (new Categories())->setName('category name');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $category);

        $children = $form->createView()->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
