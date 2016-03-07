<?php

namespace common\components;

use app\models\UserInfo;
use common\models\User;
use common\components\exceptions\InvalidDateException;
use common\components\exceptions\InvalidUserException;
use yii\db\Query;
use app\models\Post;
use app\models\PostAttachment;
use app\models\Comment;
use Yii;

class PostsService
{

    public static function getPosts($id)
    {
        $data    = Post::find()->where(['user_id' => $id])->orderBy(['post_date' => SORT_DESC])->all();
        $counter = 0;
        foreach ($data as $row)
        {
            $refined_data[$counter]['post_id'] = (int) $row['post_id'];
            if ($row['owner_id'] != NULL)
            {
                $refined_data[$counter]['owner_id'] = $row['owner_id'];
                $refined_data[$counter]['name']     = UserService::getName($row['owner_id']);
                $refined_data[$counter]['surname']  = UserService::getSurname($row['owner_id']);
            }
            else
            {
                $refined_data[$counter]['owner_id'] = $id;
                $refined_data[$counter]['name']     = UserService::getName($id);
                $refined_data[$counter]['surname']  = UserService::getSurname($id);
            }
            $refined_data[$counter]['post_visibility'] = $row['post_visibility'];
            $refined_data[$counter]['post_date']       = $row['post_date'];
            $refined_data[$counter]['post_type']       = $row['post_type'];
            $refined_data[$counter]['post_text']       = $row['post_text'];
            $refined_data[$counter]['comments']        = PostsService::getComments($row['post_id']);
            $refined_data[$counter]['attachments']     = PostsService::getAttachments($row['post_id']);
            $refined_data[$counter]['photo']           = PhotoService::getProfilePhoto($refined_data[$counter]['owner_id'], true, true);
            $counter++;
        }

        return isset($refined_data) ? $refined_data : [];
    }

    public static function createPost($receiver_id, $text)
    {
        $author_id = Yii::$app->user->getId();
        try
        {
            if (!AccessService::hasAccess($receiver_id, ObjectCheckType::Post))
            {
                Yii::$app->session->setFlash('error', 'Access Denied');
                return false;
            }
        }
        catch (Exception $ex)
        {
            Yii::$app->session->setFlash('warning', 'Something went wrong, contact Administrator');
            return false;
        }
        $post = new Post();
        if ($receiver_id != $author_id)
        {
            $post->owner_id = $author_id;
        }
        $post->user_id         = $receiver_id;
        $post->post_text       = $text;
        $post->post_date       = date('Y-m-d H:i:s');
        $post->post_type       = "text";
        $post->post_visibility = "visible";
        return $post->save();
    }

    public static function createComment($post_id, $text)
    {
        try
        {
            if (!AccessService::hasAccess($post_id, ObjectCheckType::PostComment))
            {
                Yii::$app->session->setFlash('error', 'Access Denied');
                return false;
            }
        }
        catch (Exception $ex)
        {
            Yii::$app->session->setFlash('warning', 'Something went wrong, contact Administrator');
            return false;
        }
        $comment               = new Comment();
        $author_id             = Yii::$app->user->getId();
        $comment->author_id    = $author_id;
        $comment->comment_text = $text;
        $comment->comment_date = date('Y-m-d H:i:s');
        $comment->post_id      = $post_id;
        $comment->save();
    }

    public static function getNumberOfComments($post_id)
    {
        $data = Comment::find()->where(['post_id' => $post_id])->all();
        return isset($data) ? count($data) : false;
    }

    public static function getPostDate($post_id)
    {
        $data = Post::find()->where(['post_id' => $post_id])->one();
        return isset($data) ? $data['post_date'] : false;
    }

    public static function getComments($post_id)
    {
        $data         = Comment::find()->where(['post_id' => $post_id])->all();
        $counter      = 0;
        $refined_data = [];
        foreach ($data as $row)
        {
            $refined_data[$counter]['comment_text'] = $row['comment_text'];
            $refined_data[$counter]['name']         = UserService::getName($row['author_id']);
            $refined_data[$counter]['surname']      = UserService::getSurname($row['author_id']);
            $refined_data[$counter]['comment_date'] = $row['comment_date'];
            $refined_data[$counter]['photo']        = PhotoService::getProfilePhoto($row['author_id'], true, true);
            $counter++;
        }
        return isset($refined_data) ? $refined_data : false;
    }

    public static function getAttachments($post_id)
    {
        $data = PostAttachment::find()->where(['post_id' => $post_id])->all();
        return isset($data) ? $data : false;
    }

}