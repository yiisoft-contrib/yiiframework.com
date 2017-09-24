<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\commands;


use app\models\Comment;
use app\models\Extension;
use app\models\ExtensionCategory;
use app\models\File;
use app\models\News;
use app\models\Rating;
use app\models\Star;
use app\models\User;
use app\models\Wiki;
use app\models\WikiCategory;
use app\models\WikiRevision;
use Faker\Factory;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * Populates the database with content from the old website
 */
class ImportController extends Controller
{
	public $defaultAction = 'import';

	/**
	 * @var array|Connection
	 */
	public $sourceDb = [
		'class' => Connection::class,
		'dsn' => 'mysql:host=localhost;dbname=yiisite',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
	];

	public $password;

	public function options($actionId)
	{
		return array_merge(parent::options($actionId), ['password']);
	}

	public function init()
	{
		$this->sourceDb = Instance::ensure($this->sourceDb, Connection::class);
		parent::init();
	}

	public function beforeAction($action)
	{
		if ($this->password) {
			$this->sourceDb->password = $this->password;
		}

		return parent::beforeAction($action);
	}



	public function actionImport()
	{
		if (!$this->confirm('Populate database with content from old website?')) {
			return ExitCode::OK;
		}

		$this->importUsers();

		$this->importBadges();

		$this->importWiki();

		$this->importExtensions();

		$this->importFiles();

		$this->importNews();

		$this->importComments();

		$this->importStars();
		$this->importRatings();

		return ExitCode::OK;
	}

	private function isDuplicateUsername($username)
    {
        return User::find()->where(['username' => $username])->exists();
    }

    private function chooseUser($users)
    {
        $result = $users[0];
        for ($i = 1, $length = count($users); $i < $length; $i++) {
            if ($this->getUserWeight($result) < $this->getUserWeight($users[$i])) {
                $result = $users[$i];
            }
        }
        return $result;
    }

    private function getUserWeight($user)
    {
        return 10 * $user['wiki_count'] + 10 * $user['extension_count'] + 2 * $user['comment_count'] + $user['post_count'];
    }

