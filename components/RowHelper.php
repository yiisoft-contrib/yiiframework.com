<?php
namespace app\components;

/**
 * RowHelper
 */
class RowHelper
{
    /**
     * Splits array into array of row arrays
     *
     * @param array $array array to split
     * @param int $elementsPerRow elements per row
     * @return array
     */
    public static function split(array $array, $elementsPerRow)
    {
        $rows = [];
        $currentRow = [];
        foreach ($array as $element) {
            if (count($currentRow) < $elementsPerRow) {
                $currentRow[] = $element;
            } else {
                $rows[] = $currentRow;
                $currentRow = [$element];
            }
        }

        if ($currentRow !== []) {
            $rows[] = $currentRow;
        }
        return $rows;
    }
}
