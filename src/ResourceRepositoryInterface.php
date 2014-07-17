<?php

namespace Anh\DoctrineResource;

use Doctrine\Common\Persistence\ObjectRepository;

interface ResourceRepositoryInterface extends ObjectRepository
{
    /**
     * Sets paginator.
     * @param mixed $paginator Paginator should be compatible with ResourcePaginatorInterface.
     */
    public function setPaginator($paginator);

    public function setRules(array $rules = null);

    /**
     * [paginate description]
     * @param  integer $page     Page number to retrieve.
     * @param  integer $limit    Number of elements per page.
     * @param  array   $criteria Search criteria.
     * @param  array   $sorting  Sorting order.
     * @return mixed   Paginated data for given page number and limit.
     */
    public function paginate($page, $limit, array $criteria = null, array $sorting = null);

    /**
     * [fetch description]
     * @param  array $criteria Search criteria.
     * @param  array $sorting  Sorting order.
     * @return mixed Fetched data.
     */
    public function fetch(array $criteria = null, array $sorting = null, $limit = null, $offset = null);

    /**
     * [fetchOne description]
     * @param  array $criteria Search criteria.
     * @param  array $sorting  Sorting order.
     * @return mixed Fetched data.
     */
    public function fetchOne(array $criteria = null, array $sorting = null);
}
