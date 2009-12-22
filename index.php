<?php

// get the source at: http://github.com/dicabrio/FormMapper

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
include('name.php');
include('email.php');

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
		$this->addFormElementToDomainEntityMapping('name', 'Name');
		$this->addFormElementToDomainEntityMapping('email', 'Email');
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

/**
 * Example form.
 */
class TestForm extends Form {

	private $aElements = array();

	/**
	 * @param Request $oReq
	 * @param array $aElements
	 */
	public function __construct(Request $oReq, $aElements=array()) {
		$this->aElements = $aElements;
		parent::__construct($oReq, $_SERVER['PHP_SELF'], Request::POST, 'testform');
	}

	protected function defineFormElements() {

		foreach ($this->aElements as $sIdentifier => $oFormElement) {
			parent::addFormElement($oFormElement->getName(), $oFormElement);
		}
	}

}

class OverViewForm extends Form {
	private $elements = array();
	public function __construct(Request $oReq, $elements=array()) {
		$this->elements = $elements;
		parent::__construct($oReq, $_SERVER['PHP_SELF'], Request::POST, 'overviewform');
	}
	protected function defineFormElements() {
		foreach ($this->elements as $formElement) {
			$this->addFormElement($formElement->getName(), $formElement);
		}
	}
}

$blaatGegevens = array(	array('id' => 1, 'title' => 'Boe', 'iets' => 'WOW'),
						array('id' => 2, 'title' => 'Schrik', 'iets' => 'Brrrrr'));

$overviewFields = array();
foreach ($blaatGegevens as $blaat) {
	$element = new Input('checkbox', 'select_'.$blaat['id']);
}

$request = Request::getInstance();
$overviewForm = new OverViewForm($request, array());

$nameElement = new TextInput('name');
$nameElement->setValue('bladiebladiebla');

$emailElement = new TextInput('email');
$emailElement->setValue('example@example.com');

$oForm = new TestForm($request, array('name' => $nameElement, 'email' => $emailElement));

$oFormMapper = new TestMapper($oForm);
$oForm->addSubmitButton('save', new ActionButton('Save'), new SaveHandler($oFormMapper));
$oForm->addSubmitButton('cancel', new ActionButton('Cancel'), new CancelHandler());

$oForm->listen();

$aErrors = $oFormMapper->getMappingErrors();

?>
<html>
	<head>
		<title>FormMapper example</title>
	</head>
	<body>

		<?php if (count($aErrors) > 0) : ?>
		<ul style="color: #fff; background: red;">
				<?php foreach ($aErrors as $sError) : ?>
			<li><?php echo $sError; ?></li>
				<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<?php echo $oForm->begin(); ?>
		<fieldset>
			<legend>User</legend>
			<table>
				<tr>
					<td>
						<label>Name: </label>
					</td>
					<td>
						<?php echo $oForm->getFormElement('name'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<label>Email: </label>
					</td>
					<td>
						<?php echo $oForm->getFormElement('email'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<label>Actions: </label>
					</td>
					<td>
						<?php echo $oForm->getSubmitButton('save'); ?>
						<a href="javascript:history.go(-1);">Cancel</a>
					</td>
				</tr>
			</table>
		</fieldset>
		<?php echo $oForm->end(); ?>

		<?php $overviewErrors  = array(); ?>
		<?php if (count($overviewErrors) > 0) : ?>
		<ul style="color: #fff; background: red;">
				<?php foreach ($overviewErrors as $sError) : ?>
			<li><?php echo $sError; ?></li>
				<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<?php echo $overviewForm->begin(); ?>
		<fieldset>
			<legend>Users</legend>
			<table>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Title</th>
						<th>Something</th>
						<th>Acties</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php echo $overviewForm->end(); ?>


		<?php $sFileContent = file_get_contents(__FILE__); ?>
		<strong>Simple implementation:</strong>
		<pre style="background: #ccc; padding: 10px;">
			<?php echo htmlentities($sFileContent); ?>
		</pre>

	</body>
</html>
