<?php

namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\web\Request;

/**
 * KeysetPagination implements cursor-based (keyset) pagination.
 *
 * This type of pagination is more efficient than offset-based pagination
 * for large datasets because it uses indexed columns for navigation
 * rather than counting rows with OFFSET.
 */
class KeysetPagination extends BaseObject
{
    /**
     * @var int the number of items per page
     */
    public int $pageSize = 50;

    /**
     * @var string the name of the parameter storing the cursor value
     */
    public string $cursorParam = 'cursor';

    /**
     * @var string the name of the parameter storing the direction (next/prev)
     */
    public string $directionParam = 'dir';

    /**
     * @var string|null the current cursor value
     */
    public ?string $cursor = null;

    /**
     * @var string the direction of pagination: 'next' or 'prev'
     */
    public string $direction = 'next';

    /**
     * @var string|null cursor for next page
     */
    public ?string $nextCursor = null;

    /**
     * @var string|null cursor for previous page
     */
    public ?string $prevCursor = null;

    /**
     * @var bool whether there are more items in the next direction
     */
    public bool $hasNextPage = false;

    /**
     * @var bool whether there are more items in the previous direction
     */
    public bool $hasPrevPage = false;

    /**
     * @var ?array the route for creating pagination URLs
     */
    public ?array $route = null;

    /**
     * @var array additional parameters to include in pagination URLs
     */
    public array $params = [];

    /**
     * Initializes the pagination object by reading cursor from request.
     */
    public function init(): void
    {
        parent::init();

        $request = Yii::$app->getRequest();
        if ($request instanceof Request) {
            if ($this->cursor === null) {
                $this->cursor = $request->getQueryParam($this->cursorParam);
            }
            $dir = $request->getQueryParam($this->directionParam, 'next');
            if (in_array($dir, ['next', 'prev'], true)) {
                $this->direction = $dir;
            }
        }
    }

    /**
     * Encodes cursor values for URL usage.
     *
     * @param array $values the values to encode
     * @return string the encoded cursor
     */
    public static function encodeCursor(array $values): string
    {
        return base64_encode(json_encode($values, JSON_THROW_ON_ERROR));
    }

    /**
     * Decodes a cursor string back to values.
     *
     * @param string|null $cursor the cursor to decode
     * @return array|null the decoded values or null if invalid
     */
    public static function decodeCursor(?string $cursor): ?array
    {
        if ($cursor === null || $cursor === '') {
            return null;
        }

        $decoded = base64_decode($cursor, true);
        if ($decoded === false) {
            return null;
        }

        $values = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($values)) {
            return null;
        }

        return $values;
    }

    /**
     * Creates the URL for the next page.
     *
     * @return string|null the URL or null if no next page
     */
    public function getNextPageUrl(): ?string
    {
        if (!$this->hasNextPage || $this->nextCursor === null) {
            return null;
        }

        return $this->createUrl($this->nextCursor, 'next');
    }

    /**
     * Creates the URL for the previous page.
     *
     * @return string|null the URL or null if no previous page
     */
    public function getPreviousPageUrl(): ?string
    {
        if (!$this->hasPrevPage || $this->prevCursor === null) {
            return null;
        }

        return $this->createUrl($this->prevCursor, 'prev');
    }

    /**
     * Creates a pagination URL.
     *
     * @param string $cursor the cursor value
     * @param string $direction the direction
     * @return string the URL
     */
    protected function createUrl(string $cursor, string $direction): string
    {
        $params = $this->params;
        $params[$this->cursorParam] = $cursor;
        $params[$this->directionParam] = $direction;

        if ($this->route !== null) {
            $route = $this->route;
            $route[0] = $route[0] ?? '';
            return Yii::$app->urlManager->createUrl(array_merge($route, $params));
        }

        return Yii::$app->urlManager->createUrl(array_merge([''], $params));
    }

    /**
     * Creates the URL for the first page (no cursor).
     *
     * @return string the URL
     */
    public function getFirstPageUrl(): string
    {
        $params = $this->params;
        unset($params[$this->cursorParam], $params[$this->directionParam]);

        if ($this->route !== null) {
            $route = $this->route;
            $route[0] = $route[0] ?? '';
            return Yii::$app->urlManager->createUrl(array_merge($route, $params));
        }

        return Yii::$app->urlManager->createUrl(array_merge([''], $params));
    }
}
