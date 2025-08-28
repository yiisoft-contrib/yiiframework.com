<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;

class InfoTop extends Widget
{
    public function run()
    {
        return $this->render('info-top', [
            'visible' => $this->isVisible(),
        ]);
    }

    private function isVisible(): bool
    {
        return Yii::$app->request->get('hostingtest') === '1';
    }
}