	private function importUsers()
	{
		if (User::find()->count() > 0) {
			$this->stdout("Users table is already populated, skipping.\n");
			return;
		}

		// Same email accounts should be skipped. Out of two prefer to keep one with more posts.
		$excludedDuplicateIDs = $this->sourceDb->createCommand(<<<SQL
SELECT member_id
FROM ipb_members a
WHERE member_banned = 0
GROUP BY email
HAVING COUNT(*) > 1
ORDER BY posts ASC;
SQL
)->queryColumn();

		$userQuery = (new Query)->from('ipb_members')
			->select(['member_id', 'name', 'email', 'joined', 'last_visit', 'last_activity', 'members_display_name', 'members_pass_hash', 'members_pass_salt', 'conv_password'])
			->where(['member_banned' => 0]);

		$count = $userQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing users...');
		$i = 0;
		$err = 0;
		foreach ($userQuery->each(100, $this->sourceDb) as $user) {
		    $user['name'] = trim($user['name']);

			$yiiUsers = (new Query)->from('tbl_user')->where(['id' => $user['member_id']])->all($this->sourceDb);


			if ($yiiUsers === []) {
				$this->stdout('NO YII USER for: ' . $user['member_id'] . ' - ' . $user['name']. "\n");
                continue;
			}

            if (count($yiiUsers) > 1) {
                $this->stdout('Multiple users with ID ' . $user['member_id'] . " in tbl_user. Choosing one...\n");
                $yiiUser = $this->chooseUser($yiiUsers);
            } else {
                $yiiUser = $yiiUsers[0];
            }

			if (trim($yiiUser['username']) !== $user['name']) {
				$this->stdout('NAME MISMATCH with YII USER for: ' . $user['member_id'] . ' - ' . $user['name'] . ' - ' . $yiiUser['username'] . "\n");
				continue;
			}
			if (in_array($user['member_id'], $excludedDuplicateIDs)) {
				$this->stdout('NOT IMPORTED DUPLICATE EMAIL: ' . $user['member_id'] . ' - ' . $user['name'] . ' - ' . $user['email'] . "\n");
				continue;
			}

			$passwordHash = '';
			if (!empty($user['conv_password'])) {
				$passwordHash = 'LEGACYSHA:' . $user['conv_password'];
			} elseif (!empty($user['members_pass_hash']) && !empty($user['members_pass_salt'])) {
				$passwordHash = 'LEGACYMD5:' . $user['members_pass_hash'] . ':' . $user['members_pass_salt'];
			}

			$model = new User([
				'id' => $user['member_id'],
				'username' => $user['name'],
				'email' => YII_DEBUG ? $this->debugEmail($user['email']) : $user['email'],
				'display_name' => empty($user['members_display_name']) ? $user['name'] : $user['members_display_name'],
				'created_at' => date('Y-m-d H:i:s', $user['joined']),
				'password_hash' => $passwordHash,

				'login_time' => $yiiUser['login_time'] ? date('Y-m-d H:i:s', $yiiUser['login_time']) : null,

				'rank' => $yiiUser['rank'],
				'rating' => $yiiUser['rating'],
				'extension_count' => $yiiUser['extension_count'],
				'wiki_count' => $yiiUser['wiki_count'],
				'comment_count' => $yiiUser['comment_count'],
				'post_count' => $yiiUser['post_count'],

			]);
			$model->detachBehavior('timestamp');

			try {
                $model->save(false);
            } catch (\Exception $e) {
			    // check if the error was because of duplicate username and try to import with
                // adding "-" to username
			    if ($this->isDuplicateUsername($model->username)) {
                    $model->username .= '-';

                    $this->stdout("Duplicate username. Importing with $model->username as username.\n");

			        try {
                        $model->save(false);
                    } catch (\Exception $e) {
                        $this->stdout($e->getMessage()."\n", Console::FG_RED);
                        $err++;
                    }
                } else {
                    $this->stdout($e->getMessage() . "\n", Console::FG_RED);
                    $err++;
                }
            }

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->stdout('done.', Console::FG_GREEN, Console::BOLD);
        $this->stdout(" $count records imported.");
        if ($err > 0) {
            $this->stdout(" $err errors occurred.", Console::FG_RED, Console::BOLD);
        }
        $this->stdout("\n");
	}

	private function debugEmail($email)
    {
        $parts = explode('@', $email);

        if (count($parts) < 2) {
            return 'test+' . $email .'.at@cebe.cc';
        }

        list($user, $domain) = $parts;
        return "test+$user.at.$domain@cebe.cc";
    }

	private function importBadges()
	{
		if ((new Query)->from('{{%badges}}')->count() > 0) {
			$this->stdout("Badges table is already populated, skipping.\n");
			return;
		}

		$query = (new Query)->from('tbl_badge');

		$count = $query->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing badges...');
		$i = 0;
		$err = 0;
		foreach($query->each(100, $this->sourceDb) as $badge) {

		    try {
                \Yii::$app->db->createCommand()->insert('{{%badges}}', $badge)->execute();
            } catch (\Exception $e) {
                $this->stdout($e->getMessage()."\n", Console::FG_RED);
                $err++;
            }

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);

//		$this->importBadgeQueue();
		$this->importUserBadges();
	}

	private function importBadgeQueue()
	{
		$query = (new Query)->from('tbl_badge_queue');

		$count = $query->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing badge queue...');
		$i = 0;
		$err = 0;
		foreach($query->each(100, $this->sourceDb) as $badge) {

		    try {
                \Yii::$app->db->createCommand()->insert('{{%badge_queue}}', $badge)->execute();
            } catch (\Exception $e) {
                $this->stdout($e->getMessage()."\n", Console::FG_RED);
                $err++;
            }

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	private function importUserBadges()
	{
		$query = (new Query)->from('tbl_user_badge');

		$count = $query->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing user badges...');
		$i = 0;
		$err = 0;
		foreach($query->each(100, $this->sourceDb) as $badge) {

			$badge['create_time'] = date('Y-m-d H:i:s', $badge['create_time']);
			if ($badge['complete_time'] !== null) {
				$badge['complete_time'] = date('Y-m-d H:i:s', $badge['complete_time']);
			}

			try {
                \Yii::$app->db->createCommand()->insert('{{%user_badges}}', $badge)->execute();
            } catch (\Exception $e) {
                $this->stdout($e->getMessage()."\n", Console::FG_RED);
                $err++;
            }

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	private function importWiki()
	{
		if (Wiki::find()->count() > 0 || WikiCategory::find()->count() > 0) {
			$this->stdout("Wiki table is already populated, skipping.\n");
			return;
		}

		// creating wiki categories
		$categoryQuery = (new Query)->from('tbl_lookup')->where(['type' => 'WikiCategory']);
		foreach($categoryQuery->all($this->sourceDb) as $cat) {
			$model = new WikiCategory();
			$model->id = $cat['code'];
			$model->name = $cat['name'];
			$model->sequence = $cat['sequence'];
			$model->save(false);
		}

		// import wikis
		$wikiQuery = (new Query)->from('tbl_wiki')->orderBy('id');
		$count = $wikiQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing wiki...');
		$i = 0;
		$err = 0;
		foreach($wikiQuery->each(100, $this->sourceDb) as $wiki) {

			try {
				$model = new Wiki([
					'id' => $wiki['id'],
					'title' => $wiki['title'],
					'content' => $this->convertMarkdown($wiki['content']),
					'category_id' => $wiki['category_id'],
					'tagNames' => $wiki['tags'],
					'creator_id' => $wiki['creator_id'],
					'updater_id' => $wiki['updater_id'],
					'created_at' => date('Y-m-d H:i:s', $wiki['create_time']),
					'updated_at' => date('Y-m-d H:i:s', $wiki['update_time']),

					'yii_version' => $wiki['yii_version'],

					'total_votes' => $wiki['total_votes'],
					'up_votes' => $wiki['up_votes'],
					'rating' => $wiki['rating'],

					'view_count' => $wiki['view_count'],
					'comment_count' => $wiki['comment_count'],

					'status' => $wiki['status'],
					'featured' => $wiki['featured'],
				]);
				$model->detachBehavior('timestamp');
				$model->detachBehavior('blameable');
				$model->save(false);

				// remove first revision automatically created by Wiki model
				WikiRevision::deleteAll(['wiki_id' => $wiki['id']]);
				// import revisions:
				$revisionQuery = (new Query)->from('tbl_wiki_revision')->where(['wiki_id' => $wiki['id']]);
				foreach ($revisionQuery->all($this->sourceDb) as $rev) {
					$revModel = new WikiRevision([
						'wiki_id' => $rev['wiki_id'],
						'revision' => $rev['revision'],
						'title' => $rev['title'],
						'content' => $this->convertMarkdown($rev['content']),
						'tagNames' => $rev['tags'],
						'category_id' => $rev['category_id'],
						'memo' => !empty($rev['memo']) ? $rev['memo'] : '',
						'updater_id' => $rev['updater_id'],
						'updated_at' => date('Y-m-d H:i:s', $rev['update_time']),
					]);
					$revModel->detachBehavior('timestamp');
					$revModel->detachBehavior('blameable');
					$revModel->save(false);
				}
			}catch (\Exception $e) {
				$this->stdout($e->getMessage()."\n", Console::FG_RED);
				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	private function importExtensions()
	{
		if (Extension::find()->count() > 0 || ExtensionCategory::find()->count() > 0) {
			$this->stdout("Extension table is already populated, skipping.\n");
			return;
		}

		// creating extension categories
		$categoryQuery = (new Query)->from('tbl_lookup')->where(['type' => 'ExtensionCategory']);
		foreach($categoryQuery->all($this->sourceDb) as $cat) {
			$model = new ExtensionCategory();
			$model->id = $cat['code'];
			$model->name = $cat['name'];
			$model->sequence = $cat['sequence'];
			$model->save(false);
		}

		// import extensions
		$extensionQuery = (new Query)->from('tbl_extension')->orderBy('id');
		$count = $extensionQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing extension...');
		$i = 0;
		$err = 0;
		foreach($extensionQuery->each(100, $this->sourceDb) as $extension) {

			try {
				$model = new Extension([
					'id' => $extension['id'],

					'from_packagist' => 0,

					'name' => $extension['name'],
					'tagline' => $extension['tagline'],
					'description' => $this->convertMarkdown($extension['description']),
					'category_id' => $extension['category_id'],
					'tagNames' => $extension['tags'],
					'owner_id' => $extension['owner_id'],
					'created_at' => date('Y-m-d H:i:s', $extension['create_time']),
					'updated_at' => date('Y-m-d H:i:s', $extension['update_time']),

					'license_id' => $this->convertLicense($extension['license_id']),

					'yii_version' => $extension['yii_version'],

					'total_votes' => $extension['total_votes'],
					'up_votes' => $extension['up_votes'],
					'rating' => $extension['rating'],

					'download_count' => $extension['download_count'],
					'comment_count' => $extension['comment_count'],

					'status' => $extension['status'],
					'featured' => $extension['featured'],

					// TODO mark these as default, not pacakgist
				]);
				$model->detachBehavior('timestamp');
				$model->detachBehavior('blameable');
				$model->save(false);

			} catch (\Exception $e) {
				$this->stdout($e->getMessage()."\n", Console::FG_RED);
				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	private function importFiles()
	{
		if (File::find()->count() > 0) {
			$this->stdout("File table is already populated, skipping.\n");
			return;
		}

		// import files
		$fileQuery = (new Query)->from('tbl_file')->orderBy('id');
		$count = $fileQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing files...');
		$i = 0;
		$err = 0;
		foreach($fileQuery->each(100, $this->sourceDb) as $file) {

			try {
				$model = new File([
					'id' => $file['id'],

					'object_type' => $file['object_type'],
					'object_id' => $file['object_id'],

					'file_name' => $file['file_name'],
					'file_size' => $file['file_size'],
					'mime_type' => $file['mime_type'] ?: $this->fixMimeType($file['file_name']),

					'download_count' => $file['download_count'],

					'summary' => $file['summary'],

                    // creator info is not available in old data
					'created_by' => null,

					'created_at' => date('Y-m-d H:i:s', $file['upload_time']),
					'updated_at' => null,
				]);
				$model->detachBehavior('timestamp');
				$model->detachBehavior('blameable');
				$model->save(false);

			}catch (\Exception $e) {
				$this->stdout($e->getMessage()."\n", Console::FG_RED);
				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
		$this->stdout("\nTODO copy files from old site '/common/upload/' to new site '/data/files/'\n", Console::BOLD);
	}

	private function fixMimeType($fileName)
    {
        $type = FileHelper::getMimeTypeByExtension($fileName);
        if ($type === null && substr($fileName, -4, 4) === '.tgz') {
            return 'application/x-gzip';
        }
        return $type;
    }

	/**
	 * Convert licence ID to SPDX identifier
	 * @param integer $id
	 * @return string
	 */
	private function convertLicense($id)
	{
		$licenses = [
			1 => 'Apache-2.0', // Apache License 2.0|http://www.opensource.org/licenses/apache2.0.php
			2 => 'EPL-1.0', // Eclipse Public License 1.0|http://www.opensource.org/licenses/eclipse-1.0.php
			3 => 'GPL-2.0', // GNU General Public License v2|http://www.opensource.org/licenses/gpl-2.0.php
			4 => 'GPL-3.0', // GNU General Public License v3|http://www.opensource.org/licenses/gpl-3.0.html
			5 => 'LGPL-3.0', // GNU Lesser General Public License|http://www.opensource.org/licenses/lgpl-3.0.html
			6 => 'MIT', // MIT License|http://www.opensource.org/licenses/mit-license.php
			7 => 'MPL-1.1', // Mozilla Public License 1.1|http://www.opensource.org/licenses/mozilla1.1.php
			8 => 'BSD-2-Clause', // New BSD License|http://www.opensource.org/licenses/bsd-license.php
			9 => 'PHP-3.0', // PHP License 3.0|http://www.opensource.org/licenses/php.php
			10 => 'other', // Other Open Source License
		];
		return $licenses[$id];
	}

	private function importComments()
	{
		if (Comment::find()->count() > 0) {
			$this->stdout("Comment table is already populated, skipping.\n");
			return;
		}

		$query = (new Query)->from('tbl_comment');

		$count = $query->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing comments...');
		$i = 0;
		$err = 0;
		foreach($query->each(100, $this->sourceDb) as $comment) {

			try {
				\Yii::$app->db->createCommand()->insert('{{%comment}}', [
					'id' => $comment['id'],
					'user_id' => $comment['creator_id'],
					'object_type' => $this->convertCommentType($comment['object_type']),
					'object_id' => $comment['object_id'],
					'text' => (empty($comment['title']) ? '' : '#### ' . $comment['title'] . "\n\n")
						. $this->convertMarkdown($comment['content']),
					'created_at' => date('Y-m-d H:i:s', $comment['create_time']),
					'updated_at' => date('Y-m-d H:i:s', $comment['update_time']),
					'total_votes' => $comment['total_votes'],
					'up_votes' => $comment['up_votes'],
					'rating' => $comment['rating'],
					'status' => $this->convertCommentStatus($comment['status'])
				])->execute();
			} catch (\Exception $e) {
                $this->stdout($e->getMessage()."\n", Console::FG_RED);
                				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	private function convertCommentType($type)
	{
		return $type;
	}

	private function convertCommentStatus($status)
	{
		return $status == 3 ? Comment::STATUS_ACTIVE : Comment::STATUS_DELETED;
	}

	private function importNews()
	{
		if (News::find()->count() > 0) {
			$this->stdout("News table is already populated, skipping.\n");
			return;
		}

		$newsQuery = (new Query)->from('tbl_news');

		$statusMap = [
			/*const STATUS_DRAFT=*/1 => News::STATUS_DRAFT,
			/*const STATUS_PENDING=*/2 => News::STATUS_DRAFT,
			/*const STATUS_PUBLISHED=*/3 => News::STATUS_PUBLISHED,
			/*const STATUS_ARCHIVED=*/4 => News::STATUS_PUBLISHED,
			/*const STATUS_DELETED=*/5 => News::STATUS_DELETED,
		];

		$count = $newsQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing news...');
		$i = 0;
        $err = 0;
		foreach($newsQuery->each(100, $this->sourceDb) as $news) {

			$content = $news['content'];
			$content = $this->convertMarkdown($content);

			$model = new News([
				'id' => $news['id'],
				'title' => $news['title'],
				'news_date' => date('Y-m-d', $news['news_date']),
				// TODO image_id
				'content' => $content,
				'status' => $statusMap[$news['status']],
				'creator_id' => $news['creator_id'],
				'created_at' => date('Y-m-d H:i:s', $news['create_time']),
				'updater_id' => $news['updater_id'],
				'updated_at' => date('Y-m-d H:i:s', $news['update_time']),
				'tagNames' => $news['tags'],
			]);
			$model->detachBehavior('timestamp');
			$model->detachBehavior('blameable');

			try {
                $model->save(false);
            } catch (\Exception $e) {
                $this->stdout($e->getMessage()."\n", Console::FG_RED);
                $err++;
            }

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	public function importRatings()
	{
		if (Rating::find()->count() > 0) {
			$this->stdout("Rating table is already populated, skipping.\n");
			return;
		}

		$ratingQuery = (new Query)->from('tbl_rating');

		$count = $ratingQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing ratings...');
		$i = 0;
		$err = 0;
		foreach($ratingQuery->each(100, $this->sourceDb) as $rating) {

			try {
				$rating['created_at'] = date('Y-m-d H:i:s', $rating['create_time']);
				unset($rating['create_time']);
				Rating::getDb()->createCommand()->insert(Rating::tableName(), $rating)->execute();
			} catch (\Exception $e) {
				$this->stdout($e->getMessage()."\n", Console::FG_RED);
				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	public function importStars()
	{
		if (Star::find()->count() > 0) {
			$this->stdout("Star table is already populated, skipping.\n");
			return;
		}

		$starQuery = (new Query)->from('tbl_star');

		$count = $starQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing stars...');
		$i = 0;
		$err = 0;
		foreach($starQuery->each(100, $this->sourceDb) as $star) {

			try {
				$star['created_at'] = date('Y-m-d H:i:s', $star['create_time']);
				unset($star['create_time']);
				Star::getDb()->createCommand()->insert(Star::tableName(), $star)->execute();
			} catch (\Exception $e) {
				$this->stdout($e->getMessage()."\n", Console::FG_RED);
				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
        $this->printImportSummary($count, $err);
	}

	protected function convertMarkdown($markdown)
	{
		// TODO code blocks conversion does not work inside quotes:
		// http://www.yiiframework.com/wiki/16

		// convert code blocks
		$markdown = preg_replace_callback('/~~~\s*\[php\]\s*(.+?)\n~~~/is', function($matches) {
			return "\n```php\n".$matches[1]."\n```";
		}, $markdown);

		return $markdown;
	}

	protected function getFaker()
	{
		return Factory::create('en');
	}

    /**
     * @param int $count
     * @param int $err
     */
    private function printImportSummary($count, $err)
    {
        $this->stdout("done.", Console::FG_GREEN, Console::BOLD);
        $this->stdout(" $count records imported.");
        if ($err > 0) {
            $this->stdout(" $err errors occurred.", Console::FG_RED, Console::BOLD);
        }
        $this->stdout("\n");
    }


}