<?php

namespace App\Tests\Traits;

use Symfony\Component\Validator\Validator\ValidatorInterface;

trait AssertionErrors
{
    /**
     * @param Object $object
     * @param int $number
     *
     * @return void
     */
    public function assertHasErrors (Object $object, int $number = 0)
    {
        self::bootKernel();
        /** @var ValidatorInterface $validator */
        $validator = self::$container->get('validator');
        $errors = $validator->validate($object);

        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath(). ' => '. $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
