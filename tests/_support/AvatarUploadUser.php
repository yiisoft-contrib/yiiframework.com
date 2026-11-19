<?php

use app\models\User;

final class AvatarUploadUser extends User
{
    public int $avatar_version = 4;
    public ?bool $saveValidation = null;

    public function __construct(private string $avatarPath)
    {
        parent::__construct();
    }

    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        $this->saveValidation = $runValidation;

        return true;
    }
}
