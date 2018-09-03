<?php

namespace app\commands;

use app\components\forum\ForumAdapterInterface;
use app\models\Comment;
use app\models\Extension;
use app\models\User;
use app\models\Wiki;
use app\models\WikiRevision;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;

class UserController  extends Controller
{
    /**
     * @var bool whether to show a progress bar.
     */
    public $progress = false;


    public function options($actionID)
    {
        if ($actionID === 'ranking') {
            return array_merge(parent::options($actionID), ['progress']);
        }
        return parent::options($actionID);
    }

    /**
     * Calculate member ranking.
     *
     * This action should be configured as a daily cronjob.
     *
     * For an interactive output with progress bar, call it like:
     *
     * ```
     * yii user/ranking --progress
     * ```
     *
     * Member rating formula:
     *
     * FP: forum posts: 0 ~ 5485 posts
     * MA: member activity: 0 ~ 2*12 months (recent active time - register time)
     * EC: extension count: 0 ~ 10
     * ER: extension rating: 0 ~ 1
     * WC: wiki count (original): 0 ~ 10
     * WR: wiki rating: 0 ~ 1
     * WE: wiki edit count: 0 ~ 1
     * CC: comment count: 0 ~ 100
     *
     * Member Rating =	FP / 10
     * + MA
     * + EC*(ER+0.1)*10
     * + WC*(WR+0.1)*20
     * + WE
     * + CC
     */
    public function actionRanking()
    {
        $db = Yii::$app->db;

        $extensionScores = Extension::find()
            ->active()
            ->excludeOfficial()
            ->select(['score' => 'SUM(rating+0.1)*10'])
            ->groupBy('owner_id')
            ->indexBy('owner_id')
            ->column($db);
        $wikiScores = Wiki::find()
            ->active()
            ->select(['score' => 'SUM(rating+0.1)*20'])
            ->groupBy('creator_id')
            ->indexBy('creator_id')
            ->column($db);
        $commentScores = Comment::find()
            ->active()
            ->select(['score' => 'COUNT(*)'])
            ->groupBy('user_id')
            ->indexBy('user_id')
            ->column($db);
        $revisionScore = WikiRevision::find()
            ->select(['score' => 'COUNT(*)*0.5'])
            ->groupBy('updater_id')
            ->indexBy('updater_id')
            ->column($db);

        $extensionCounts = Extension::find()
            ->active()
            ->excludeOfficial()
            ->select(['score' => 'COUNT(*)'])
            ->groupBy('owner_id')
            ->indexBy('owner_id')
            ->column($db);
        $wikiCounts = Wiki::find()
            ->active()
            ->select(['score' => 'COUNT(*)'])
            ->groupBy('creator_id')
            ->indexBy('creator_id')
            ->column($db);

        /** @var ForumAdapterInterface $forumAdapter */
        $forumAdapter = Yii::$app->forumAdapter;
        $postCounts = $forumAdapter->getPostCounts();

        $users = User::find()->asArray();
        //$users = $db->createCommand('SELECT t.id, t.rating, t.post_count, t.wiki_count, t.extension_count, t.comment_count, f.last_visit, f.joined, f.posts FROM {{%user}} t, ipb_members f WHERE t.id=f.member_id')->queryAll();
        $updateCommand = $db->createCommand("UPDATE {{%user}} SET rating=:rating, post_count=:posts, extension_count=:extensions, comment_count=:comments, wiki_count=:wiki WHERE id=:id");
        if ($this->progress) {
            Console::startProgress($i = 0, $c = $users->count(), 'Calculating user ratings...');
        }
        foreach($users->each(500, $db) as $user) {
            $id = $user['id'];

            if ($postCounts !== null) {
                $posts = isset($postCounts[$id]) ? $postCounts[$id] : 0;
            } else {
                $posts = $forumAdapter->getPostCount($user);
            }
            $rating = $posts / 10;

            $lastLogin = strtotime($user['login_time']);
            $joined = strtotime($user['created_at']);
            if ($lastLogin > $joined && $joined) {
                $rating += ($lastLogin - $joined) / (24 * 3600 * 30);
            }
            if (isset($extensionScores[$id])) {
                $rating += $extensionScores[$id];
            }
            if (isset($wikiScores[$id])) {
                $rating += $wikiScores[$id];
            }
            if (isset($commentScores[$id])) {
                $rating += $commentScores[$id];
            }
            if (isset($revisionScore[$id])) {
                $rating += $revisionScore[$id];
            }
            $rating = (int) $rating;
            $comments = isset($commentScores[$id]) ? $commentScores[$id] : 0;
            $wiki = isset($wikiCounts[$id]) ? $wikiCounts[$id] : 0;
            $extensions = isset($extensionCounts[$id]) ? $extensionCounts[$id] : 0;

            // update only if something has changed
            if ($user['rating'] != $rating ||
                $user['post_count'] != $posts ||
                $user['wiki_count'] != $wiki ||
                $user['extension_count'] != $extensions ||
                $user['comment_count'] != $comments)
            {
                $updateCommand
                    ->bindValue(':rating',$rating)
                    ->bindValue(':posts',$posts)
                    ->bindValue(':wiki',$wiki)
                    ->bindValue(':extensions',$extensions)
                    ->bindValue(':comments',$comments)
                    ->bindValue(':id',$id)
                    ->execute();
            }
            if ($this->progress) {
                Console::updateProgress(++$i, $c);
            }
        }
        if ($this->progress) {
            Console::endProgress();
        }

        $users = User::find()
            ->active()
            ->select(['id', 'rank'])
            ->orderBy(['rating' => SORT_DESC])
            ->asArray()->all($db);
        $updateCommand = $db->createCommand('UPDATE {{%user}} SET rank=:rank WHERE id=:id');
        if ($this->progress) {
            Console::startProgress($i = 0, $c = count($users), 'Updating user ranks...');
        }
        foreach($users as $i => $user) {
            $id = $user['id'];
            $rank = $i + 1;
            if ($rank != $user['rank']) {
                $updateCommand
                    ->bindValue(':rank', $rank)
                    ->bindValue(':id', $id)
                    ->execute();
            }
            if ($this->progress) {
                Console::updateProgress(++$i, $c);
            }
        }
        if ($this->progress) {
            Console::endProgress();
        }
    }
}
