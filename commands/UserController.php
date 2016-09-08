<?php

namespace app\commands;

use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;

class UserController  extends Controller
{
    /**
     * Calculate member ranking
     *
     * member rating formula:
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
        // TODO review code and make sure it works
        // TODO depends on extensoins and wiki to be ready

        $db = Yii::$app->db;
//        $lastMemberTime = strtotime($db->createCommand('SELECT MAX(created_at) FROM {{%user}}')->queryScalar())-3600*50;

        // insert new users into tbl_user
//        $members = $db->createCommand("SELECT member_id, name, members_display_name, posts, joined, last_visit FROM ipb_members WHERE joined>$lastMemberTime")->queryAll();
//        foreach($members as $member)
//        {
//            if(User::model()->exists('id='.$member['member_id']))
//                continue;
//            $model=new User;
//            $model->id=$member['member_id'];
//            $model->username=$member['name'];
//            $model->display_name=$member['members_display_name'];
//            $model->register_time=$member['joined'];
//            $model->login_time=$member['last_visit'];
//            $model->save(false);
//        }

        $extensionScores = ArrayHelper::map($db->createCommand('SELECT owner_id, SUM(rating+0.1)*10 AS score FROM {{%extension}} WHERE status=3 GROUP BY owner_id')->queryAll(),'owner_id','score');
        $wikiScores=ArrayHelper::map($db->createCommand('SELECT creator_id, SUM(rating+0.1)*20 AS score FROM {{%wiki}} WHERE status=3 GROUP BY creator_id')->queryAll(),'creator_id','score');
        $commentScores=ArrayHelper::map($db->createCommand('SELECT creator_id, COUNT(*) AS score FROM {{%comment}} WHERE status=3 GROUP BY creator_id')->queryAll(),'creator_id','score');
        $revisionScore=ArrayHelper::map($db->createCommand('SELECT updater_id, COUNT(*)*0.5 AS score FROM {{%wiki_revision}} GROUP BY updater_id')->queryAll(),'updater_id','score');

        $extensionCounts=ArrayHelper::map($db->createCommand('SELECT owner_id, COUNT(*) AS score FROM {{%extension}} WHERE status=3 GROUP BY owner_id')->queryAll(),'owner_id','score');
        $wikiCounts=ArrayHelper::map($db->createCommand('SELECT creator_id, COUNT(*) AS score FROM {{%wiki}} WHERE status=3 GROUP BY creator_id')->queryAll(),'creator_id','score');
        $postCounts=ArrayHelper::map($db->createCommand('SELECT member_id, posts FROM ipb_members')->queryAll(),'member_id','posts');

        $users=$db->createCommand('SELECT t.id, t.rating, t.post_count, t.wiki_count, t.extension_count, t.comment_count, f.last_visit, f.joined, f.posts FROM {{%user}} t, ipb_members f WHERE t.id=f.member_id')->queryAll();
        $command=$db->createCommand("UPDATE {{%user}} SET rating=:rating, post_count=:posts, extension_count=:extensions, comment_count=:comments, wiki_count=:wiki WHERE id=:id");
        foreach($users as $user)
        {
            $id=$user['id'];
            $rating=$user['posts']/10;
            if($user['last_visit']>$user['joined'] && $user['joined'])
                $rating+=($user['last_visit']-$user['joined'])/(24*3600*30);
            if(isset($extensionScores[$id]))
                $rating+=$extensionScores[$id];
            if(isset($wikiScores[$id]))
                $rating+=$wikiScores[$id];
            if(isset($commentScores[$id]))
                $rating+=$commentScores[$id];
            if(isset($revisionScore[$id]))
                $rating+=$revisionScore[$id];
            $rating=(int)$rating;
            $comments=isset($commentScores[$id])?$commentScores[$id]:0;
            $wiki=isset($wikiCounts[$id])?$wikiCounts[$id]:0;
            $extensions=isset($extensionCounts[$id])?$extensionCounts[$id]:0;
            $posts=isset($postCounts[$id])?$postCounts[$id]:0;

            if($user['rating']!=$rating || $user['post_count']!=$posts || $user['wiki_count']!=$wiki || $user['extension_count']!=$extensions || $user['comment_count']!=$comments)
            {
                $command
                    ->bindValue(':rating',$rating)
                    ->bindValue(':posts',$posts)
                    ->bindValue(':wiki',$wiki)
                    ->bindValue(':extensions',$extensions)
                    ->bindValue(':comments',$comments)
                    ->bindValue(':id',$id)
                    ->execute();
            }
        }

        $users=$db->createCommand('SELECT id, rank FROM tbl_user ORDER BY rating DESC')->queryAll();
        $command=$db->createCommand('UPDATE tbl_user SET rank=:rank WHERE id=:id');
        foreach($users as $i=>$user)
        {
            $id=$user['id'];
            $rank=$i+1;
            if($rank!=$user['rank'])
                $command->bindValue(':rank',$rank)->bindValue(':id',$id)->execute();
        }
    }
}
