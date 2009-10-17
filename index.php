<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


include('request.class.php');
include('form.php');
include('formelement.php');
include('formmapper.php');
include('domainentity.php');
include('domaintext.php');

//include('')

class Input implements FormElement {
	private $sType;
	private $sName;
	private $sValue;
	public function __construct($sType, $sName) {
		$this->sName = $sName;
		$this->sType = $sType;
	}

	public function setValue($sValue) {
		$this->sValue = $sValue;
	}

	public function getValue() {
		return $this->sValue;
	}

	public function getName() {
		return $this->sName;
	}

	public function __toString() {
		return '<input type="'.$this->sType.'" name="'.$this->sName.'" value="'.$this->sValue.'" />';
	}
}

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
interface FormHandler {
	public function handleForm(Form $oForm);
}
class JemoederHandler implements FormHandler {
	public function __construct() {

	}

	public function handleForm(Form $oForm) {
		echo 'Jemoeder->handleForm called for form: '.$oForm->getIdentifier();
	}
}

class JevaderHandler implements FormHandler {
	public function __construct() {

	}

	public function handleForm(Form $oForm) {
		echo 'Jevader->handleForm called for form: '.$oForm->getIdentifier();
	}
}

class TestForm extends Form {

	public function __construct(Request $oReq) {
		parent::__construct($oReq, $_SERVER['PHP_SELF'], Request::POST, 'testform');
	}

	protected function defineFormElements() {
		parent::addFormElement(new TextInput('test'));

		parent::addSubmitButton('save', new ActionButton('jemoeder'), new JemoederHandler());
		parent::addSubmitButton('cancel', new ActionButton('jevader'), new JevaderHandler());
	}

}

class TestFormMapper extends FormMapper {

	protected function defineFormElementToDomainEntityMapping() {
		parent::addFormElementToDomainEntityMapping('test', 'DomainText');
	}
}

$oForm = new TestForm(Request::getInstance());
$oForm->listen();

//
//if ($oForm->isSubmitted()) {
//	$oMapper = new TestFormMapper($oForm);
//	$oMapper->buildModels();
//}

?>
<html>
	<head>
		<title>FormMapper example</title>
	</head>
	<body>
		<?php echo $oForm->begin(); ?>
		<form id="<?php echo $oForm->getIdentifier(); ?>" method="<?php echo $oForm->getMethod(); ?>" action="<?php echo $oForm->getAction(); ?>">
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
		</form>
	</body>
</html>