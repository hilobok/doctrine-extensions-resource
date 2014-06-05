<?php

namespace Anh\DoctrineResource;

/**
 * Paginator passed to ResourceRepository must be compatible with this interface
 */
interface ResourcePaginatorInterface
{
    /**
     * Returns paginated data.
     * @param  mixed   $data  Dataset for pagination. (Array, Query, QueryBuilder etc.)
     * @param  integer $page  Page number to retrieve.
     * @param  integer $limit Number of items per page.
     * @return mixed   Paginated data.
     */
    public function paginate($data, $page, $limit);
}
