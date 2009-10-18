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
include('shorttext.php');

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

class TestMapper extends FormMapper {

	protected function defineFormElementToDomainEntityMapping() {
		$this->addFormElementToDomainEntityMapping('test', 'ShortText');
	}

}

class SaveHandler implements FormHandler {

	/**
	 * @var FormMapper
	 */
	private $oMapper;

	public function __construct(FormMapper $oMapper) {
		$this->oMapper = $oMapper;
	}

	public function handleForm(Form $oForm) {

		try {
			$this->oMapper->constructModelsFromForm();
			echo 'done mapping';
			echo  '<pre>';
			print_r($this->oMapper->getModel('test'));
			echo '</pre>';

		} catch (FormMapperException $e) {
			// error when mapping
			echo  '<pre>';
			print_r($this->oMapper->getMappingErrors());
			echo '</pre>';
		}

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
	 * @param Request $oReq
	 * @param FormHandler $oSaveHandler
	 * @param FormHandler $oCancelHandler
	 */
	public function __construct(Request $oReq) {
		parent::__construct($oReq, $_SERVER['PHP_SELF'], Request::POST, 'testform');
	}

	protected function defineFormElements() {
		parent::addFormElement(new TextInput('test'));
	}

}


$oForm = new TestForm(Request::getInstance());

$oFormMapper = new TestMapper($oForm);
$oForm->addSubmitButton('save', new ActionButton('Save'), new SaveHandler($oFormMapper));
$oForm->addSubmitButton('cancel', new ActionButton('Cancel'), new CancelHandler());

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

		<?php $sFileContent = file_get_contents(__FILE__); ?>
<strong>Simple implementation:</strong>
<pre style="background: #ccc; padding: 10px;">
<?php echo htmlentities($sFileContent); ?>
</pre>

	</body>
</html>
