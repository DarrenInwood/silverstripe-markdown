<?php

class MarkdownCloudinaryUpload extends Extension {

    /**
     * update the field holder adding new javascript
     */
    public function updateFieldHolder(){
		if(Config::inst()->get('MarkdownCloudinaryUpload', 'enable') == true){
			Requirements::javascript('markdown/javascript/MarkdownCloudinaryUpload.js');
		}
	}

}

class MarkdownCloudinaryUpload_Controller extends Controller {

	private static $allowed_actions = array(
		'ImageForm',
		'getImageTag'
	);

    /**
     * @return Form
     */
    public function ImageForm(){

		$numericLabelTmpl = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span>'
			. '<strong class="title">%s</strong></span>';
		$form = new Form(
			$this,
			"ImageForm",
			new FieldList(
				$contentComposite = new CompositeField(
					new LiteralField('Step1',
						'<div class="step1">'
						. sprintf($numericLabelTmpl, '1', _t('HtmlEditorField.SELECTIMAGE', 'Select Image')) . '</div>'
					),
					CloudinaryImageField::create('Image')->addExtraClass('markdown-popup'),
					new LiteralField('Step2',
						'<div class="step2">'
						. sprintf($numericLabelTmpl, '2', _t('HtmlEditorField.DETAILS', 'Details')) . '</div>'
					),
					NumericField::create('Width'),
					NumericField::create('Height'),
                    TextField::create('AltText')->setTitle('Alter Text')
				)
			),
			new FieldList(
				FormAction::create('insert', _t('HtmlEditorField.BUTTONINSERTIMAGE', 'Insert Image'))
					->addExtraClass('ss-ui-action-constructive')
					->setAttribute('data-icon', 'accept')
					->setUseButtonTag(true)
			)
		);

		$contentComposite->addExtraClass('ss-insert-image content ss-insert-media');
		$form->unsetValidator();
		$form->loadDataFrom($this);
		$form->addExtraClass('markdownfield-form markdowneditorfield-imageform ');
		return $form;
	}

    /**
     * get markdown image url
     *
     * @return string
     */
    public function getImageTag(){
		$strRet = '';
		if(isset($_POST['Image']) && $_POST['Image']
			&& isset($_POST['Width'])
			&& isset($_POST['Height'])
			&& isset($_POST['AltText'])
		){
            $arrImages = reset($_POST['Image']);
			$image = CloudinaryImage::get()->byID($arrImages[0]);
			if($image){
				if((int)$_POST['Width'] && (int)$_POST['Height']){
					$image = $image->FillImage((int)$_POST['Width'], (int)$_POST['Height']);
				}

				$strURL = $image->Link();

				$strRet = '!';

				if($_POST['AltText']) {
					$strRet.= '[' . $_POST['AltText'] . ']';
				} else {
					$strRet.= '[]';
				}
                $strRet.= '('.$strURL.')';

			}
		}
		return Convert::array2json(array(
			'Markdown'	=> $strRet
		));
	}
}