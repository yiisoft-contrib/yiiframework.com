<?php

namespace app\components;

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
     * @var QueryInterface the query that is used to fetch data models
     */
    public $query;

    /**
     * @var string|callable the column to use for keyset pagination.
     * This should be an indexed column for best performance.
     */
    public $keyColumn = 'id';

    /**
     * @var string the secondary column to use for tie-breaking when primary key values are not unique.
     * Usually the primary key column.
     */
    public $secondaryKeyColumn = 'id';

    /**
     * @var int the sort direction for the key column. SORT_ASC or SORT_DESC.
     */
    public $keySort = SORT_ASC;

    /**
     * @var KeysetPagination|array|false the pagination object or configuration.
     * Set to false to disable pagination.
     */
    public $pagination;

    /**
     * @var int total count of items (optional, expensive to calculate)
     */
    private $_totalCount;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->query === null) {
            throw new InvalidConfigException('The "query" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
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
                        ['<', $this->keyColumn, $keyValue],
                        [
                            'and',
                            [$this->keyColumn => $keyValue],
                            ['<', $this->secondaryKeyColumn, $idValue]
                        ]
                    ]);
                    // Reverse sort to get the last N items before cursor
                    $query->orderBy([
                        $this->keyColumn => SORT_DESC,
                        $this->secondaryKeyColumn => SORT_DESC
                    ]);
                } else {
                    $query->andWhere([
                        'or',
                        ['>', $this->keyColumn, $keyValue],
                        [
                            'and',
                            [$this->keyColumn => $keyValue],
                            ['>', $this->secondaryKeyColumn, $idValue]
                        ]
                    ]);
                    $query->orderBy([
                        $this->keyColumn => SORT_ASC,
                        $this->secondaryKeyColumn => SORT_ASC
                    ]);
                }
            } else {
                // Going forward: get items after the cursor
                if ($this->keySort === SORT_ASC) {
                    $query->andWhere([
                        'or',
                        ['>', $this->keyColumn, $keyValue],
                        [
                            'and',
                            [$this->keyColumn => $keyValue],
                            ['>', $this->secondaryKeyColumn, $idValue]
                        ]
                    ]);
                    $query->orderBy([
                        $this->keyColumn => SORT_ASC,
                        $this->secondaryKeyColumn => SORT_ASC
                    ]);
                } else {
                    $query->andWhere([
                        'or',
                        ['<', $this->keyColumn, $keyValue],
                        [
                            'and',
                            [$this->keyColumn => $keyValue],
                            ['<', $this->secondaryKeyColumn, $idValue]
                        ]
                    ]);
                    $query->orderBy([
                        $this->keyColumn => SORT_DESC,
                        $this->secondaryKeyColumn => SORT_DESC
                    ]);
                }
            }
        } else {
            // No cursor - start from beginning
            if ($this->keySort === SORT_ASC) {
                $query->orderBy([
                    $this->keyColumn => SORT_ASC,
                    $this->secondaryKeyColumn => SORT_ASC
                ]);
            } else {
                $query->orderBy([
                    $this->keyColumn => SORT_DESC,
                    $this->secondaryKeyColumn => SORT_DESC
                ]);
            }
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
                'key' => $lastModel->{$this->keyColumn},
                'id' => $lastModel->{$this->secondaryKeyColumn}
            ]);

            $pagination->prevCursor = KeysetPagination::encodeCursor([
                'key' => $firstModel->{$this->keyColumn},
                'id' => $firstModel->{$this->secondaryKeyColumn}
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
    protected function prepareKeys($models)
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

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        if ($this->_totalCount !== null) {
            return $this->_totalCount;
        }

        // Note: This is expensive for large tables. 
        // Consider caching or not using total count with keyset pagination.
        $query = clone $this->query;
        return (int) $query->limit(-1)->offset(-1)->orderBy([])->count('*');
    }

    /**
     * Sets the total count manually to avoid expensive COUNT query.
     *
     * @param int $count
     */
    public function setTotalCount($count)
    {
        $this->_totalCount = $count;
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
            $this->pagination = \Yii::createObject($config);
        }

        return $this->pagination;
    }
}
