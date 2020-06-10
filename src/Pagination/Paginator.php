<?php

namespace App\Pagination;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Exception;
use Traversable;
use function count;

class Paginator
{
    private const PAGE_SIZE = 10;

    /**
     * @var int
     */
    private int $currentPage;

    /**
     * @var int
     */
    private int $nbResults;

    /**
     * @var ArrayIterator
     */
    private ArrayIterator $results;

    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    /**
     * @var int
     */
    private int $pageSize;

    /**
     * Paginator constructor.
     * @param QueryBuilder $queryBuilder
     * @param int $pageSize
     */
    public function __construct (QueryBuilder $queryBuilder, int $pageSize = self::PAGE_SIZE)
    {
        $this->queryBuilder = $queryBuilder;
        $this->pageSize = $pageSize;
    }

    /**
     * @param int $page
     * @return $this
     * @throws Exception
     */
    public function paginate (int $page = 1)
    {
        $this->currentPage = max(1, $page);
        $firstResult = ($this->currentPage - 1) * $this->pageSize;

        $query = $this->queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        if (0 === count($this->queryBuilder->getDQLPart('join'))) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new DoctrinePaginator($query, true);

        $useOutputWalkers = count($this->queryBuilder->getDQLPart('having') ?: []) > 0;
        $paginator->setUseOutputWalkers($useOutputWalkers);

        $this->results = $paginator->getIterator();
        $this->nbResults = $paginator->count();

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return (int) ceil($this->nbResults / $this->pageSize);
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int
     */
    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    /**
     * @return bool
     */
    public function hasToPaginate(): bool
    {
        return $this->nbResults > $this->pageSize;
    }

    /**
     * @return int
     */
    public function getNumResults(): int
    {
        return $this->nbResults;
    }

    /**
     * @return Traversable
     */
    public function getResults(): Traversable
    {
        return $this->results;
    }
}
