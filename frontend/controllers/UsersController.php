<?php

namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserService;
use common\models\User;
use app\components;
use app\components\RelationService;
use app\components\RelationMode;
use app\components\RelationType;
use app\components\PhotoService;
use app\components\AccessService;
use app\components\RequestService;
use app\components\Permission;
use app\components\PostsService;
use app\components\RequestType;

class UsersController extends Controller
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

    public function actionView($uname)
    {
        /////////////////////////--- Profile Infos ---//////////////////////////
        $id = UserService::getUserIdByName($uname);
        if($id === false)
        {
            throw new \yii\web\NotFoundHttpException("User cannot be found");
        }
        $myId = Yii::$app->user->getId();
        if ($id == $myId)
        {
            return $this->redirect('/profile');
        }
        $education = UserService::getUserEducation($id);
        $about = UserService::getUserAbout($id);
        $city = UserService::getUserCity($id);
        $birth = UserService::getBirthDate($id);
        $name = UserService::getName($id);
        $surname = UserService::getSurname($id);
        if (strlen($name) == 0 || strlen($surname) == 0)
        {
            $name = "Dane nie uzupełnione";
            $surname = "";
        }
        $email = UserService::getEmail($id);
        $followers = count(RelationService::getUsersWhoFollowMe($id));
        $following = count(RelationService::getUsersWhoIFollow($id));
        $friends = count(RelationService::getFriendsList($id));
        $photo = PhotoService::getProfilePhoto($id, true, true);
        /////$$$$$ FORMS $$$$$//////////////////////////////////////////////////
        if (Yii::$app->request->isPjax)
        {
            if (AccessService::check(Permission::ManageUserRelations))
            {
                $request = Yii::$app->request;
                if (!is_null($request->post('follow-btn')))
                {
                    RelationService::setRelation($myId, $id, RelationType::Follower);
                }
                if (!is_null($request->post('friend-btn')))
                {
                    RequestService::createRequest($myId, $id, RequestType::FriendRequest, date('Y-m-d H:i:s')); //to tutaj
                }
                if (!is_null($request->post('unfriend-btn')))
                {
                    RelationService::removeRelation($myId, $id, RelationType::Friend);
                }
                if (!is_null($request->post('unfollow-btn')))
                {
                    RelationService::removeRelation($myId, $id, RelationType::Follower);
                }
            }
            else
            {
                $this->redirect("intouch/accessdenied");
            }
        }
        if (Yii::$app->request->isPost)
        {
            if (!is_null(Yii::$app->request->post('type')))
            {
                switch (Yii::$app->request->post('type'))
                {
                    case 'newpost':
                        PostsService::createPost($id, Yii::$app->request->post('inputText'));
                        break;

                    case 'newcomment':
                        PostsService::createComment(Yii::$app->request->post('post_id'), Yii::$app->request->post('inputText'));
                        break;
                }
            }
        }

        ////////////////////////////--- Other stuff ---/////////////////////////
        $UserRelations = RelationService::getRelations($myId, $id);
        $isFriend = $UserRelations[RelationType::Friend];
        if (!$isFriend)
        {
            if (RequestService::isRequestBetween($id, $myId, RequestType::FriendRequest))
            {
                $isFriend = "Friend Request Sent";
            }
        }
        $IFollow = $UserRelations[RelationType::Follower];
        $uname = UserService::getUserName($id);
        //***Do not add anything new below this line (except for the render)****
        $this->getUserData($id);
        $this->layout = 'logged';
        $posts = PostsService::getPosts($id);
        $shared = [
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'education' => $education,
            'about' => $about,
            'city' => $city,
            'birth' => $birth,
            'followers' => $followers,
            'following' => $following,
            'friends' => $friends,
            'UserFollowState' => $IFollow,
            'UserFriendshipState' => $isFriend,
            'UserName' => $uname,
            'UserProfilePhoto' => $photo,
            'id' => $id,
            'posts' => $posts,
            'photo' => $photo,
            'myId' => $myId,
        ];
        
        $this->getUserData();
        $this->layout = "logged";
        return $this->render('view', $shared);
    }
    
    private function getUserData()
    {
        $id = Yii::$app->user->getId();

        $photo = \app\components\PhotoService::getProfilePhoto($id);

        if (is_string($photo))
        {
            $location = "@web/dist/content/images/";
            //TODO set chmod for that directory(php init)
            $this->view->params['userProfilePhoto'] = $location . $photo;
        }
        else
        {
            $location = "@web/dist/img/guest.png";
            //TODO add that file
            $this->view->params['userProfilePhoto'] = $location;
        }

        $userinfo = array();
        $userinfo['user_name'] = UserService::getName($id);
        $userinfo['user_surname'] = UserService::getSurname($id);
        if ($userinfo['user_name'] == false)
        {
            $userinfo['user_name'] = "Uzupełnij";
        }
        if ($userinfo['user_surname'] == false)
        {
            $userinfo['user_surname'] = "swoje dane";
        }

        $this->view->params['userInfo'] = $userinfo;
        ////////////////////////////////////////////////////// request service

        $notification = RequestService::getMyRequests($id);
        $tablelength = count($notification);
        $this->view->params['notification_data'] = $notification;
        $this->view->params['notification_count'] = $tablelength;
    }

}
