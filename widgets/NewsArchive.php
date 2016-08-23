<?php

namespace app\widgets;


use app\models\News;
use Yii;
use yii\base\Widget;

class NewsArchive extends Widget
{
    public $dateFormat = 'MMMM yyyy';

    public function run()
    {
        $query = News::find()
             ->select([
                 'y' => 'YEAR(news_date)',
//                 'm' => 'MONTH(news_date)',
             ])
             ->groupBy([
                 'YEAR(news_date)',
//                 'MONTH(news_date)',
             ])
             ->orderBy(['news_date' => SORT_ASC]);

        if (!Yii::$app->user->can('news:pAdmin')) {
            $query->andWhere(['status' => News::STATUS_PUBLISHED]);
        }

        $months = $query->asArray()->all();

         $archiveEntries = [];
         foreach($months as $m) {
             $date = $m['y'];// . '-' . (strlen($m['m']) == 1 ? '0' : '') . $m['m'];
             //$archiveEntries[$date] = \Yii::$app->formatter->asDate("$date-01", $this->dateFormat);
             $archiveEntries[$date] = $m['y'];
         }
         krsort($archiveEntries);

         return $this->render('newsArchive', ['archiveEntries' => $archiveEntries]);
    }

}