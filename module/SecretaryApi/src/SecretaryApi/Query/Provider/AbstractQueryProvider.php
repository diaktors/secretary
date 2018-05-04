<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5
 *
 * @category Query\Provider\User2Note
 * @package  SecretaryApi
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/wesrc/secretary
 */

namespace SecretaryApi\Query\Provider;

use Doctrine\ORM\QueryBuilder;
use Zend\Stdlib\Parameters;
use ZF\Apigility\Doctrine\Server\Query\Provider\AbstractQueryProvider as ApigilityAbstractQueryProvider;

abstract class AbstractQueryProvider extends ApigilityAbstractQueryProvider
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param Parameter $parameters
     * @return QueryBuilder
     */
    protected function generateQueryParamFilter(QueryBuilder $queryBuilder, Parameters $parameters)
    {
        if (isset($parameters['query']) &&  is_array($parameters['query'])) {
            $i = 1;
            foreach ($parameters['query'] as $query) {
                $type = '=';
                if ($query['type'] == 'like') {
                    $type = 'LIKE';
                }
                $whereString = sprintf(
                    'row.%s %s :%sValue%d',
                    $query['field'],
                    $type,
                    $query['field'],
                    $i
                );
                if (!isset($query['where']) || $query['where'] == 'and') {
                    $queryBuilder->andWhere($whereString);
                }
                else if ($query['where'] == 'or') {
                    $queryBuilder->orWhere($whereString);
                }
                $queryBuilder->setParameter(sprintf('%sValue%d', $query['field'], $i), $query['value']);
                $i++;
            }
        }

        if (isset($parameters['orderBy']) &&  is_array($parameters['orderBy'])) {
            foreach ($parameters['orderBy'] as $orderKey => $orderDirection) {
                $queryBuilder->addOrderBy('row.' . $orderKey, strtoupper($orderDirection));
            }
        }

        return $queryBuilder;
    }
} 