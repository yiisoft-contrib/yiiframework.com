<?php

/**
 * In-memory ContentShare fixture for TwitterService::publish() tests. Never
 * loaded from or saved to the database.
 */
final class TwitterPublishContentShare extends \app\models\ContentShare
{
    public string $message = 'hello world';
}
