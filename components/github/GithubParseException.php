<?php
namespace app\components\github;

class GithubParseException extends GithubException
{
    /**
     * GithubParseException constructor.
     * @param $line
     * @param $column
     */
    public function __construct($message, $query, $line)
    {
        parent::__construct($message . ': ' . $this->getErroredLine($query, $line));

    }

    private function getErroredLine($query, $line)
    {
        $lines = preg_split ('/\R/', $query);
        return $lines[$line - 1] . "\n\n" . $query;
    }


    public function getName()
    {
        return 'Github parsing exception';
    }
}

