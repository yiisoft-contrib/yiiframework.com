<?php


namespace app\models;


interface Tweetable
{
    public function getTweetedObjectID();
    public function getTweetedObjectType();
    public function getTweetedText();
}
