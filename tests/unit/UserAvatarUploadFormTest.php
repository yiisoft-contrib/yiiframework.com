<?php

use app\models\UserAvatarUploadForm;
use PHPUnit\Framework\TestCase;
use yii\helpers\FileHelper;
use yii\imagine\BaseImage;
use yii\imagine\Image;
use yii\web\UploadedFile;

final class UserAvatarUploadFormTest extends TestCase
{
    private string $directory;
    private array $previousAliases;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/yii-avatar-test-' . bin2hex(random_bytes(8));
        FileHelper::createDirectory($this->directory);
        $this->previousAliases = Yii::$aliases;
        Yii::setAlias('@app', dirname(__DIR__, 2));
        require_once __DIR__ . '/../_support/AvatarUploadUser.php';
        require_once __DIR__ . '/../_support/AvatarUploadedFile.php';
    }

    protected function tearDown(): void
    {
        Yii::$aliases = $this->previousAliases;
        FileHelper::removeDirectory($this->directory);
    }

    public function testUploadsPngAvatarWithGd(): void
    {
        $this->assertAvatarUpload('png', IMAGETYPE_PNG);
    }

    public function testUploadsJpegAvatarWithGd(): void
    {
        $this->assertAvatarUpload('jpg', IMAGETYPE_JPEG);
    }

    private function assertAvatarUpload(string $extension, int $imageType): void
    {
        $source = $this->directory . '/upload.' . $extension;
        $image = imagecreatetruecolor(360, 240);
        self::assertInstanceOf(GdImage::class, $image);
        self::assertTrue($imageType === IMAGETYPE_PNG ? imagepng($image, $source) : imagejpeg($image, $source));

        $path = $this->directory . '/avatars/profile.png';
        $user = new AvatarUploadUser($path);
        $form = new UserAvatarUploadForm(['user' => $user]);

        $previousImagine = (new ReflectionProperty(BaseImage::class, '_imagine'))->getValue();

        try {
            $form->avatar = new AvatarUploadedFile([
                'name' => basename($source),
                'tempName' => $source,
                'size' => filesize($source),
                'error' => UPLOAD_ERR_OK,
            ]);
            Image::setImagine(new \Imagine\Gd\Imagine());

            self::assertTrue($form->upload(), implode(' ', $form->getFirstErrors()));
            self::assertFalse($user->saveValidation);
            self::assertSame(5, $user->avatar_version);
            self::assertFileEquals($source, $path . '.orig');
            $thumbnail = getimagesize($path);
            self::assertIsArray($thumbnail);
            self::assertSame([200, 200, IMAGETYPE_PNG], array_slice($thumbnail, 0, 3));
        } finally {
            Image::setImagine($previousImagine);
        }
    }

    public function testRejectsNonImageWithPngExtension(): void
    {
        $source = $this->directory . '/upload.png';
        file_put_contents($source, 'plain text');
        $path = $this->directory . '/avatars/profile.png';
        $user = new AvatarUploadUser($path);
        $form = new UserAvatarUploadForm([
            'user' => $user,
            'avatar' => new UploadedFile([
                'name' => basename($source),
                'tempName' => $source,
                'size' => filesize($source),
                'error' => UPLOAD_ERR_OK,
            ]),
        ]);

        self::assertFalse($form->upload());
        self::assertTrue($form->hasErrors('avatar'));
        self::assertNull($user->saveValidation);
        self::assertSame(4, $user->avatar_version);
        self::assertFileDoesNotExist($path);
        self::assertFileDoesNotExist($path . '.orig');
    }
}
