<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\PostsService;
use common\components\UserService;
use common\components\PhotoService;

foreach ($posts as $row)
{
	if ($row['post_visibility'] == "visible")
	{
		?>
		<div class="post">
			<div class="user-block">
				<img class="img-circle img-bordered-sm" src="<?php echo $row['photo']; ?>"
				     alt="user image">
                                        <span class="username">
                                            <a href="#"><?php echo($row['name'] . " " . $row['surname']); ?></a>
                                            <a href="#" class="pull-right btn-box-tool"><i class="fa fa-times"></i></a>
	                                        <a href="#" class="pull-right btn-box-tool"><i class="fa fa-wrench"></i></a>
                                        </span>
                                        <span class="description"><?php
	                                        if ($row['post_visibility'] == "visible")
	                                        {
		                                        echo Yii::t('app', 'Post public');
	                                        }
	                                        else
	                                        {
		                                        echo Yii::t('app', 'Post hidden');
	                                        }
	                                        ?> - <?php echo($row['post_date']); ?></span>
			</div>
			<!-- /.user-block -->
			<p>
				<?php
				$attachments = $row['attachments'];
				if ($row['post_type'] == "text")
				{
					echo $row['post_text'];
				}
				else if ($row['post_type'] == "gallery")
				{
				echo $row['post_text'] . "<br>";
				?>
			<div class="row margin-bottom">
				<div class="col-sm-6">
					<img class="img-responsive"
					     src="../../dist/content/attachments/<?php echo $attachments[0]['file']; ?>"
					     alt="Photo">
				</div>
				<!-- /.col -->
				<div class="col-sm-6">
					<div class="row">
						<div class="col-sm-6">
							<img class="img-responsive"
							     src="../../dist/content/attachments/<?php echo $attachments[1]['file']; ?>"
							     alt="Photo">
							<br>
							<img class="img-responsive"
							     src="../../dist/content/attachments/<?php echo $attachments[2]['file']; ?>"
							     alt="Photo">
						</div>
						<!-- /.col -->
						<div class="col-sm-6">
							<img class="img-responsive"
							     src="../../dist/content/attachments/<?php echo $attachments[3]['file']; ?>"
							     alt="Photo">
							<br>
							<?php
							if (isset($attachments[4]['file']))
							{
								?><img class="img-responsive"
								       src="../../dist/content/attachments/<?php echo $attachments[1]['file']; ?>"
								       alt="Photo"> <?php } ?>
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.col -->
			</div>
			<?php
			}
			?>

			</p>
			<ul class="list-inline">
				<li><a href="#" class="link-black text-sm"><i
							class="fa fa-share margin-r-5"></i> <?= Yii::t('app',
							'Share'); ?></a></li>
				<li><a href="#" class="link-black text-sm"><i
							class="fa fa-thumbs-o-up margin-r-5"></i> <?= Yii::t('app',
							'Like'); ?></a>
				</li>
				<li class="pull-right">
					<a href="#" class="link-black text-sm"><i
							class="fa fa-comments-o margin-r-5"></i> <?= Yii::t('app',
							'Comments'); ?> (<?php echo(count($row['comments'])); ?>
						)</a>
				</li>
				<li class="pull-right">
					<a href="#" class="link-black text-sm"><i
							class="fa fa-exclamation margin-r-5"></i><?= Yii::t('app',
							'Report'); ?></a>
				</li>
			</ul>
			<?= Html::beginForm(["intouch/index", 'uname' => $UserName], 'post',
				['data-pjax' => '']) ?>
			<input class="form-control input-sm send-form-input" type="text"
			       placeholder="<?= Yii::t('app', 'Type a comment'); ?>" name="inputText">
			<input type="hidden" name="type" value="newcomment">
			<input type="hidden" name="post_id" value="<?= $row['post_id'] ?>">
			<button style="width:20%; margin-top:5px;" type="submit"
			        class="btn btn-danger btn-block btn-sm hidden"></button>
			<?= Html::endForm() ?>
			<div class="direct-chat-msg" style="margin-top: 10px;">
				<div class="direct-chat-info clearfix">
				</div>
				<!-- /.direct-chat-info -->
				<?php
				foreach ($row['comments'] as $comment)
				{
					?>
					<div style="background-color: #EDF5F7; padding: 10px 10px 1px 10px; border-radius: 10px; margin-left: 30px; margin-bottom:5px;">
						<img class="direct-chat-img" src="<?php echo $comment['photo']; ?>"
						     alt="message user image" style="margin-right: 10px;">
						<!-- /.direct-chat-img -->
						<a href="#" class="pull-right btn-box-tool"><i
								class="fa fa-times"></i></a>
						<a href="#" class="pull-right btn-box-tool"><i
								class="fa fa-wrench"></i></a>
						<p class="message">
							<a href="#" class="name">
								<small class="text-muted pull-right">
									<i class="fa fa-clock-o"></i> <?php echo $comment['comment_date']; ?>
								</small>
								<?php echo($comment['name'] . " " . $comment['surname']); ?>
								<br>
							</a>
							<?php echo $comment['comment_text']; ?>
						</p>
					</div>
				<?php } ?>
				<!-- /.direct-chat-text -->
			</div>
		</div>

		<?php

	}
}