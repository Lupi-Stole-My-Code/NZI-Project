<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
<?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="/" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>I</b>T</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>In</b>Touch</span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">

                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                                    <!-- <?php echo Html::img('@web/dist/img/guest.png', ['class' => "user-image"]) ?>-->
                                    <i class="fa fa-sign-in"></i>
                                    <span class="hidden-xs">Control</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->

                                    <li class="user-header">

<?= Html::img($this->params['userProfilePhoto'], ['class' => 'img-circle', 'alt' => 'User Image']) ?>
                                        <p style='color:black; font-weight:bold'><?= $this->params['userInfo']['user_name'] . ' ' . $this->params['userInfo']['user_surname'] ?></p>
                                        <p>
                                            You're InTouch now.
                                        </p>

                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">

                                        <div class="col-xs-4 text-center">
                                            <a href="#">Friends</a>
                                        </div>
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="/profile" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= Url::to(['/logout']) ?>" data-method="post" class="btn btn-default btn-flat">Logout</a>                                           
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
                            <li>
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
<?= Html::img($this->params['userProfilePhoto'], ['class' => 'img-circle', 'alt' => 'User Image']) ?>
                        </div>
                        <div class="pull-left info">
                            <p><?= $this->params['userInfo']['user_name'] . ' ' . $this->params['userInfo']['user_surname'] ?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>

                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="header">MAIN NAVIGATION</li>
                        <li>
                            <a href="/profile">
                                <i class="fa fa-user"></i> <span><?= Yii::t('app', 'Profile') ?></span> 
                            </a>
                        </li>


                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <!-- Main content -->
                <section class="content">
                    <?= Alert::widget() ?>
<?= $content ?>
                </section>
            </div>


<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
