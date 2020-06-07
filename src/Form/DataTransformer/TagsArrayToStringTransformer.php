<?php


namespace App\Form\DataTransformer;


use App\Entity\Tags;
use App\Repository\TagsRepository;
use Symfony\Component\Form\DataTransformerInterface;
use function Symfony\Component\String\u;

class TagsArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @var TagsRepository
     */
    private TagsRepository $tagsRepository;

    /**
     * TagsArrayToStringTransformer constructor.
     * @param TagsRepository $tagsRepository
     */
    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @param mixed $tags
     * @return mixed|string
     */
    public function transform($tags)
    {
        /** @var Tags[] $tags */
        return implode(',', $tags);
    }

    /**
     * @param mixed $string
     * @return Tags[]|array|mixed
     */
    public function reverseTransform($string)
    {
        if (null === $string || u($string)->isEmpty()) {
            return [];
        }

        $names = array_filter(array_unique(array_map('trim', u($string)->split(','))));

        $tags = $this->tagsRepository->findBy([
            'name' => $names,
        ]);

        $newNames = array_diff($names, $tags);

        foreach ($newNames as $name) {
            $tags[] = (new Tags())->setName($name);
        }

        return $tags;
    }
}
