<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $uh ConcreteUrlsHelper */ 
$uh = Loader::helper('concrete/urls');
/* @var $form FormHelper */
$form = Loader::helper('form');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/ui');
$a = $view->getAreaObject();
$bt = BlockType::getByHandle('form');

$addSelected = true;
?>
<p>
<?php print Loader::helper('concrete/ui')->tabs(array(
	array('form-add', t('Add'), $addSelected),
	array('form-edit', t('Edit')),
	array('form-preview', t('Preview')),
	array('form-options', t('Options')),
));?>
</p>

<input type="hidden" name="miniSurveyServices" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/services" />
<?php /* these question ids have been deleted, or edited, and so shouldn't be duplicated for block versioning */ ?>
<input type="hidden" id="ccm-ignoreQuestionIDs" name="ignoreQuestionIDs" value="" />
<input type="hidden" id="ccm-pendingDeleteIDs" name="pendingDeleteIDs" value="" />
<input type="hidden" id="qsID" name="qsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />
<input type="hidden" id="oldQsID" name="oldQsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />
<input type="hidden" id="msqID" name="msqID" type="text" value="<?php echo intval($msqID)?>" />
	

<div class="ccm-tab-content" id="ccm-tab-content-form-options">
	<?php 
	$c = Page::getCurrentPage();
	if(strlen($miniSurveyInfo['surveyName'])==0)
		$miniSurveyInfo['surveyName']=$c->getCollectionName();
	?>
	<fieldset>
		<legend><?=t('Options')?></legend>
		<div class="form-group">
			<?=$form->label('surveyName', t('Form Name'))?>
			<?=$form->text('surveyName', $miniSurveyInfo['surveyName'])?>
		</div>
		<div class="form-group">
			<?=$form->label('thankyouMsg', t('Message to display when completed'))?>
			<?=$form->textarea('thankyouMsg', $this->controller->thankyouMsg, array('rows' => 3))?>
		</div>
		<div class="form-group">
			<?=$form->label('recipientEmail', t('Notify me by email when people submit this form'))?>
			<div class="input-group">
				<span class="input-group-addon" style="z-index: 2000">
				<?=$form->checkbox('notifyMeOnSubmission', 1, $miniSurveyInfo['notifyMeOnSubmission'] == 1, array('onclick' => "$('input[name=recipientEmail]').focus()"))?>
				</span><?=$form->text('recipientEmail', $miniSurveyInfo['recipientEmail'], array('style' => 'z-index:2000;' ))?>
			</div>
			<span class="help-block"><?=t('(Seperate multiple emails with a comma)')?></span>
		</div>
		<div class="form-group">
			<label class="control-label"><?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha')?></label>
			<div class="radio">
			<label>
				<?=$form->radio('displayCaptcha', 1, (int) $miniSurveyInfo['displayCaptcha'])?>
				<span><?=t('Yes')?></span>
			</label>
			</div>
			<div class="radio">
			<label>
				<?=$form->radio('displayCaptcha', 0, (int) $miniSurveyInfo['displayCaptcha'])?>
				<span><?=t('No')?></span>
			</label>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label" for="ccm-form-redirect"><?=t('Redirect to another page after form submission?')?></label>
			<div id="ccm-form-redirect-page">
				<?php
					$page_selector = Loader::helper('form/page_selector');
					if ($miniSurveyInfo['redirectCID']) {
						print $page_selector->selectPage('redirectCID', $miniSurveyInfo['redirectCID']);
					} else {
						print $page_selector->selectPage('redirectCID');
					}
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label" for="ccm-form-fileset"><?=t('Add uploaded files to a set?')?></label>
			<div id="ccm-form-fileset">
				<?php
					
					$fs = new FileSet();
					$fileSets = $fs->getMySets();
					$sets = array(0 => t('None'));
					foreach($fileSets as $fileSet) {
						$sets[$fileSet->fsID] = $fileSet->fsName;
					}
					print $form->select('addFilesToSet', $sets, $miniSurveyInfo['addFilesToSet']);
				?>
			</div>
		</div>
	</fieldset>
</div> 
	
<div class="ccm-tab-content" id="ccm-tab-content-form-add">
	

	<fieldset id="newQuestionBox">
		<legend><?php echo t('New Question')?></legend>		
		
		<div id="questionAddedMsg" class="alert alert alert-info" style="display:none">
			<?=t('Question added. To view it click the preview tab.')?>
		</div>

		<div class="form-group">
			<?=$form->label('question', t('Question'))?>
			<?=$form->text('question')?>
		</div>
		<div class="form-group">
			<?=$form->label('answerType', t('Answer Type'))?>
			<select class="form-control" name="answerType" id="answerType">
				<option value="field"><?=t('Text Field')?></option>
				<option value="text"><?=t('Text Area')?></option>
				<option value="radios"><?=t('Radio Buttons')?></option>
				<option value="select"><?=t('Select Box')?></option>
				<option value="checkboxlist"><?=t('Checkbox List')?></option>
				<option value="fileupload"><?=t('File Upload')?></option>
				<option value="email"><?=t('Email Address')?></option>
				<option value="telephone"><?=t('Telephone')?></option>
				<option value="url"><?=t('Web Address')?></option>
				<option value="date"><?=t('Date Field')?></option>
				<option value="datetime"><?=t('DateTime Field')?></option>
			</select>
		</div>
		
		<div id="answerOptionsArea">
			<div class="form-group">
				<?=$form->label('answerOptions', t('Answer Options'))?>
				<?=$form->textarea('answerOptions', array('rows' => 3))?>
				<span class="help-block"><?=t('Put each answer options on a new line')?></span>
			</div>
		</div>

		<div id="answerSettings">
			<div class="form-group">
				<?=$form->label('width', t('Text Area Width'))?>
				<?=$form->text('width', 50)?>
			</div>
			<div class="form-group">
				<?=$form->label('height', t('Text Area Height'))?>
				<?=$form->text('height', 3)?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label"><?=t('Required')?></label>
			<div class="radio"><label><?=$form->radio('required', 1)?> <?=t('Yes')?></label></div>
			<div class="radio"><label><?=$form->radio('required', 0)?> <?=t('No')?></label></div>
		</div>

		<div class="form-group">
			<div id="emailSettings">
				<?php print $form->label('send_notification_from', t('Reply to this email address'));?>
				<span class="send_notification_from"><?php print $form->checkbox('send_notification_from', 1); ?></span>
			</div>
		</div>

		<div class="form-group">
			<?=$ih->button(t('Add Question'), '#', '', '', array('id' => 'addQuestion'))?>
		</div>
		
	</fieldset> 
