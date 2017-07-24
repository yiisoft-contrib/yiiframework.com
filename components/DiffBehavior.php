<?php

namespace app\components;

use DiffMatchPatch\Diff;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use DiffMatchPatch\DiffMatchPatch;
use yii\base\Behavior;
use yii\db\AfterSaveEvent;
use yii\helpers\ArrayHelper;

/**
 * Class DiffBehavior
 *
 * @property ActiveRecord $owner
 */
class DiffBehavior extends Behavior
{
    private $_changedAttributes;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    /**
     * @param AfterSaveEvent $event
     */
    public function afterUpdate($event)
    {
        $this->_changedAttributes = $event->changedAttributes;
    }

    public function diffAttribute($attribute)
    {
        $oldAttribute = $newAttribute = $this->owner->getAttribute($attribute);
        if (isset($this->_changedAttributes[$attribute])) {
            $oldAttribute = $this->_changedAttributes[$attribute];
        }
        return static::diffStrings(
            (string) $oldAttribute,
            (string) $newAttribute
        );
    }

    public function getReallyOldAttribute($attribute)
    {
        if (isset($this->_changedAttributes[$attribute])) {
            return $this->_changedAttributes[$attribute];
        } else {
            return $this->owner->getAttribute($attribute);
        }
    }

    public function diff($record, $attribute)
    {
        return static::diffStrings(
            (string) ArrayHelper::getValue($this->owner, $attribute),
            (string) ArrayHelper::getValue($record, $attribute)
        );
    }

    public static function diffStrings($a, $b)
    {
        $diff = new DiffMatchPatch();
        $diffs = $diff->diff_main(
            (string) $a,
            (string) $b,
            false
        );
        $diff->diff_cleanupSemantic($diffs);
        return $diffs;
    }

    /**
     * Convert a diff array into a pretty text report.
     *
     * @return string text representation.
     */
    public static function diffPrettyText($diffs)
    {
        $diff = new DiffMatchPatch();
        $patch = $diff->patch_make($diffs);
        return $diff->patch_toText($patch);
    }

    /**
     * Convert a diff array into a pretty HTML report.
     *
     * @return string HTML representation.
     */
    public static function diffPrettyHtml($diffs)
    {
        $html = '';
        $diffs = array_values($diffs);
        $c = count($diffs);
        for($i = 0; $i < $c; ++$i) {
            $change = $diffs[$i];
            $op = $change[0];
            $data = $change[1];
            $text = str_replace(array(
                '&', '<', '>',
            ), array(
                '&amp;', '&lt;', '&gt;'
            ), $data);

            if ($op == Diff::INSERT) {
                // make sure whitespace changes are visible
                $text = str_replace([' ', "\n"], ['&nbsp;', "&nbsp;\n"], $text);
                $html .= '<ins>' . nl2br($text) . '</ins>';
            } elseif ($op == Diff::DELETE) {
                // make sure whitespace changes are visible
                $text = str_replace([' ', "\n"], ['&nbsp;', "&nbsp;\n"], $text);
                $html .= '<del>' . nl2br($text) . '</del>';
            } else {
                $pos = ($i == 0 ? 'first' :
                    (($i == $c - 1) ? 'last' : 'middle'));
                $html .= static::trimContext($text, $pos);
            }
        }

        return $html;
    }

    /**
     * make long unchanged text smaller.
     * @param string $text
     */
    private static function trimContext($text, $pos)
    {
        $threshold = 6;

        $lines = explode("\n", $text);
        $count = count($lines);

        if ($count <= $threshold) {
            return $text;
        }

        switch($pos)
        {
            case "first":
                return '<div class="diff-snip">[...]</div>'
                     . '<span>' . nl2br(ltrim(implode("\n", array_slice($lines, $count - $threshold)))) . '</span>';
            case "last":
                return '<span>' . nl2br(rtrim(implode("\n", array_slice($lines, 0, $threshold)))) . '</span>'
                     . '<div class="diff-snip">[...]</div>';
            default:
                return '<span>' . nl2br(rtrim(implode("\n", array_slice($lines, 0, $threshold / 2)))) . '</span>'
                     . '<div class="diff-snip">[...]</div>'
                     . '<span>' . nl2br(ltrim(implode("\n", array_slice($lines, $count - $threshold / 2)))) . '</span>';
        }
    }
}
