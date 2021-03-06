<style>
    .hr {
        width: 95%;
        font-size: 1px;
        color: rgba(0, 0, 0, 0);
        line-height: 1px;

        background-color: grey;
        margin-top: -6px;
        margin-bottom: 10px;
    }

    #hr1 {
        position: relative;
        top: 10em;
    }

    .userbox {
        background-color: #EDF5F7;
        padding: 10px 10px 1px 10px;
        border-radius: 10px;
        margin-left: 30px;
        margin-bottom: 5px;
        max-width: 90%;
    }
</style>
<div>
    <font size="5"><?= Yii::t('app', 'My friends'); ?></font>
    <div class="hr" id="hr1">.</div>
</div>
<br/>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $friends \common\components\IntouchUser[] */

if (count($friends) == 0)
{
    echo Yii::t('app', 'You don\'t have friends. Invite someone :)');;
    ?>
    <br/>
    <center>
        <div>
            <center><img src='<?= $imgForeverAlone ?>'/></center>

            <img src='<?= $imgForeverAloneText ?>'/>
        </div>
    </center>
    <?php
}
foreach ($friends as $friend)
{
    ?>
    <div class="userbox">
        <img class="direct-chat-img" src="<?= $friend->getImageUrl() ?>" alt="message user image" style="margin-right: 10px;">

        <p><?= $friend->getFullName() . " (" . $friend->getUsername() . ")" ?></p>
        <div>
            <a href="/user/<?= $friend->getUsername() ?>"><?= Yii::t('app', 'Profile'); ?></a>
            |
            <a href="mailto:<?= $friend->getEmail() ?>"><?= Yii::t('app', 'Send e-mail'); ?></a>
        </div>
    </div>
    <?php
}

