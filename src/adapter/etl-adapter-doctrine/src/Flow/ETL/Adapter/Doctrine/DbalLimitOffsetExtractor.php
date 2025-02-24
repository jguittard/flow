<?php

declare(strict_types=1);

namespace Flow\ETL\Adapter\Doctrine;

use function Flow\ETL\DSL\array_to_rows;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\{Extractor, FlowContext, Row\Schema};

final class DbalLimitOffsetExtractor implements Extractor
{
    private ?int $maximum = null;

    private int $pageSize = 1000;

    private ?Schema $schema = null;

    public function __construct(
        private readonly Connection $connection,
        private readonly QueryBuilder $queryBuilder,
    ) {
    }

    /**
     * @param array<OrderBy> $orderBy
     */
    public static function table(
        Connection $connection,
        Table $table,
        array $orderBy,
    ) : self {
        if (!\count($orderBy)) {
            throw new InvalidArgumentException('There must be at least one column to order by, zero given');
        }

        $queryBuilder = $connection->createQueryBuilder()
            ->select(...$table->columns ?: ['*'])
            ->from($table->name);

        foreach ($orderBy as $order) {
            $queryBuilder = $queryBuilder->orderBy($order->column, $order->order->name);
        }

        return new self(
            $connection,
            $queryBuilder,
        );
    }

    public function extract(FlowContext $context) : \Generator
    {
        if (isset($this->maximum)) {
            $total = $this->maximum;
        } else {
            $countQuery = (clone $this->queryBuilder)->select('COUNT(*)');

            // @phpstan-ignore-next-line
            if (\method_exists($countQuery, 'resetOrderBy')) {
                $countQuery->resetOrderBy();
            } else {
                /**
                 * @psalm-suppress UndefinedMethod
                 *
                 * @phpstan-ignore-next-line
                 */
                $countQuery->resetQueryPart('orderBy');
            }

            $total = (int) $this->connection->fetchOne(
                $countQuery->getSQL(),
                $countQuery->getParameters(),
                $countQuery->getParameterTypes()
            );
        }

        $totalFetched = 0;

        for ($page = 0; $page <= (new Pages($total, $this->pageSize))->pages(); $page++) {
            $offset = $page * $this->pageSize;

            $pageQuery = $this->queryBuilder
                ->setMaxResults($this->pageSize)
                ->setFirstResult($offset);

            $pageResults = $this->connection->executeQuery(
                $pageQuery->getSQL(),
                $pageQuery->getParameters(),
                $pageQuery->getParameterTypes()
            )->fetchAllAssociative();

            foreach ($pageResults as $row) {
                $signal = yield array_to_rows($row, $context->entryFactory(), [], $this->schema);

                if ($signal === Extractor\Signal::STOP) {
                    return;
                }

                $totalFetched++;

                if (null !== $this->maximum && $totalFetched >= $this->maximum) {
                    break;
                }
            }
        }
    }

    public function withMaximum(int $maximum) : self
    {
        if ($maximum <= 0) {
            throw new InvalidArgumentException('Maximum must be greater than 0, got ' . $maximum);
        }

        $this->maximum = $maximum;

        return $this;
    }

    public function withPageSize(int $pageSize) : self
    {
        if ($pageSize <= 0) {
            throw new InvalidArgumentException('Page size must be greater than 0, got ' . $pageSize);
        }

        $this->pageSize = $pageSize;

        return $this;
    }

    public function withSchema(Schema $schema) : self
    {
        $this->schema = $schema;

        return $this;
    }
}
