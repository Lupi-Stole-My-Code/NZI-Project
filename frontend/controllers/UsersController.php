<?php

namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\UserService;
use common\models\User;
use common\components;
use common\components\RelationService;
use common\components\RelationMode;
use common\components\RelationType;
use common\components\PhotoService;
use common\components\AccessService;
use common\components\RequestService;
use common\components\Permission;
use common\components\PostsService;
use common\components\RequestType;
use common\components\EScoreElem;
use common\components\EScoreType;
use common\components\ScoreService;

class UsersController extends components\GlobalController
{

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	public function actionView($uname)
	{
		/////////////////////////--- Profile Infos ---//////////////////////////
		$id = UserService::getUserIdByName($uname);

		if ($id === false)
		{
			throw new \yii\web\NotFoundHttpException("User cannot be found");
		}
		$uid = new components\UserId($id);
		$myId = Yii::$app->user->getId();
		$myUid = new components\UserId($myId);
		if ($id == $myId)
		{
			return $this->redirect('/profile');
		}
		if (Yii::$app->request->isPost || Yii::$app->request->isPjax)
		{
			if (Yii::$app->user->can('relations-manage-own'))
			{
				$request = Yii::$app->request;
				if (!is_null($request->post('follow-btn')) && Yii::$app->user->can('relations-follow'))
				{
					RelationService::setRelation($myUid, $uid, RelationType::Follower);
					components\EventService::createEvent(components\EEvent::FOLLOWS(), $uid, false, $myUid);
					components\EventService::createEvent(components\EEvent::FOLLOWS(), $myUid, true, $uid);
				}
				if (!is_null($request->post('friend-btn')) && Yii::$app->user->can('relations-friend'))
				{
					RequestService::createRequest($myUid, $uid, RequestType::FriendRequest,
						date('Y-m-d H:i:s')); //to tutaj
					components\EventService::createEvent(components\EEvent::FRIEND_REQUEST_SENT(), $uid, false, $myUid);
					components\EventService::createEvent(components\EEvent::FRIEND_REQUEST_SENT(), $myUid, true, $uid);
				}

				if (!is_null($request->post('unfriend-btn')))
				{
					RelationService::removeRelation($myUid, $uid, RelationType::Friend);
					components\EventService::createEvent(components\EEvent::UNFRIEND(), $uid, false, $myUid);
					components\EventService::createEvent(components\EEvent::UNFRIEND(), $myUid, true, $uid);
				}
				if (!is_null($request->post('unfollow-btn')))
				{
					RelationService::removeRelation($myUid, $uid, RelationType::Follower);
					components\EventService::createEvent(components\EEvent::UNFOLLOWS(), $uid, false, $myUid);
					components\EventService::createEvent(components\EEvent::UNFOLLOWS(), $myUid, true, $uid);
				}

				if (!is_null(Yii::$app->request->post('type')))
				{
					switch (Yii::$app->request->post('type'))
					{
						case 'newpost':
							$post_id = PostsService::createPost($uid, Yii::$app->request->post('inputText'));
							$pliks = $_FILES['kawaiiPicture']['tmp_name'];
							if ($pliks[0] != '')
							{
								PhotoService::addPostAttachmentPhoto($pliks, $post_id);
							}
							components\EventService::createEvent(components\EEvent::POST_CREATE(), $uid, false, $myUid);
							components\EventService::createEvent(components\EEvent::POST_CREATE(), $myUid, true, $uid);
							break;

						case 'newcomment':
							PostsService::createComment(PostsService::getPostById(Yii::$app->request->post('post_id')),
								Yii::$app->request->post('inputText'));
							components\EventService::createEvent(components\EEvent::COMMENT_CREATE(), $uid, false, $myUid);
							components\EventService::createEvent(components\EEvent::COMMENT_CREATE(), $myUid, true, $uid);
							break;

						case 'delete_post':
							$rep_post_id = Yii::$app->request->post('post_id');
							PostsService::deletePost(PostsService::getPostById($rep_post_id));
							components\EventService::createEvent(components\EEvent::POST_DELETE(), $uid, false, $myUid);
							components\EventService::createEvent(components\EEvent::POST_DELETE(), $myUid, true, $uid);
							break;
						case 'delete_comment':
							$rep_comment_id = Yii::$app->request->post('comment_id');
							PostsService::deleteComment(PostsService::getCommentById($rep_comment_id));
							components\EventService::createEvent(components\EEvent::COMMENT_DELETE(), $uid, false, $myUid);
							components\EventService::createEvent(components\EEvent::COMMENT_DELETE(), $myUid, true, $uid);
							break;
					}
				}
			}
			else
			{
				$this->redirect(["intouch/accessdenied"]);
			}
			if (!is_null(Yii::$app->request->post('type')))
			{
				switch (Yii::$app->request->post('type'))
				{
					case 'like':
						$like_form_post_id = Yii::$app->request->post('post_id');
						$like_form_score_elem = Yii::$app->request->post('score_elem');
						$like_form_user_id = Yii::$app->request->post('user_id');
						$score = new components\Score(EScoreType::like(), null, EScoreElem::$like_form_score_elem(),
							$like_form_post_id, new components\UserId($like_form_user_id));
						$existing_scores = ScoreService::getScoresByElem(EScoreElem::post(), $like_form_post_id);
						$found = false;
						foreach ($existing_scores as $var)
						{
							$user = $var->getPublisher();
							$userId = $user->getId();
							if ((int)$like_form_user_id == $userId && (int)$like_form_post_id == $var->getElementId())
							{
								$found = true;
								$found_score_id = $var->getScoreId();
							}
						}
						if (!$found)
						{
							ScoreService::addScore($score);
							components\EventService::createEvent(components\EEvent::POST_LIKED(), $uid, false, $myUid);
							components\EventService::createEvent(components\EEvent::POST_LIKED(), $myUid, true, $uid);
						}
						else
						{
							ScoreService::revokeScore($found_score_id);
							components\EventService::createEvent(components\EEvent::POST_UNLIKED(), $uid, false, $myUid);
							components\EventService::createEvent(components\EEvent::POST_UNLIKED(), $myUid, true, $uid);
						}
						break;
				}
			}
		}

		$user = $uid->getUser();
		$followers = count(RelationService::getUsersWhoFollowMe($uid));
		$following = count(RelationService::getUsersWhoIFollow($uid));
		$friends = count(RelationService::getFriendsList($uid));
		/////$$$$$ FORMS $$$$$//////////////////////////////////////////////////
		////////////////////////////--- Other stuff ---/////////////////////////
		$UserRelations = RelationService::getRelations($myUid, $uid);
		$isFriend = $UserRelations[RelationType::Friend];
		if (!$isFriend)
		{
			if (RequestService::isRequestBetween($uid, $myUid, RequestType::FriendRequest))
			{
				$isFriend = "Friend Request Sent";
			}
		}
		$IFollow = $UserRelations[RelationType::Follower];
		//***Do not add anything new below this line (except for the render)****
		//$this->getUserData();
		$posts = PostsService::getUserPosts($uid);
		$shared = [
			'user' => $user,
			'followers' => $followers,
			'following' => $following,
			'friends' => $friends,
			'UserFollowState' => $IFollow,
			'UserFriendshipState' => $isFriend,
			'posts' => $posts,
		];
		return $this->render('view', $shared);
	}
}
