<?php
$this->title = 'Stay InTouch';
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\PostsService;
use common\components\UserService;
use common\components\PhotoService;

/* @var $this yii\web\View */
/* @var $posts \common\components\Post[] */
/* @var $loggedUser \common\components\IntouchUser */
?>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#activity" data-toggle="tab"><?= Yii::t('app', 'Latest Posts'); ?></a></li>
				
				</ul>
				<div class="tab-content">
					
					<?php
					//yii\widgets\Pjax::begin();
					?>
					<div class="active tab-pane" id="activity">
						
						
						<!-- Add post -->
						<?= Html::beginForm(["intouch/index"], 'post',
								['data-pjax' => '', 'enctype'=>"multipart/form-data"]) ?>
						<input class="form-control input-sm send-form-input" row="3" type="text" placeholder="Post"
						       name="inputText">
						<input type="hidden" name="type" value="newpost">
						<!-- Add picture-->
						<div class="btn-file btn btn-default fa fa-t link-black text-sm" style="margin-top: 5px;">
							<i class="fa fa-paperclip "></i><i style="font: inherit"><?= Yii::t('app',
										' Attach Image') ?></i>
							<input type="file" name="kawaiiPicture[]" multiple>
						</div>
						<!-- /Add picture-->
						<button style="width:20%; margin-top:5px;" type="submit"
						        class="btn btn-danger pull-right btn-primary btn-sm"><?= Yii::t('app',
									'Publish'); ?></button>
						<hr>
						<?= Html::endForm() ?>
						<!-- /Add post-->
						<!-- Post -->
						<div id="posts">
							<?php
							$limit = 5;
							foreach ($posts as $row)
							{
								if($limit-- < 0)
								{
									continue;
								}
								$author = $row->getAuthor();
								$comments = $row->getComments();
								?>
								<div class="post">
									<div class="user-block">
										<img class="img-circle img-bordered-sm"
										     src="<?php echo $row->getAuthor()->getImageUrl() ?>"
										     alt="user image">
                                        <span class="username">
                                            <a href="/user/<?= $author->getUsername() ?>"><?= $row->getAuthor()
			                                            ->getFullName() ?></a>
	                                        <?= Html::beginForm(['intouch/index'], 'post') ?>
	                                        <input type="hidden" name="post_id" value="<?= $row->getId() ?>">
                                        <input type="hidden" name="type" value="delete_post"
                                               id="delete_post-form">
                                        <button style="border-style: none; margin-top: 2px" type="submit"
                                                class="pull-right btn-box-tool fa fa-times"></button>
	                                        <?= Html::endForm() ?>
	                                        <?php
	                                        $postOwner = $author->getId();
	                                        if (Yii::$app->user->getId() == $postOwner || Yii::$app->user->can('admin'))
	                                        {
		                                        echo '<button type="button"
                                                                    onclick="window.location.href=\'/post/edit/' .
		                                             $row->getId() . ' \'"
															        class="btn pull-right btn-box-tool dropdown-toggle"
															        data-toggle="dropdown">
																<i class="fa fa-wrench"></i></button>';
	                                        }
	                                        ?>
                                        </span>
                                        <span class="description"><?php
	                                        if ($row->checkVisibility(\common\components\EVisibility::visible()))
	                                        {
		                                        echo Yii::t('app', 'Post public');
	                                        }
	                                        else
	                                        {
		                                        echo Yii::t('app', 'Post hidden');
	                                        }
	                                        ?> - <?php echo($row->getDate()); ?></span>
									</div>
									<!-- /.user-block -->
									<p>
										<?php
										$attachment = $row->getAttachments();
										//die(var_dump($attachment));
										/* @var $attachment common\components\PostAttachment */
										echo $row->getContent();
										if ($attachment != null)
										{
										$attachment = $attachment->getFile();
										echo "<br>";
										?>
									<div class="row margin-bottom">
										<?php foreach ($attachment as $att)
										{ ?>
											<div class="col-sm-6">
												<img class="img-responsive"
												     src="<?= $att ?>"
												     alt="Photo">
											</div>
										<?php } ?>
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
										<li>
											<?php echo Html::beginForm(['intouch/index'], 'post') ?>
											<input type="hidden" name="post_id" value="<?= $row->getId() ?>">
											<input type="hidden" name="score_elem" value="post">
											<input type="hidden" name="user_id" value="<?= $loggedUser->getId() ?>">
											<input class="" type="hidden" name="type" value="like" id="like-form">
                                            <span class="link-black text-sm">
                                                <i class="fa fa-thumbs-o-up"></i>
                                                <input type="submit" class="fa fa-t humbs-o-up link-black text-sm"
                                                       style="background: none; border: none;"
                                                       value="<?=
                                                       Yii::t('app', 'Like') .
                                                       " (" .
                                                       $row->countScoresByType(\common\components\EScoreType::like()) .
                                                       ")"
                                                       ?> ">
                                            </span>
											<?= Html::endForm() ?>
										</li>
										<li class="pull-right">
											<a href="#" class="link-black text-sm"><i
														class="fa fa-comments-o margin-r-5"></i> <?= Yii::t('app',
														'Comments'); ?> (<?= (count($comments)) ?>)</a>
										</li>
										<li class="pull-right">
											<?php echo Html::beginForm(['intouch/profile'], 'post') ?>
											<input type="hidden" name="post_id" value="<?= $row->getId() ?>">
											<input type="hidden" name="score_elem" value="post">
											<input type="hidden" name="user_id" value="<?= $loggedUser->getId() ?>">
											<input class="" type="hidden" name="type" value="report" id="like-form">
                                            <span class="link-black text-sm">
                                                <i class="fa fa-exclamation"></i>
                                                <input type="submit" class="link-black text-sm"
                                                       style="background: none; border: none;"
                                                       value="<?= Yii::t('app', 'Report'); ?>">
                                            </span>
											
											<?= Html::endForm() ?>
										</li>
									</ul>
									<?= Html::beginForm(["intouch/index", 'uname' => $loggedUser->getUsername()],
											'post',
											['data-pjax' => '']) ?>
									<input class="form-control input-sm send-form-input" type="text"
									       placeholder="<?= Yii::t('app', 'Type a comment'); ?>" name="inputText">
									<input type="hidden" name="type" value="newcomment">
									<input type="hidden" name="post_id" value="<?= $row->getId() ?>">
									<button style="width:20%; margin-top:5px;" type="submit"
									        class="btn btn-danger btn-block btn-sm hidden"></button>
									<?= Html::endForm() ?>
									<div class="direct-chat-msg" style="margin-top: 10px;">
										<div class="direct-chat-info clearfix">
										</div>
										<!-- /.direct-chat-info -->
										<?php
										foreach ($comments as $comment)
										{
											$comAuthor = $comment->getAuthor();
											?>
											<div style="background-color: #EDF5F7; padding: 10px 10px 1px 10px; border-radius: 10px; margin-left: 30px; margin-bottom:5px;">
												<img class="direct-chat-img"
												     src="<?php echo $comAuthor->getImageUrl() ?>"
												     alt="message user image" style="margin-right: 10px;">
												<!-- /.direct-chat-img -->
												
												<?php echo Html::beginForm(['intouch/index'], 'post') ?>
												<input type="hidden" name="comment_id" value="<?= $comment->getId() ?>">
												<input class="" type="hidden" name="type" value="delete_comment"
												       id="delete_post-form">
												<button style="border-style: none; margin-top: 2px" type="submit"
												        class="pull-right btn-box-tool fa fa-times"></button>
												<?= Html::endForm() ?>
												
												<p class="message">
													<a href="#" class="name">
														<small class="text-muted pull-right">
															<i class="fa fa-clock-o"></i> <?= $comment->getDate() ?>
															<?php
															
															$nothing = $comment->getAuthor();
															$commentOwner = $nothing->getId();
															if (Yii::$app->user->getId() == $commentOwner ||
															    Yii::$app->user->can('admin')
															)
															{
																echo '<button type="button"
                                                                    onclick="window.location.href=\'/post/commentEdit/' .
																     $comment->getId() . '\'"
															        class="btn btn-box-tool dropdown-toggle"
															        data-toggle="dropdown">
																<i class="fa fa-wrench"></i></button>';
															}
															?>
														</small>
														<?= $comAuthor->getFullName() ?>
														<br>
													</a>
													<?= $comment->getContent() ?>
												</p>
											</div>
										<?php } ?>
										<!-- /.direct-chat-text -->
									</div>
								</div>
								
								<?php
							}
							?>
						</div>
						
						<!-- /.post -->
					</div>
					<?php
					//yii\widgets\Pjax::end();
					?>
				</div>
				<div class="box-footer text-center hidden">
					<a href="javascript::;" class="btn btn-sm btn-info btn-flat"><?= Yii::t('app',
								'View more posts'); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>