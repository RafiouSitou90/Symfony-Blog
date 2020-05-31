<?php


namespace App\Tests\Traits;


trait AssertionErrors
{
    /**
     * @param object $object
     * @param int $number
     *
     * @return void
     */
    public function assertHasErrors (object $object, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($object);

        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath(). ' => '. $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
