<?php

use yii\web\UploadedFile;

final class AvatarUploadedFile extends UploadedFile
{
    public function saveAs($file, $deleteTempFile = true): bool
    {
        // CLI fixtures cannot use PHP's move_uploaded_file().
        return parent::saveAs($file, false);
    }
}
