<?php


namespace app\components\github;


use yii\base\Exception;

class GithubException extends Exception
{
    public function getName()
    {
        return 'Github exception';
    }
}
