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
        $html .= '<div class="voting">';

        $html .= '  <span class="votes-up' . ($hasVoted === 1 ? ' voted' : '') . '">';
        $html .= '    <span class="votes">' . $up . '</span> ';
        $html .= Html::a('<i title="Vote Up" class="thumbs-up"></i>', '', [
            'data-vote-url' => Url::to(['/ajax/vote', 'type' => $modelType, 'id' => $modelId, 'vote' => 1])
        ]);
        $html .= '    </span>';
        $html .= '  </span>';

        $html .= '  <span class="votes-down' . ($hasVoted === 0 ? ' voted' : '') . '">';
        $html .= '    <span class="votes">' . ($total - $up) . '</span> ';
        $html .= Html::a('<i title="Vote Down" class="thumbs-down"></i>', '', [
            'data-vote-url' => Url::to(['/ajax/vote', 'type' => $modelType, 'id' => $modelId, 'vote' => 0])
        ]);
        $html .= '    </span>';
        $html .= '  </span>';

        $html .= '</div>';

        return $html;
    }
}
