<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('request.php');
include('form.php');
include('formelement.php');
include('formhandler.php');
include('formmapper.php');
include('domainentity.php');
include('domaintext.php');
include('input.php');

class Button extends Input {
	public function __construct($sName, $sValue) {
		parent::__construct('submit', $sName);
		parent::setValue($sValue);
	}
}

class ActionButton extends Button {
	public function  __construct($sValue) {
		parent::__construct('action', $sValue);
	}
}

class TextInput extends Input {
	public function __construct($sName) {
		parent::__construct('text', $sName);
	}
}

class SaveHandler implements FormHandler {
	public function __construct() {

	}

	public function handleForm(Form $oForm) {
		echo 'Save->handleForm called for form: '.$oForm->getIdentifier();
	}
}

class CancelHandler implements FormHandler {
	public function __construct() {

	}

	public function handleForm(Form $oForm) {
		echo 'Cancel->handleForm called for form: '.$oForm->getIdentifier();
	}
}

class TestForm extends Form {

	/**
	 * @var FormHandler
	 */
	private $oHandlerSave;

	/**
	 * @var FormHandler
	 */
	private $oHandlerCancel;

	/**
	 * @param Request $oReq
	 * @param FormHandler $oSaveHandler
	 * @param FormHandler $oCancelHandler
	 */
	public function __construct(Request $oReq, FormHandler $oSaveHandler, FormHandler $oCancelHandler) {

		$this->oHandlerSave = $oSaveHandler;
		$this->oHandlerCancel = $oCancelHandler;

		parent::__construct($oReq, $_SERVER['PHP_SELF'], Request::POST, 'testform');


	}

	protected function defineFormElements() {
		parent::addFormElement(new TextInput('test'));

		parent::addSubmitButton('save', new ActionButton('Save'), $this->oHandlerSave);
		parent::addSubmitButton('cancel', new ActionButton('Cancel'), $this->oHandlerCancel);
	}

}

//class TestFormMapper extends FormMapper {
//
//	protected function defineFormElementToDomainEntityMapping() {
//		parent::addFormElementToDomainEntityMapping('test', 'DomainText');
//	}
//}
$oForm = new TestForm(Request::getInstance(), new SaveHandler(), new CancelHandler());
$oForm->listen();


?>
<html>
	<head>
		<title>FormMapper example</title>
	</head>
	<body>
		<?php echo $oForm->begin(); ?>
			<table>
				<tr>
					<td>
						<label>test: </label>
						<?php echo $oForm->getFormElement('test'); ?>
						<?php echo $oForm->getSubmitButton('save'); ?>
						<?php echo $oForm->getSubmitButton('cancel'); ?>
					</td>
				</tr>
			</table>
		<?php echo $oForm->end(); ?>
	</body>
</html>