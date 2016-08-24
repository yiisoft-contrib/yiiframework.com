<?php

namespace app\components;

use yii\helpers\Inflector;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class SluggableBehavior extends \yii\behaviors\SluggableBehavior
{
    /**
     * Overrides the default slug generator to keep dot characters, i.e. version numbers in the slug.
     *
     * @param array $slugParts an array of strings that should be concatenated and converted to generate the slug value.
     * @return string the conversion result.
     */
    protected function generateSlug($slugParts)
    {
        $replacement = '-';
        $string = implode('-', $slugParts);
        $string = Inflector::transliterate($string);
        $string = preg_replace('/[^a-zA-Z0-9=\s—–.-]+/u', '', $string);
        $string = preg_replace('/[=\s—–.-]+/u', $replacement, $string);
        $string = trim($string, $replacement);
        return strtolower($string);
    }
}