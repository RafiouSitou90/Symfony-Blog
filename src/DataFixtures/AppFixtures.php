<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Comments;
use App\Entity\CommentsResponses;
use App\Entity\Tags;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\AbstractString;
use function array_slice;
use function Symfony\Component\String\u;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    /**
     * @var User
     */
    private User $user;
    /**
    * @var User
    */
    private User $admin;
    /**
    * @var User
    */
    private User $super_admin;

    /**
     * @var Categories[] $tabCategories
     */
    private array $tabCategories = [];

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct (
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadCategories($manager);
        $this->loadTags($manager);
        $this->loadArticles($manager);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    private function loadUser (ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setUsername('Username')
            ->setEmail('user@domain.com')
            ->setFullName('User FullName')
            ->setProfile(null)
            ->setPassword($this->passwordEncoder->encodePassword($user, '123456789'))
        ;

        $admin = new User();
        $admin
            ->setUsername('AdminName')
            ->setEmail('admin@domain.com')
            ->setFullName('Admin FullName')
            ->setRoles(['ROLE_ADMIN'])
            ->setProfile(null)
            ->setPassword($this->passwordEncoder->encodePassword($admin, '123456789'))
        ;

        $super_admin = new User();
        $super_admin
            ->setUsername('SuperAdmin')
            ->setEmail('superadmin@domain.com')
            ->setFullName('Super Admin FullName')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setProfile(null)
            ->setPassword($this->passwordEncoder->encodePassword($super_admin, '123456789'))
        ;
        $manager->persist($user);
        $manager->persist($admin);
        $manager->persist($super_admin);
        $manager->flush();

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => 'Username']);
        /** @var User $admin */
        $admin = $this->userRepository->findOneBy(['username' => 'AdminName']);
        /** @var User $super_admin */
        $super_admin = $this->userRepository->findOneBy(['username' => 'SuperAdmin']);
        $user->setToken(null)->setIsActive(true);
        $admin->setToken(null)->setIsActive(true);
        $super_admin->setToken(null)->setIsActive(true);
        $manager->flush();

        $this->user = $user;
        $this->admin = $admin;
        $this->super_admin = $super_admin;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    private function loadCategories (ObjectManager $manager)
    {
        foreach ($this->getCategoriesData() as $index => $value ) {
            $category = (new Categories())
                ->setName($value)
            ;
            $manager->persist($category);

            $this->tabCategories[] = $category;
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     *
     * @return void
     */
    private function loadArticles (ObjectManager $manager)
    {
        foreach ($this->getCategoriesData() as $index => $value ) {
            for ($i = 0; $i < random_int(1, count($this->getCategoriesData())); $i++) {
                $article = (new Articles())
                    ->setTitle($this->getSentences()[$i])
                    ->setSummary($this->getRandomSummary())
                    ->setContent($this->getArticleContent())
                    ->setCategory($this->tabCategories[$i])
                    ->setAuthor($this->getRandomAuthor())
                    ->setPublishedAt(new DateTime('now - '.$i.'days'))
                    ->setArticleStatus(Articles::PUBLISHED())
                    ->setCommentsStatus(Articles::COMMENT_OPENED())
//                    ->setImageFile($this->getRandomUploadedFile())
                    ->setImageName($this->faker
                        ->file(
                        dirname(__DIR__) . '/DataFixtures/assets/articles',
                        dirname(__DIR__) .'/../public/uploads/images/articles' , $fullPath = false
                        )
                    )
                ;

                for ($k = 0; $k < random_int(2, count($this->getTagsData())); $k++) {
                    $article->addTag(...$this->getRandomTags());
                }

                for ($j = 0; $j < random_int(5, 10); $j++) {
                    $comment = (new Comments())
                        ->setAuthor($this->getRandomAuthor())
                        ->setContent($this->getSentences()[$j])
                        ->setPublishedAt(new DateTime('now - '.$i.'days'))
                    ;

                    for ($l = 0; $l < random_int(5, 10); $l++) {
                        $commentResponse = (new CommentsResponses())
                            ->setAuthor($this->getRandomAuthor())
                            ->setContent($this->getSentences()[$j])
                            ->setPublishedAt(new DateTime('now - '.$i.'days'))
                        ;
                        $comment->addCommentResponse($commentResponse);
                    }

                    $article->addComment($comment);
                }

                $manager->persist($article);
                $manager->flush();
            }
        }
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    private function loadTags(ObjectManager $manager): void
    {
        foreach ($this->getTagsData() as $index => $name) {
            $tag = new Tags();
            $tag->setName($name);

            $manager->persist($tag);
            $this->addReference('tag-'.$name, $tag);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    private function getTagsData ()
    {
        return [
            'Cantare',
            'vix',
            'ducunt',
            'brevis',
            'zirbus',
            'Nunquam',
            'attrahendam',
            'calceus',
            'Idoleum',
            'experimentums'
        ];
    }

    /**
     * @return string[]
     */
    private function getSentences ()
    {
        return [
            'All prime bodies hurt each other, only mediocre spirits have a life.',
            'When the reef screams for pantano river, all corsairs love real, coal-black cockroachs.',
            'Confucius says: conclusion, career, control.',
            'Everyone loves the asperity of ramen sauce rinsed with tasty black cardamon.',
            'Die mechanically like an interstellar pathway.',
            'For a sliced minced fritters, add some remoulade and black pepper.',
            'Zucchini combines greatly with dried peanut butter.',
            'When the particle flies for hyperspace, all transporters influence harmless, ship-wide processors.',
            'The dosi reproduces turbulence like a harmless ship.',
            'The post-apocalyptic klingon tightly converts the sonic shower.'
        ];
    }

    /**
     * @return string[]
     */
    private function getCategoriesData ()
    {
        return [
          'Galatae talis axona est',
          'Magical meditations visualizes most loves',
          'Yes, there is zion, it rises with grace',
          'Fidess sunt calceuss de neuter orgia',
          'Rusticus galatae recte attrahendams competition est',
          'Quadra de bi-color ratione, vitare cursus!',
          'Cur vita persuadere',
          'Nuptias trabem, tanquam clemens orexis',
          'Placidus ollas ducunt ad mens',
          'Racanas observare in clemens tubinga'
        ];
    }

    /**
     * @return string
     */
    private function getArticleContent ()
    {
        return
            <<<'MARKDOWN'
                Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
                incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
                reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                deserunt mollit anim id est laborum.
                
                  * Ut enim ad minim veniam
                  * Quis nostrud exercitation *ullamco laboris*
                  * Nisi ut aliquip ex ea commodo consequat
                
                Praesent id fermentum lorem. Ut est lorem, fringilla at accumsan nec, euismod at
                nunc. Aenean mattis sollicitudin mattis. Nullam pulvinar vestibulum bibendum.
                Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
                himenaeos. Fusce nulla purus, gravida ac interdum ut, blandit eget ex. Duis a
                luctus dolor.
                
                Integer auctor massa maximus nulla scelerisque accumsan. *Aliquam ac malesuada*
                ex. Pellentesque tortor magna, vulputate eu vulputate ut, venenatis ac lectus.
                Praesent ut lacinia sem. Mauris a lectus eget felis mollis feugiat. Quisque
                efficitur, mi ut semper pulvinar, urna urna blandit massa, eget tincidunt augue
                nulla vitae est.
                
                Ut posuere aliquet tincidunt. Aliquam erat volutpat. **Class aptent taciti**
                sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi
                arcu orci, gravida eget aliquam eu, suscipit et ante. Morbi vulputate metus vel
                ipsum finibus, ut dapibus massa feugiat. Vestibulum vel lobortis libero. Sed
                tincidunt tellus et viverra scelerisque. Pellentesque tincidunt cursus felis.
                Sed in egestas erat.
                
                Aliquam pulvinar interdum massa, vel ullamcorper ante consectetur eu. Vestibulum
                lacinia ac enim vel placerat. Integer pulvinar magna nec dui malesuada, nec
                congue nisl dictum. Donec mollis nisl tortor, at congue erat consequat a. Nam
                tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
                tincidunt, faucibus nisl in, aliquet libero.
            MARKDOWN
        ;
    }

    /**
     * @param int $maxLength
     * @return AbstractString
     */
    private function getRandomSummary (int $maxLength = 255)
    {
        $phrases = $this->getSentences();
        shuffle($phrases);

        do {
            $text = u('. ')->join($phrases)->append('.');
            array_pop($phrases);
        } while ($text->length() > $maxLength);

        return $text;
    }

    /**
     * @return User
     * @throws Exception
     */
    private function getRandomAuthor ()
    {
        $author = $this->user;
        switch (random_int(1, 3)) {
            case 1:
                $author = $this->user;
                break;
            case 2:
                $author = $this->admin;
                break;
            case 3:
                $author = $this->super_admin;
                break;
            default:
                $author = $this->user;
        }

        return $author;
    }

    /**
     * @return UploadedFile
     * @throws Exception
     */
    private function getRandomUploadedFile ()
    {
        $image = random_int(1, 5);

        return new UploadedFile(
            dirname(__DIR__) . "/DataFixtures/assets/articles/$image.jpg",
            "$image.jpg", "image/jpeg"
        );
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getRandomTags(): array
    {
        $tagNames = $this->getTagsData();
        shuffle($tagNames);
        $selectedTags = array_slice($tagNames, 0, random_int(2, 4));

        return array_map(fn ($tagName) => $this->getReference('tag-'.$tagName) , $selectedTags);
    }
}
