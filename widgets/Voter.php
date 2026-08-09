<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 08.09.16
 * Time: 19:22
 */

namespace app\widgets;

use app\components\object\ObjectIdentityInterface;
use app\models\ActiveRecord;
use app\models\Rating;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This widget prints up/down voting buttons for a model.
 */
class Voter extends Widget
{
    /**
     * @var ActiveRecord|ObjectIdentityInterface
     */
    public $model;

    public function init()
    {
        if ($this->model === null) {
            throw new InvalidConfigException('Voter widget property model is not set.');
        }
    }

    public function run()
    {
        // TODO check user login
        // TODO send not logged in user to login and after login redirect back here

        list($total, $up) = Rating::getVotes($this->model);
        $modelType = $this->model->getObjectType();
        $modelId = $this->model->getObjectId();

        $hasVoted = -1;
        if (!Yii::$app->user->isGuest) {
            /** @var $userRating Rating */
            $userRating = Rating::find()->where(['object_type' => $modelType, 'object_id' => $modelId, 'user_id' => Yii::$app->user->id])->one();
            if ($userRating !== null) {
                $hasVoted = $userRating->rating;
            }
        }

        $html = '';
        $html .= '<div class="voting' . ($modelType === 'wiki' ? ' voting--wiki' : '') . '">';

        $html .= '  <span class="votes-up' . ($hasVoted === 1 ? ' voted' : '') . '">';
        $html .= '    <span class="votes">' . $up . '</span> ';
        $html .= Html::a(
            '<svg class="vote-arrow vote-arrow--up" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 3 3.5 9.5l1.4 1.4L9 6.8V17h2V6.8l4.1 4.1 1.4-1.4L10 3Z"/></svg>',
            '',
            [
                'aria-label' => 'Vote up',
                'title' => 'Vote up',
                'data-vote-url' => Url::to(['/ajax/vote', 'type' => $modelType, 'id' => $modelId, 'vote' => 1]),
            ]
        );
        $html .= '  </span>';

        $html .= '  <span class="votes-down' . ($hasVoted === 0 ? ' voted' : '') . '">';
        $html .= '    <span class="votes">' . ($total - $up) . '</span> ';
        $html .= Html::a(
            '<svg class="vote-arrow vote-arrow--down" viewBox="0 0 20 20" aria-hidden="true"><path d="m10 17 6.5-6.5-1.4-1.4-4.1 4.1V3H9v10.2L4.9 9.1l-1.4 1.4L10 17Z"/></svg>',
            '',
            [
                'aria-label' => 'Vote down',
                'title' => 'Vote down',
                'data-vote-url' => Url::to(['/ajax/vote', 'type' => $modelType, 'id' => $modelId, 'vote' => 0]),
            ]
        );
        $html .= '  </span>';

        $html .= '</div>';

        return $html;
    }
}
