<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\commands;


use app\models\Comment;
use app\models\News;
use app\models\User;
use Faker\Factory;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Console;

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
			return 1;
		}

		$this->importUsers();

		$this->importBadges();

		// TODO wiki
		// TODO extensions

		$this->importComments();

		$this->importNews();

        // TODO
        //$this->importStars();

		return 0;
	}

	private function importUsers()
	{
		if (User::find()->count() > 0) {
			$this->stdout("Users table is already populated, skipping.\n");
			return;
		}

		//$userQuery = (new Query)->from('tbl_user');
		$userQuery = (new Query)->from('ipb_members')
			->select(['member_id', 'name', 'email', 'joined', 'last_visit', 'last_activity', 'members_display_name', 'members_pass_hash', 'members_pass_salt', 'conv_password'])
			->where(['member_banned' => 0]);

		$count = $userQuery->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing users...');
		$i = 0;
		foreach($userQuery->each(100, $this->sourceDb) as $user) {

			$yiiUser = (new Query)->from('tbl_user')->where(['id' => $user['member_id']])->one($this->sourceDb);
			if ($yiiUser === false) {
				$this->stdout('NO YII USER for: ' . $user['member_id'] . ' - ' . $user['name']. "\n");
			}
			if ($yiiUser['username'] !== $user['name']) {
				$this->stdout('NAME MISMATCH with YII USER for: ' . $user['member_id'] . ' - ' . $user['name'] . ' - ' . $yiiUser['username'] . "\n");
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
				'email' => $user['email'],
				'display_name' => empty($user['members_display_name']) ? $user['name'] : $user['members_display_name'],
				'created_at' => date('Y-m-d H:i:s', $user['joined']),
				'password_hash' => $passwordHash,

				'login_time' => date('Y-m-d H:i:s', $yiiUser['login_time']),

				'rank' => $yiiUser['rank'],
				'rating' => $yiiUser['rating'],
				'extension_count' => $yiiUser['extension_count'],
				'wiki_count' => $yiiUser['wiki_count'],
				'comment_count' => $yiiUser['comment_count'],
				'post_count' => $yiiUser['post_count'],

			]);
			$model->detachBehavior('timestamp');
			$model->save(false);

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.\n");
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
		foreach($query->each(100, $this->sourceDb) as $badge) {

			\Yii::$app->db->createCommand()->insert('{{%badges}}', $badge)->execute();

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.\n");

//		$this->importBadgeQueue();
		$this->importUserBadges();
	}

	private function importBadgeQueue()
	{
		$query = (new Query)->from('tbl_badge_queue');

		$count = $query->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing badge queue...');
		$i = 0;
		foreach($query->each(100, $this->sourceDb) as $badge) {

			\Yii::$app->db->createCommand()->insert('{{%badge_queue}}', $badge)->execute();

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.\n");
	}

	private function importUserBadges()
	{
		$query = (new Query)->from('tbl_user_badge');

		$count = $query->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing user badges...');
		$i = 0;
		foreach($query->each(100, $this->sourceDb) as $badge) {

			$badge['create_time'] = date('Y-m-d H:i:s', $badge['create_time']);
			if ($badge['complete_time'] !== null) {
				$badge['complete_time'] = date('Y-m-d H:i:s', $badge['complete_time']);
			}
			\Yii::$app->db->createCommand()->insert('{{%user_badges}}', $badge)->execute();

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.\n");
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
					'object_type' => $comment['object_type'],
					'object_id' => $comment['object_id'],
					'text' => (empty($comment['title']) ? '' : '#### ' . $comment['title'] . "\n\n")
						. $this->convertMarkdown($comment['content']),
					'created_at' => date('Y-m-d H:i:s', $comment['create_time']),
					'updated_at' => date('Y-m-d H:i:s', $comment['update_time']),

					// TODO votes, rating and status
				])->execute();
			}catch (\Exception $e) {
				$err++;
			}
			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.");
		if ($err > 0) {
			$this->stdout(" $err errors occurred.", Console::FG_RED, Console::BOLD);
		}
		$this->stdout("\n");
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
			$model->save(false);

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.\n");
	}

	protected function convertMarkdown($markdown)
	{
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


}