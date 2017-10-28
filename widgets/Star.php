<?php

namespace app\widgets;

use app\components\objectKey\ObjectKeyInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use app\models\ActiveRecord;
use yii\helpers\Url;

/**
 * Star widget for following items
 */
class Star extends Widget
{
    /**
     * @var ActiveRecord|ObjectKeyInterface
     */
    public $model;

    public $starValue;

    public function init()
    {
        if ($this->model === null) {
            throw new InvalidConfigException('Star widget property model is not set.');
        }
    }

    public function run()
    {
        $modelType = $this->model->getObjectType();
        $modelId = $this->model->getObjectId();

        if ($this->starValue === null) {
            // display start widget for an item
            $starValue = 0;
            if (!Yii::$app->user->isGuest) {
                $starValue = \app\models\Star::getStarValue($this->model, Yii::$app->user->id);
            }
            $starCount = \app\models\Star::getFollowerCount($this->model);
        } else {
            // display start widget on user profile page
            $starValue = $this->starValue;
            $starCount = null;
        }

        return $this->render('star', [
            'ajaxUrl' => Url::to(['/ajax/star', 'type' => $modelType, 'id' => $modelId]),
            'starValue' => $starValue,
            'starCount' => $starCount,
        ]);
    }
}
