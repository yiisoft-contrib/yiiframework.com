<?php

namespace app\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\QueryInterface;

/**
 * KeysetDataProvider implements a data provider based on keyset/cursor pagination.
 *
 * This is more efficient than offset-based pagination for large datasets
 * because it uses indexed columns (like primary key or rank) for navigation.
 */
class KeysetDataProvider extends BaseDataProvider
{
    /**
     * @var ?QueryInterface the query that is used to fetch data models
     */
    public ?QueryInterface $query;

    /**
     * @var string|callable the column to use for keyset pagination.
     * This should be an indexed column for best performance.
     */
    public $key = 'id';

    /**
     * @var string the secondary column to use for tie-breaking when primary key values are not unique.
     * Usually the primary key column.
     */
    public string $secondaryKey = 'id';

    /**
     * @var int the sort direction for the key column. SORT_ASC or SORT_DESC.
     */
    public int $keySort = SORT_ASC;

    /**
     * @var KeysetPagination|array|false the pagination object or configuration.
     * Set to `false` to disable pagination.
     */
    public $pagination;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        if ($this->query === null) {
            throw new InvalidConfigException('The "query" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels(): array
    {
        $query = clone $this->query;
        $pagination = $this->getPagination();

        if ($pagination === false) {
            return $query->all();
        }

        $pageSize = $pagination->pageSize;
        $cursor = KeysetPagination::decodeCursor($pagination->cursor);
        $direction = $pagination->direction;

        // Build the keyset condition
        if ($cursor !== null && isset($cursor['key'], $cursor['id'])) {
            $keyValue = $cursor['key'];
            $idValue = $cursor['id'];

            if ($direction === 'prev') {
                // Going backwards: get items before the cursor
                if ($this->keySort === SORT_ASC) {
                    $query->andWhere([
                        'or',
                        ['<', $this->key, $keyValue],
                        [
                            'and',
                            [$this->key => $keyValue],
                            ['<', $this->secondaryKey, $idValue]
                        ]
                    ]);
                    // Reverse sort to get the last N items before cursor
                    $query->orderBy([
                        $this->key => SORT_DESC,
                        $this->secondaryKey => SORT_DESC
                    ]);
                } else {
                    $query->andWhere([
                        'or',
                        ['>', $this->key, $keyValue],
                        [
                            'and',
                            [$this->key => $keyValue],
                            ['>', $this->secondaryKey, $idValue]
                        ]
                    ]);
                    $query->orderBy([
                        $this->key => SORT_ASC,
                        $this->secondaryKey => SORT_ASC
                    ]);
                }
            } elseif ($this->keySort === SORT_ASC) {
                $query->andWhere([
                    'or',
                    ['>', $this->key, $keyValue],
                    [
                        'and',
                        [$this->key => $keyValue],
                        ['>', $this->secondaryKey, $idValue]
                    ]
                ]);
                $query->orderBy([
                    $this->key => SORT_ASC,
                    $this->secondaryKey => SORT_ASC
                ]);
            } else {
                $query->andWhere([
                    'or',
                    ['<', $this->key, $keyValue],
                    [
                        'and',
                        [$this->key => $keyValue],
                        ['<', $this->secondaryKey, $idValue]
                    ]
                ]);
                $query->orderBy([
                    $this->key => SORT_DESC,
                    $this->secondaryKey => SORT_DESC
                ]);
            }
        } elseif ($this->keySort === SORT_ASC) {
            $query->orderBy([
                $this->key => SORT_ASC,
                $this->secondaryKey => SORT_ASC
            ]);
        } else {
            $query->orderBy([
                $this->key => SORT_DESC,
                $this->secondaryKey => SORT_DESC
            ]);
        }

        // Fetch one extra to determine if there are more pages
        $query->limit($pageSize + 1);
        $models = $query->all();

        // Check if there are more items
        $hasMore = count($models) > $pageSize;
        if ($hasMore) {
            // Remove the extra item
            array_pop($models);
        }

        // If we went backwards, reverse the results to maintain correct order
        if ($direction === 'prev' && $cursor !== null) {
            $models = array_reverse($models);
        }

        // Update pagination cursors
        if (!empty($models)) {
            $firstModel = reset($models);
            $lastModel = end($models);

            // Set cursors for next/prev navigation
            $pagination->nextCursor = KeysetPagination::encodeCursor([
                'key' => $lastModel->{$this->key},
                'id' => $lastModel->{$this->secondaryKey}
            ]);

            $pagination->prevCursor = KeysetPagination::encodeCursor([
                'key' => $firstModel->{$this->key},
                'id' => $firstModel->{$this->secondaryKey}
            ]);

            if ($direction === 'prev') {
                $pagination->hasPrevPage = $hasMore;
                // Check if there's a next page by seeing if we have a cursor we came from
                $pagination->hasNextPage = ($cursor !== null);
            } else {
                $pagination->hasNextPage = $hasMore;
                // We have a previous page if we started from a cursor
                $pagination->hasPrevPage = ($cursor !== null);
            }
        } else {
            $pagination->hasNextPage = false;
            $pagination->hasPrevPage = ($cursor !== null);
        }

        return $models;
    }

    /**
     * @inheritdoc
     */
    protected function prepareKeys($models): array
    {
        $keys = [];
        if ($this->key !== null) {
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }
            return $keys;
        }

        if ($this->query instanceof ActiveQueryInterface) {
            /* @var $class \yii\db\ActiveRecordInterface */
            $class = $this->query->modelClass;
            $pks = $class::primaryKey();
            if (count($pks) === 1) {
                $pk = $pks[0];
                foreach ($models as $model) {
                    $keys[] = $model[$pk];
                }
            } else {
                foreach ($models as $model) {
                    $kk = [];
                    foreach ($pks as $pk) {
                        $kk[$pk] = $model[$pk];
                    }
                    $keys[] = $kk;
                }
            }
            return $keys;
        }

        return array_keys($models);
    }

    protected function prepareTotalCount(): int
    {
        // Not implemented on purpose.
        return 0;
    }

    public function setTotalCount($value): void
    {
        // Not implemented on purpose.
    }

    /**
     * Returns the pagination object.
     *
     * @return KeysetPagination|false
     */
    public function getPagination()
    {
        if ($this->pagination === null) {
            $this->pagination = new KeysetPagination();
        } elseif (is_array($this->pagination)) {
            $config = $this->pagination;
            $config['class'] = KeysetPagination::class;
            $this->pagination = Yii::createObject($config);
        }

        return $this->pagination;
    }
}
