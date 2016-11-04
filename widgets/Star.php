<?php

namespace app\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Class Star
 * @package app\widgets
 */
class Star extends Widget
{
    /**
     * @var ActiveRecord
     */
    public $model;

    public function init()
    {
        if ($this->model === null) {
            throw new InvalidConfigException('Star widget property model is not set.');
        }
    }

    public function run()
    {
        $modelClass = $this->model->formName();
        $modelId = $this->model->primaryKey;

        $starValue = 0;
        if (!Yii::$app->user->isGuest) {
            $starValue = \app\models\Star::getStarValue($this->model, Yii::$app->user->id);
        }

        return $this->render('star', [
            'ajaxUrl' => Url::to(['/ajax/star', 'type' => $modelClass, 'id' => $modelId]),
            'starValue' => $starValue
        ]);
    }
}