</div> 
		
<div class="ccm-tab-content" id="ccm-tab-content-form-edit">
	
	<div id="questionEditedMsg" class="alert alert-success" style="display:none">
		<?php echo t('Your question has been saved.')?>
	</div>
	
	<div id="editQuestionForm" style="display:none">
		<fieldset>
			<legend id="editQuestionTitle"><?=t('Edit Question')?></legend>

			<div class="form-group">
				<?=$form->label('questionEdit', t('Question'))?>
				<?=$form->text('questionEdit')?>
			</div>
			<div class="form-group">
				<?=$form->label('answerTypeEdit', t('Answer Type'))?>
				<select class="form-control" name="answerTypeEdit" id="answerTypeEdit">
					<option value="field"><?=t('Text Field')?></option>
					<option value="text"><?=t('Text Area')?></option>
					<option value="radios"><?=t('Radio Buttons')?></option>
					<option value="select"><?=t('Select Box')?></option>
					<option value="checkboxlist"><?=t('Checkbox List')?></option>
					<option value="fileupload"><?=t('File Upload')?></option>
					<option value="email"><?=t('Email Address')?></option>
					<option value="telephone"><?=t('Telephone')?></option>
					<option value="url"><?=t('Web Address')?></option>
					<option value="date"><?=t('Date Field')?></option>
					<option value="datetime"><?=t('DateTime Field')?></option>
				</select>
			</div>

			
			<div id="answerOptionsAreaEdit">
				<div class="form-group">
					<?=$form->label('answerOptionsEdit', t('Answer Options'))?>
					<?=$form->textarea('answerOptionsEdit', array('rows' => 3))?>
					<span class="help-block"><?=t('Put each answer options on a new line')?></span>
				</div>
			</div>

			<div id="answerSettingsEdit">
				<div class="form-group">
					<?=$form->label('widthEdit', t('Text Area Width'))?>
					<?=$form->text('widthEdit', 50)?>
				</div>
				<div class="form-group">
					<?=$form->label('heightEdit', t('Text Area Height'))?>
					<?=$form->text('heightEdit', 3)?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label"><?=t('Required')?></label>
				<div class="radio"><label><?=$form->radio('requiredEdit', 1)?> <?=t('Yes')?></label></div>
				<div class="radio"><label><?=$form->radio('requiredEdit', 0)?> <?=t('No')?></label></div>
			</div>

			<div class="form-group">
				<div id="emailSettings">
					<?php print $form->label('send_notification_from_edit', t('Reply to this email address'));?>
					<span class="send_notification_from_edit"><?php print $form->checkbox('send_notification_from_edit', 1); ?></span>
				</div>
			</div>
		</fieldset>
		
		<input type="hidden" id="positionEdit" name="position" type="text" value="1000" />
		
		<div>
			<?=$ih->button(t('Cancel'), 'javascript:void(0)', 'left', '', array('id' => 'cancelEditQuestion'))?>
			<?=$ih->button(t('Save Changes'), 'javascript:void(0)', 'right', 'primary', array('id' => 'editQuestion'))?>
		</div>
	</div>

	<div id="miniSurvey">
		<fieldset>
			<legend><?=t('Edit Survey')?></legend>
			<div id="miniSurveyWrap"></div>
		</fieldset>
	</div>
</div>	
		
<div id="ccm-tab-content-form-preview" class="ccm-tab-content">
	<fieldset>
		<legend><?=t('Preview Survey')?></legend>
		<div id="miniSurveyPreviewWrap"></div>
	</fieldset>
</div>

<style type="text/css">
	div.miniSurveyQuestion {
		float: left; 
		width: 80%;
	}
	div.miniSurveyOptions {
		float: left;
		width: 20%;
		text-align: right;
	}
</style>

<script type="text/javascript">
//safari was loading the auto.js too late. This ensures it's initialized
function initFormBlockWhenReady(){
	if(miniSurvey && typeof(miniSurvey.init)=='function'){
		miniSurvey.cID=parseInt(<?php echo $c->getCollectionID()?>);
		miniSurvey.arHandle="<?php echo $a->getAreaHandle()?>";
		miniSurvey.bID=thisbID;
		miniSurvey.btID=thisbtID;
		miniSurvey.qsID=parseInt(<?php echo $miniSurveyInfo['questionSetId']?>);	
		miniSurvey.init();
		miniSurvey.refreshSurvey();
	}else setTimeout('initFormBlockWhenReady()',100);
}
initFormBlockWhenReady();
</script>
