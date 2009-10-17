<?php

include('request.class.php');
include('form.php');
//include('')

class TextInputFormElement implements FormElement {
	private $sName;
	private $sValue;
	public function __construct($sName) {
		$this->sName = $sName;
	}

	public function setValue($sValue) {
		$this->sValue = $sValue;
	}

	public function __toString() {
		return '<input type="text" name="'.$this->sName.'" value="'.$this->sValue.'" />';
	}
}

class TestForm extends Form {

	public function __construct(Request $oReq) {
		parent::__construct($oReq, $_SERVER['PHP_SELF'], Request::POST, 'testform');
	}

	protected function defineFormElements() {
		parent::addFormElement(new TextInputFormElement('test'));
	}

}

$oForm = new TestForm(Request::getInstance());

?>
<html>
	<head>
		<title>FormMapper example</title>
	</head>
	<body>

		<form id="<?php echo $oForm->getIdentifier(); ?>" method="<?php echo $oForm->getMethod(); ?>" action="<?php echo $oForm->getAction(); ?>">
			<table>
				<tr>
					<td>
						<label>test</label>
						<?php echo $oForm->getFormElement('test'); ?>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>