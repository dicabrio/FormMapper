<?php

// get the source at: http://github.com/dicabrio/FormMapper

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('request.php');
include('form.php');
include('formelement.php');
include('formhandler.php');
include('formmapper.php');
include('formelementimpl.php');
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
			$this->oMapper->constructModelsFromForm($oForm);
			echo 'done mapping';
			echo  '<pre>';
			print_r($this->oMapper->getModel('name'));
			print_r($this->oMapper->getModel('email'));
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

$request = Request::getInstance();

// create a form element
$nameElement = new TextInput('name');
$nameElement->setValue('bladiebladiebla');

// create a form element
$emailElement = new TextInput('email');
$emailElement->setValue('example@example.com');

// Creating the form
$oForm = new Form($request, $_SERVER['PHP_SELF'], Request::POST, 'testform');
$oForm->addFormElement('name', $nameElement);
$oForm->addFormElement('email', $emailElement);

// Creating the mapper
$oFormMapper = new FormMapper();
$oFormMapper->addFormElementToDomainEntityMapping('name', 'Name');
$oFormMapper->addFormElementToDomainEntityMapping('email', 'Email');

// add some action
$oForm->addSubmitButton('save', new ActionButton('Save'), new SaveHandler($oFormMapper));

// Let the form listen
$oForm->listen();

// errors when mapped. When not mapped this array will be empty
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
						<?php echo $oForm->getFormElement('name'); ?> (minimum of 3 letters)
					</td>
				</tr>
				<tr>
					<td>
						<label>Email: </label>
					</td>
					<td>
						<?php echo $oForm->getFormElement('email'); ?> (valid emailaddress)
					</td>
				</tr>
				<tr>
					<td>
						<label>Actions: </label>
					</td>
					<td>
						<?php echo $oForm->getSubmitButton('save'); ?>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>">Cancel</a>
					</td>
				</tr>
			</table>
		</fieldset>
		<?php echo $oForm->end(); ?>

		<?php $sFileContent = file_get_contents(__FILE__); ?>
		<strong>Simple implementation:</strong>
		<pre style="background: #ccc; padding: 10px;">
			<?php echo htmlentities($sFileContent); ?>
		</pre>

	</body>
</html>
