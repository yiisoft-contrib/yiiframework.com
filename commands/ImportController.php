<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\commands;


use app\models\News;
use Faker\Factory;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Console;

/**
 * Populates the database with content from the old website
 */
class ImportController extends Controller
{
	public $defaultAction = 'import';

	public $sourceDb = [
		'class' => Connection::class,
		'dsn' => 'mysql:host=localhost;dbname=yiisite',
		'username' => 'root',
		'password' => 'wurstepelle',
	];

	public function init()
	{
		$this->sourceDb = Instance::ensure($this->sourceDb, Connection::class);
		parent::init();
	}


	public function actionImport()
	{
		if (!$this->confirm('Populate database with content from old website?')) {
			return 1;
		}

		// TODO import users!

		$this->importNews();

		return 0;
	}

	private function importNews()
	{
		$news = (new Query)->from('tbl_news');

		$statusMap = [
			/*const STATUS_DRAFT=*/1 => News::STATUS_DRAFT,
			/*const STATUS_PENDING=*/2 => News::STATUS_DRAFT,
			/*const STATUS_PUBLISHED=*/3 => News::STATUS_PUBLISHED,
			/*const STATUS_ARCHIVED=*/4 => News::STATUS_PUBLISHED,
			/*const STATUS_DELETED=*/5 => News::STATUS_DELETED,
		];

		$count = $news->count('*', $this->sourceDb);
		Console::startProgress(0, $count, 'Importing news...');
		$i = 0;
		foreach($news->each(100, $this->sourceDb) as $news) {

			$content = $news['content'];
			$content = $this->convertMarkdown($content);

			$news = new News([
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
			$news->save(false);

			Console::updateProgress(++$i, $count);
		}
		Console::endProgress(true);
		$this->stdout("done.", Console::FG_GREEN, Console::BOLD);
		$this->stdout(" $count records imported.\n");
	}

	protected function convertMarkdown($markdown)
	{
		// convert code blocks
		echo "converting MD...\n";
		$markdown = preg_replace_callback('/~~~\s*\[php\]\s*(.+?)\n~~~/is', function($matches) {
			print_r($matches);
			return "```php\n".$matches[1]."\n```";
		}, $markdown);

		return $markdown;
	}

	protected function getFaker()
	{
		return Factory::create('en');
	}


}