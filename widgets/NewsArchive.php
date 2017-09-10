<?php

namespace app\widgets;


use app\components\UserPermissions;
use app\models\News;
use yii\base\Widget;
use yii\helpers\Html;

class NewsArchive extends Widget
{
    public $urlParams = [];

    public function run()
    {
        $query = News::find()
            ->select([
                'y' => 'YEAR(news_date)',
                'c' => 'COUNT(*)',
            ])
            ->groupBy(['YEAR(news_date)'])
            ->orderBy(['news_date' => SORT_ASC]);

        if (!UserPermissions::canManageNews()) {
            $query->published();
        }

        $years = $query->asArray()->all();

        $archiveEntries = [];
        foreach($years as $year) {
            $date = $year['y'];
            $count = $year['c'];
            $archiveEntries[$date] = Html::a($date, array_merge($this->urlParams, ['news/index', 'year' => $date])) . " ($count)";
        }
        krsort($archiveEntries);

        return $this->render('newsArchive', ['archiveEntries' => $archiveEntries]);
    }

}