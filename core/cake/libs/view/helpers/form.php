<?php
/**
 * Automatic generation of HTML FORMs from given data.
 *
 * Used for scaffolding.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link        http://cakephp.org CakePHP(tm) Project
 * @package     cake.libs.view.helpers
 * @since       CakePHP(tm) v 0.10.0.1076
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Form helper library.
 *
 * Automatic generation of HTML FORMs from given data.
 *
 * @package     cake.libs.view.helpers
 * @link http://book.cakephp.org/view/1383/Form
 */
class FormHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 * @access public
 */
	public $helpers = array('Html');

/**
 * Holds the fields array('field_name' => array('type'=> 'string', 'length'=> 100),
 * primaryKey and validates array('field_name')
 *
 * @access public
 */
	public $fieldset = array();

/**
 * Options used by DateTime fields
 *
 * @var array
 */
	private $__options = array(
		'day' => array(), 'minute' => array(), 'hour' => array(),
		'month' => array(), 'year' => array(), 'meridian' => array()
	);

/**
 * List of fields created, used with secure forms.
 *
 * @var array
 * @access public
 */
	public $fields = array();

/**
 * Defines the type of form being created.  Set by FormHelper::create().
 *
 * @var string
 * @access public
 */
	public $requestType = null;

/**
 * The default model being used for the current form.
 *
 * @var string
 * @access public
 */
	public $defaultModel = null;


/**
 * Persistent default options used by input(). Set by FormHelper::create().
 *
 * @var array
 * @access protected
 */
	protected $_inputDefaults = array();

/**
 * Introspects model information and extracts information related
 * to validation, field length and field type. Appends information into
 * $this->fieldset.
 *
 * @return Model Returns a model instance
 */
	protected function &_introspectModel($model) {
		$object = null;
		if (is_string($model) && strpos($model, '.') !== false) {
			$path = explode('.', $model);
			$model = end($path);
		}

		if (ClassRegistry::isKeySet($model)) {
			$object = ClassRegistry::getObject($model);
		}

		if (!empty($object)) {
			$fields = $object->schema();
			foreach ($fields as $key => $value) {
				unset($fields[$key]);
				$fields[$key] = $value;
			}

			if (!empty($object->hasAndBelongsToMany)) {
				foreach ($object->hasAndBelongsToMany as $alias => $assocData) {
					$fields[$alias] = array('type' => 'multiple');
				}
			}
			$validates = array();
			if (!empty($object->validate)) {
				foreach ($object->validate as $validateField => $validateProperties) {
					if ($this->_isRequiredField($validateProperties)) {
						$validates[] = $validateField;
					}
				}
			}
			$defaults = array('fields' => array(), 'key' => 'id', 'validates' => array());
			$key = $object->primaryKey;
			$this->fieldset[$model] = array_merge($defaults, compact('fields', 'key', 'validates'));
		}

		return $object;
	}

/**
 * Returns if a field is required to be filled based on validation properties from the validating object
 *
 * @return boolean true if field is required to be filled, false otherwise
 */
	protected function _isRequiredField($validateProperties) {
		$required = false;
		if (is_array($validateProperties)) {

			$dims = Set::countDim($validateProperties);
			if ($dims == 1 || ($dims == 2 && isset($validateProperties['rule']))) {
				$validateProperties = array($validateProperties);
			}

			foreach ($validateProperties as $rule => $validateProp) {
				if (isset($validateProp['allowEmpty']) && $validateProp['allowEmpty'] === true) {
					return false;
				}
				$rule = isset($validateProp['rule']) ? $validateProp['rule'] : false;
				$required = $rule || empty($validateProp);
				if ($required) {
					break;
				}
			}
		}
		return $required;
	}

/**
 * Returns an HTML FORM element.
 *
 * ### Options:
 *
 * - `type` Form method defaults to POST
 * - `action`  The controller action the form submits to, (optional).
 * - `url`  The url the form submits to. Can be a string or a url array,
 * - `default`  Allows for the creation of Ajax forms.
 * - `onsubmit` Used in conjunction with 'default' to create ajax forms.
 * - `inputDefaults` set the default $options for FormHelper::input(). Any options that would
 *	be set when using FormHelper::input() can be set here.  Options set with `inputDefaults`
 *	can be overridden when calling input()
 * - `encoding` Set the accept-charset encoding for the form.  Defaults to `Configure::read('App.encoding')`
 *
 * @access public
 * @param string $model The model object which the form is being defined for
 * @param array $options An array of html attributes and options.
 * @return string An formatted opening FORM tag.
 * @link http://book.cakephp.org/view/1384/Creating-Forms
 */
	function create($model = null, $options = array()) {
		$created = $id = false;
		$append = '';

		if (is_array($model) && empty($options)) {
			$options = $model;
			$model = null;
		}
		if (empty($model) && $model !== false && !empty($this->request['models'])) {
			$model = $this->request['models'][0];
			$this->defaultModel = $this->request['models'][0];
		} elseif (empty($model) && empty($this->request['models'])) {
			$model = false;
		}

		$models = ClassRegistry::keys();
		foreach ($models as $currentModel) {
			if (ClassRegistry::isKeySet($currentModel)) {
				$currentObject = ClassRegistry::getObject($currentModel);
				if ($currentObject instanceof Model && !empty($currentObject->validationErrors)) {
					$this->validationErrors[Inflector::camelize($currentModel)] = $currentObject->validationErrors;
				}
			}
		}

		if ($model !== false) {
			$object = $this->_introspectModel($model);
			$this->setEntity($model . '.', true);
		}

		$modelEntity = $this->model();
		if ($model !== false && isset($this->fieldset[$modelEntity]['key'])) {
			$data = $this->fieldset[$modelEntity];
			$recordExists = (
				isset($this->request->data[$model]) &&
				!empty($this->request->data[$model][$data['key']]) &&
				!is_array($this->request->data[$model][$data['key']])
			);

			if ($recordExists) {
				$created = true;
				$id = $this->request->data[$model][$data['key']];
			}
		}

		$options = array_merge(array(
			'type' => ($created && empty($options['action'])) ? 'put' : 'post',
			'action' => null,
			'url' => null,
			'default' => true,
			'encoding' => strtolower(Configure::read('App.encoding')),
			'inputDefaults' => array()),
		$options);
		$this->_inputDefaults = $options['inputDefaults'];
		unset($options['inputDefaults']);

		if (empty($options['url']) || is_array($options['url'])) {
			if (empty($options['url']['controller'])) {
				if (!empty($model) && $model != $this->defaultModel) {
					$options['url']['controller'] = Inflector::underscore(Inflector::pluralize($model));
				} elseif (!empty($this->request->params['controller'])) {
					$options['url']['controller'] = Inflector::underscore($this->request->params['controller']);
				}
			}
			if (empty($options['action'])) {
// Was			$options['action'] = $this->request->params['action'];
				$options['action'] = ($created) ? 'edit' : 'add';;
// <!--
			}

			$actionDefaults = array(
				'plugin' => $this->plugin,
				'controller' => $this->_View->viewPath,
				'action' => $options['action'],
			);
			if (!empty($options['action']) && !isset($options['id'])) {
				$options['id'] = $this->domId($options['action'] . 'Form');
			}
			$options['action'] = array_merge($actionDefaults, (array)$options['url']);
// Was		if (empty($options['action'][0])) {
			if (empty($options['action'][0]) && !empty($id)) {
// <!--
				$options['action'][0] = $id;
			}
		} elseif (is_string($options['url'])) {
			$options['action'] = $options['url'];
		}
		unset($options['url']);

		switch (strtolower($options['type'])) {
			case 'get':
				$htmlAttributes['method'] = 'get';
			break;
			case 'file':
				$htmlAttributes['enctype'] = 'multipart/form-data';
				$options['type'] = ($created) ? 'put' : 'post';
			case 'post':
			case 'put':
			case 'delete':
				$append .= $this->hidden('_method', array(
					'name' => '_method', 'value' => strtoupper($options['type']), 'id' => null
				));
			default:
				$htmlAttributes['method'] = 'post';
			break;
		}
		$this->requestType = strtolower($options['type']);

		$htmlAttributes['action'] = $this->url($options['action']);
		unset($options['type'], $options['action']);

		if ($options['default'] == false) {
			if (isset($htmlAttributes['onSubmit']) || isset($htmlAttributes['onsubmit'])) {
				$htmlAttributes['onsubmit'] .= ' event.returnValue = false; return false;';
			} else {
				$htmlAttributes['onsubmit'] = 'event.returnValue = false; return false;';
			}
		}

		if (!empty($options['encoding'])) {
			$htmlAttributes['accept-charset'] = $options['encoding'];
			unset($options['encoding']);
		}

		unset($options['default']);
		$htmlAttributes = array_merge($options, $htmlAttributes);

		$this->fields = array();
		if (isset($this->request->params['_Token']) && !empty($this->request->params['_Token'])) {
			$append .= $this->hidden('_Token.key', array(
				'value' => $this->request->params['_Token']['key'], 'id' => 'Token' . mt_rand())
			);
		}

		if (!empty($append)) {
			$append = sprintf($this->Html->tags['block'], ' style="display:none;"', $append);
		}

		$this->setEntity($model . '.', true);
		$attributes = $this->_parseAttributes($htmlAttributes, null, '');
		return sprintf($this->Html->tags['form'], $attributes) . $append;
	}

/**
 * Closes an HTML form, cleans up values set by FormHelper::create(), and writes hidden
 * input fields where appropriate.
 *
 * If $options is set a form submit button will be created. Options can be either a string or an array.
 *
 * {{{
 * array usage:
 *
 * array('label' => 'save'); value="save"
 * array('label' => 'save', 'name' => 'Whatever'); value="save" name="Whatever"
 * array('name' => 'Whatever'); value="Submit" name="Whatever"
 * array('label' => 'save', 'name' => 'Whatever', 'div' => 'good') <div class="good"> value="save" name="Whatever"
 * array('label' => 'save', 'name' => 'Whatever', 'div' => array('class' => 'good')); <div class="good"> value="save" name="Whatever"
 * }}}
 *
 * @param mixed $options as a string will use $options as the value of button,
 * @return string a closing FORM tag optional submit button.
 * @access public
 * @link http://book.cakephp.org/view/1389/Closing-the-Form
 */
	public function end($options = null) {
		if (!empty($this->request['models'])) {
			$models = $this->request['models'][0];
		}
		$out = null;
		$submit = null;

		if ($options !== null) {
			$submitOptions = array();
			if (is_string($options)) {
				$submit = $options;
			} else {
				if (isset($options['label'])) {
					$submit = $options['label'];
					unset($options['label']);
				}
				$submitOptions = $options;

				if (!$submit) {
					$submit = __('Submit');
				}
			}
			$out .= $this->submit($submit, $submitOptions);
		}
		if (isset($this->request['_Token']) && !empty($this->request['_Token'])) {
			$out .= $this->secure($this->fields);
			$this->fields = array();
		}
		$this->setEntity(null);
		$out .= $this->Html->tags['formend'];

		$this->_View->modelScope = false;
		return $out;
	}

/**
 * Generates a hidden field with a security hash based on the fields used in the form.
 *
 * @param array $fields The list of fields to use when generating the hash
 * @return string A hidden input field with a security hash
 */
	public function secure($fields = array()) {
		if (!isset($this->request['_Token']) || empty($this->request['_Token'])) {
			return;
		}
		$locked = array();

		foreach ($fields as $key => $value) {
			if (!is_int($key)) {
				$locked[$key] = $value;
				unset($fields[$key]);
			}
		}
		sort($fields, SORT_STRING);
		ksort($locked, SORT_STRING);
		$fields += $locked;
		//pr($fields);
		$fields = Security::hash(serialize($fields) . Configure::read('Security.salt'));
		$locked = implode(array_keys($locked), '|');

		$out = $this->hidden('_Token.fields', array(
			'value' => urlencode($fields . ':' . $locked),
			'id' => 'TokenFields' . mt_rand()
		));
		$out = sprintf($this->Html->tags['block'], ' style="display:none;"', $out);
		return $out;
	}

/**
 * Determine which fields of a form should be used for hash.
 * Populates $this->fields
 *
 * @param mixed $field Reference to field to be secured
 * @param mixed $value Field value, if value should not be tampered with.
 * @return void
 * @access private
 */
	function __secure($field = null, $value = null) {
		if (!$field) {
			$field = $this->_View->entity();
		} elseif (is_string($field)) {
			$field = Set::filter(explode('.', $field), true);
		}

		if (!empty($this->request['_Token']['disabledFields'])) {
			foreach ((array)$this->request['_Token']['disabledFields'] as $disabled) {
				$disabled = explode('.', $disabled);
				if (array_values(array_intersect($field, $disabled)) === $disabled) {
					return;
				}
			}
		}
		$field = implode('.', $field);
		if (!in_array($field, $this->fields)) {
			if ($value !== null) {
				return $this->fields[$field] = $value;
			}
			$this->fields[] = $field;
		}
	}

/**
 * Returns true if there is an error for the given field, otherwise false
 *
 * @param string $field This should be "Modelname.fieldname"
 * @return boolean If there are errors this method returns true, else false.
 * @access public
 * @link http://book.cakephp.org/view/1426/isFieldError
 */
	public function isFieldError($field) {
		$this->setEntity($field);
		return (bool)$this->tagIsInvalid();
	}

/**
 * Returns a formatted error message for given FORM field, NULL if no errors.
 *
 * ### Options:
 *
 * - `escape`  bool  Whether or not to html escape the contents of the error.
 * - `wrap`  mixed  Whether or not the error message should be wrapped in a div. If a
 *   string, will be used as the HTML tag to use.
 * - `class` string  The classname for the error message
 *
 * @param string $field A field name, like "Modelname.fieldname"
 * @param mixed $text Error message or array of $options. If array, `attributes` key
 * will get used as html attributes for error container
 * @param array $options Rendering options for <div /> wrapper tag
 * @return string If there are errors this method returns an error message, otherwise null.
 * @access public
 * @link http://book.cakephp.org/view/1423/error
 */
	public function error($field, $text = null, $options = array()) {
		$defaults = array('wrap' => true, 'class' => 'error-message', 'escape' => true);
		$options = array_merge($defaults, $options);
		$this->setEntity($field);

		if ($error = $this->tagIsInvalid()) {
			if (is_array($error)) {
				list(,,$field) = explode('.', $field);
				if (isset($error[$field])) {
					$error = $error[$field];
				} else {
					return null;
				}
			}

			if (is_array($text) && is_numeric($error) && $error > 0) {
				$error--;
			}
			if (is_array($text)) {
				$options = array_merge($options, array_intersect_key($text, $defaults));
				if (isset($text['attributes']) && is_array($text['attributes'])) {
					$options = array_merge($options, $text['attributes']);
				}
				$text = isset($text[$error]) ? $text[$error] : null;
				unset($options[$error]);
			}

			if ($text != null) {
				$error = $text;
			} elseif (is_numeric($error)) {
				$error = __('Error in field %s', Inflector::humanize($this->field()));
			}
			if ($options['escape']) {
				$error = h($error);
				unset($options['escape']);
			}
			if ($options['wrap']) {
				$tag = is_string($options['wrap']) ? $options['wrap'] : 'div';
				unset($options['wrap']);
				return $this->Html->tag($tag, $error, $options);
			} else {
				return $error;
			}
		} else {
			return null;
		}
	}

/**
 * Returns a formatted LABEL element for HTML FORMs. Will automatically generate
 * a for attribute if one is not provided.
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param string $text Text that will appear in the label field.
 * @param mixed $options An array of HTML attributes, or a string, to be used as a class name.
 * @return string The formatted LABEL element
 * @link http://book.cakephp.org/view/1427/label
 */
	function label($fieldName = null, $text = null, $options = array()) {
		if (empty($fieldName)) {
			$fieldName = implode('.', $this->_View->entity());
		}

		if ($text === null) {
			if (strpos($fieldName, '.') !== false) {
				$fieldElements = explode('.', $fieldName);
				$text = array_pop($fieldElements);
			} else {
				$text = $fieldName;
			}
			if (substr($text, -3) == '_id') {
				$text = substr($text, 0, strlen($text) - 3);
			}
			$text = __(Inflector::humanize(Inflector::underscore($text)));
		}

		if (is_string($options)) {
			$options = array('class' => $options);
		}

		if (isset($options['for'])) {
			$labelFor = $options['for'];
			unset($options['for']);
		} else {
			$labelFor = $this->domId($fieldName);
		}

		return sprintf(
			$this->Html->tags['label'],
			$labelFor,
			$this->_parseAttributes($options), $text
		);
	}

/**
 * Generate a set of inputs for `$fields`.  If $fields is null the current model
 * will be used.
 *
 * In addition to controller fields output, `$fields` can be used to control legend
 * and fieldset rendering with the `fieldset` and `legend` keys.
 * `$form->inputs(array('legend' => 'My legend'));` Would generate an input set with
 * a custom legend.  You can customize individual inputs through `$fields` as well.
 *
 * {{{
 *	$form->inputs(array(
 *		'name' => array('label' => 'custom label')
 *	));
 * }}}
 *
 * In addition to fields control, inputs() allows you to use a few additional options.
 *
 * - `fieldset` Set to false to disable the fieldset. If a string is supplied it will be used as
 *    the classname for the fieldset element.
 * - `legend` Set to false to disable the legend for the generated input set. Or supply a string
 *	to customize the legend text.
 *
 * @param mixed $fields An array of fields to generate inputs for, or null.
 * @param array $blacklist a simple array of fields to not create inputs for.
 * @return string Completed form inputs.
 */
	public function inputs($fields = null, $blacklist = null) {
		$fieldset = $legend = true;
		$model = $this->model();
		if (is_array($fields)) {
			if (array_key_exists('legend', $fields)) {
				$legend = $fields['legend'];
				unset($fields['legend']);
			}

			if (isset($fields['fieldset'])) {
				$fieldset = $fields['fieldset'];
				unset($fields['fieldset']);
			}
		} elseif ($fields !== null) {
			$fieldset = $legend = $fields;
			if (!is_bool($fieldset)) {
				$fieldset = true;
			}
			$fields = array();
		}

		if (empty($fields)) {
			$fields = array_keys($this->fieldset[$model]['fields']);
		}

		if ($legend === true) {
			$actionName = __('New %s');
			$isEdit = (
				strpos($this->request->params['action'], 'update') !== false ||
				strpos($this->request->params['action'], 'edit') !== false
			);
			if ($isEdit) {
				$actionName = __('Edit %s');
			}
			$modelName = Inflector::humanize(Inflector::underscore($model));
			$legend = sprintf($actionName, __($modelName));
		}

		$out = null;
		foreach ($fields as $name => $options) {
			if (is_numeric($name) && !is_array($options)) {
				$name = $options;
				$options = array();
			}
			$entity = explode('.', $name);
			$blacklisted = (
				is_array($blacklist) &&
				(in_array($name, $blacklist) || in_array(end($entity), $blacklist))
			);
			if ($blacklisted) {
				continue;
			}
			$out .= $this->input($name, $options);
		}

		if (is_string($fieldset)) {
			$fieldsetClass = sprintf(' class="%s"', $fieldset);
		} else {
			$fieldsetClass = '';
		}

		if ($fieldset && $legend) {
			return sprintf(
				$this->Html->tags['fieldset'],
				$fieldsetClass,
				sprintf($this->Html->tags['legend'], $legend) . $out
			);
		} elseif ($fieldset) {
			return sprintf($this->Html->tags['fieldset'], $fieldsetClass, $out);
		} else {
			return $out;
		}
	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * ### Options
 *
 * See each field type method for more information. Any options that are part of
 * $attributes or $options for the different **type** methods can be included in `$options` for input().
 *
 * - `type` - Force the type of widget you want. e.g. `type => 'select'`
 * - `label` - Either a string label, or an array of options for the label. See FormHelper::label()
 * - `div` - Either `false` to disable the div, or an array of options for the div.
 *	See HtmlHelper::div() for more options.
 * - `options` - for widgets that take options e.g. radio, select
 * - `error` - control the error message that is produced
 * - `empty` - String or boolean to enable empty select box options.
 * - `before` - Content to place before the label + input.
 * - `after` - Content to place after the label + input.
 * - `between` - Content to place between the label + input.
 * - `format` - format template for element order. Any element that is not in the array, will not be in the output.
 *	- Default input format order: array('before', 'label', 'between', 'input', 'after', 'error')
 *	- Default checkbox format order: array('before', 'input', 'between', 'label', 'after', 'error')
 *	- Hidden input will not be formatted
 *	- Radio buttons cannot have the order of input and label elements controlled with these settings.
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $options Each type of input takes different options.
 * @return string Completed form widget.
 * @access public
 * @link http://book.cakephp.org/view/1390/Automagic-Form-Elements
 */
	public function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);

		$options = array_merge(
// Siva: info & help
			array('before' => null, 'between' => null, 'after' => null, 'format' => null, 'info' => null, 'help' => null),
			$this->_inputDefaults,
			$options
		);

		$modelKey = $this->model();
		$fieldKey = $this->field();
		if (!isset($this->fieldset[$modelKey])) {
			$this->_introspectModel($modelKey);
		}

		if (!isset($options['type'])) {
			$magicType = true;
			$options['type'] = 'text';
			if (isset($options['options'])) {
				$options['type'] = 'select';
			} elseif (in_array($fieldKey, array('psword', 'passwd', 'password'))) {
				$options['type'] = 'password';
			} elseif (isset($options['checked'])) {
				$options['type'] = 'checkbox';
			} elseif (isset($this->fieldset[$modelKey]['fields'][$fieldKey])) {
				$fieldDef = $this->fieldset[$modelKey]['fields'][$fieldKey];
				$type = $fieldDef['type'];
				$primaryKey = $this->fieldset[$modelKey]['key'];
			}

			if (isset($type)) {
				$map = array(
					'string'  => 'text',	 'datetime'  => 'datetime',
					'boolean' => 'checkbox', 'timestamp' => 'datetime',
					'text'	=> 'textarea', 'time'	  => 'time',
					'date'	=> 'date',	 'float'	 => 'text'
				);

				if (isset($this->map[$type])) {
					$options['type'] = $this->map[$type];
				} elseif (isset($map[$type])) {
					$options['type'] = $map[$type];
				}
				if ($fieldKey == $primaryKey) {
					$options['type'] = 'hidden';
				}
			}
			if (preg_match('/_id$/', $fieldKey) && $options['type'] !== 'hidden') {
				$options['type'] = 'select';
			}

			if ($modelKey === $fieldKey) {
				$options['type'] = 'select';
				if (!isset($options['multiple'])) {
					$options['multiple'] = 'multiple';
				}
			}
		}
		$types = array('checkbox', 'radio', 'select');

		if (
			(!isset($options['options']) && in_array($options['type'], $types)) ||
			(isset($magicType) && $options['type'] == 'text')
		) {
			$varName = Inflector::variable(
				Inflector::pluralize(preg_replace('/_id$/', '', $fieldKey))
			);
			$varOptions = $this->_View->getVar($varName);
			if (is_array($varOptions)) {
				if ($options['type'] !== 'radio') {
					$options['type'] = 'select';
				}
				$options['options'] = $varOptions;
			}
		}

		$autoLength = (!array_key_exists('maxlength', $options) && isset($fieldDef['length']));
		if ($autoLength && $options['type'] == 'text') {
			$options['maxlength'] = $fieldDef['length'];
		}
		if ($autoLength && $fieldDef['type'] == 'float') {
			$options['maxlength'] = array_sum(explode(',', $fieldDef['length']))+1;
		}

		$divOptions = array();
		$div = $this->_extractOption('div', $options, true);
		unset($options['div']);

		if (!empty($div)) {
			$divOptions['class'] = 'input';
			$divOptions = $this->addClass($divOptions, $options['type']);
			if (is_string($div)) {
				$divOptions['class'] = $div;
			} elseif (is_array($div)) {
				$divOptions = array_merge($divOptions, $div);
			}
			if (
				isset($this->fieldset[$modelKey]) &&
				in_array($fieldKey, $this->fieldset[$modelKey]['validates'])
			) {
				$divOptions = $this->addClass($divOptions, 'required');
			}
			if (!isset($divOptions['tag'])) {
				$divOptions['tag'] = 'div';
			}
		}

		$label = null;
		if (isset($options['label']) && $options['type'] !== 'radio') {
			$label = $options['label'];
			unset($options['label']);
		}

		if ($options['type'] === 'radio') {
			$label = false;
			if (isset($options['options'])) {
				$radioOptions = (array)$options['options'];
				unset($options['options']);
			}
		}

		if ($label !== false) {
			$label = $this->_inputLabel($fieldName, $label, $options);
		}

		$error = $this->_extractOption('error', $options, null);
		unset($options['error']);

		$selected = $this->_extractOption('selected', $options, null);
		unset($options['selected']);

		if (isset($options['rows']) || isset($options['cols'])) {
			$options['type'] = 'textarea';
		}

		if ($options['type'] === 'datetime' || $options['type'] === 'date' || $options['type'] === 'time' || $options['type'] === 'select') {
			$options += array('empty' => false);
		}
		if ($options['type'] === 'datetime' || $options['type'] === 'date' || $options['type'] === 'time') {
			$dateFormat = $this->_extractOption('dateFormat', $options, 'MDY');
			$timeFormat = $this->_extractOption('timeFormat', $options, 12);
			unset($options['dateFormat'], $options['timeFormat']);
		}

		$type = $options['type'];
		$out = array_merge(
			array('before' => null, 'label' => null, 'between' => null, 'input' => null, 'after' => null, 'error' => null),
			array('before' => $options['before'], 'label' => $label, 'between' => $options['between'], 'after' => $options['after'])
		);
		$format = null;
		if (is_array($options['format']) && in_array('input', $options['format'])) {
			$format = $options['format'];
		}
// Siva: info & help
		$info	 = $options['info'];
		$help	 = $options['help'];
		unset($options['type'], $options['before'], $options['between'], $options['after'], $options['format'], $options['info'], $options['help']);
// <--
		switch ($type) {
			case 'hidden':
				$input = $this->hidden($fieldName, $options);
				$format = array('input');
				unset($divOptions);
			break;
			case 'checkbox':
				$input = $this->checkbox($fieldName, $options);
				$format = $format ? $format : array('before', 'input', 'between', 'label', 'after', 'error');
			break;
			case 'radio':
				$input = $this->radio($fieldName, $radioOptions, $options);
			break;
			case 'file':
				$input = $this->file($fieldName, $options);
			break;
			case 'select':
				$options += array('options' => array(), 'value' => $selected);
				$list = $options['options'];
				unset($options['options']);
				$input = $this->select($fieldName, $list, $options);
			break;
			case 'time':
				$options['value'] = $selected;
				$input = $this->dateTime($fieldName, null, $timeFormat, $options);
			break;
			case 'date':
				$options['value'] = $selected;
				$input = $this->dateTime($fieldName, $dateFormat, null, $options);
			break;
			case 'datetime':
				$options['value'] = $selected;
				$input = $this->dateTime($fieldName, $dateFormat, $timeFormat, $options);
			break;
			case 'textarea':
				$input = $this->textarea($fieldName, $options + array('cols' => '30', 'rows' => '6'));
			break;
			default:
				$input = $this->{$type}($fieldName, $options);
		}

		if ($type != 'hidden' && $error !== false) {
			$errMsg = $this->error($fieldName, $error);
			if ($errMsg) {
				$divOptions = $this->addClass($divOptions, 'error');
				$out['error'] = $errMsg;
			}
		}

// Siva: info & help
		if (!empty($info)){
			$out['info'] = $this->Html->tag('span', $info, 'info');
		}
		if (!empty($help)){
			$out['help'] = $this->Html->tag('span', $help, 'help');
		}
// <--

		$out['input'] = $input;
		$format = $format ? $format : array('before', 'label', 'between', 'input', 'after', 'error', 'info', 'help');
		$output = '';
		foreach ($format as $element) {
			if (!empty($out[$element])) {
				$output .= $out[$element];
			}
			unset($out[$element]);
		}

		if (!empty($divOptions['tag'])) {
			$tag = $divOptions['tag'];
			unset($divOptions['tag']);
			$output = $this->Html->tag($tag, $output, $divOptions);
		}
		return $output;
	}

/**
 * Extracts a single option from an options array.
 *
 * @param string $name The name of the option to pull out.
 * @param array $options The array of options you want to extract.
 * @param mixed $default The default option value
 * @return the contents of the option or default
 */
	protected function _extractOption($name, $options, $default = null) {
		if (array_key_exists($name, $options)) {
			return $options[$name];
		}
		return $default;
	}

/**
 * Generate a label for an input() call.
 *
 * @param array $options Options for the label element.
 * @return string Generated label element
 */
	protected function _inputLabel($fieldName, $label, $options) {
		$labelAttributes = $this->domId(array(), 'for');
		if ($options['type'] === 'date' || $options['type'] === 'datetime') {
			if (isset($options['dateFormat']) && $options['dateFormat'] === 'NONE') {
				$labelAttributes['for'] .= 'Hour';
				$idKey = 'hour';
			} else {
				$labelAttributes['for'] .= 'Month';
				$idKey = 'month';
			}
			if (isset($options['id']) && isset($options['id'][$idKey])) {
				$labelAttributes['for'] = $options['id'][$idKey];
			}
		} elseif ($options['type'] === 'time') {
			$labelAttributes['for'] .= 'Hour';
			if (isset($options['id']) && isset($options['id']['hour'])) {
				$labelAttributes['for'] = $options['id']['hour'];
			}
		}

		if (is_array($label)) {
			$labelText = null;
			if (isset($label['text'])) {
				$labelText = $label['text'];
				unset($label['text']);
			}
			$labelAttributes = array_merge($labelAttributes, $label);
		} else {
			$labelText = $label;
		}

		if (isset($options['id']) && is_string($options['id'])) {
			$labelAttributes = array_merge($labelAttributes, array('for' => $options['id']));
		}
		return $this->label($fieldName, $labelText, $labelAttributes);
	}

/**
 * Creates a checkbox input widget.
 *
 * ### Options:
 *
 * - `value` - the value of the checkbox
 * - `checked` - boolean indicate that this checkbox is checked.
 * - `hiddenField` - boolean to indicate if you want the results of checkbox() to include
 *	a hidden input with a value of ''.
 * - `disabled` - create a disabled input.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string An HTML text input element.
 * @access public
 * @link http://book.cakephp.org/view/1414/checkbox
 */
	public function checkbox($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options) + array('hiddenField' => true);
		$value = current($this->value());
		$output = "";

		if (empty($options['value'])) {
			$options['value'] = 1;
		} elseif (!empty($value) && $value === $options['value']) {
			$options['checked'] = 'checked';
		}
		if ($options['hiddenField']) {
			$hiddenOptions = array(
				'id' => $options['id'] . '_', 'name' => $options['name'],
				'value' => '0', 'secure' => false
			);
			if (isset($options['disabled']) && $options['disabled'] == true) {
				$hiddenOptions['disabled'] = 'disabled';
			}
			$output = $this->hidden($fieldName, $hiddenOptions);
		}
		unset($options['hiddenField']);

		return $output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		);
	}

/**
 * Creates a set of radio widgets. Will create a legend and fieldset
 * by default.  Use $options to control this
 *
 * ### Attributes:
 *
 * - `separator` - define the string in between the radio buttons
 * - `legend` - control whether or not the widget set has a fieldset & legend
 * - `value` - indicate a value that is should be checked
 * - `label` - boolean to indicate whether or not labels for widgets show be displayed
 * - `hiddenField` - boolean to indicate if you want the results of radio() to include
 *	a hidden input with a value of ''. This is useful for creating radio sets that non-continuous
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Radio button options array.
 * @param array $attributes Array of HTML attributes, and special attributes above.
 * @return string Completed radio widget set.
 * @access public
 * @link http://book.cakephp.org/view/1429/radio
 */
	public function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->_initInputField($fieldName, $attributes);
		$legend = false;

		if (isset($attributes['legend'])) {
			$legend = $attributes['legend'];
			unset($attributes['legend']);
		} elseif (count($options) > 1) {
			$legend = __(Inflector::humanize($this->field()));
		}
		$label = true;

		if (isset($attributes['label'])) {
			$label = $attributes['label'];
			unset($attributes['label']);
		}
		$inbetween = null;

		if (isset($attributes['separator'])) {
			$inbetween = $attributes['separator'];
			unset($attributes['separator']);
		}

		if (isset($attributes['value'])) {
			$value = $attributes['value'];
		} else {
			$value =  $this->value($fieldName);
		}
		$out = array();

		$hiddenField = isset($attributes['hiddenField']) ? $attributes['hiddenField'] : true;
		unset($attributes['hiddenField']);

		foreach ($options as $optValue => $optTitle) {
			$optionsHere = array('value' => $optValue);

			if (isset($value) && $optValue == $value) {
				$optionsHere['checked'] = 'checked';
			}
			$parsedOptions = $this->_parseAttributes(
				array_merge($attributes, $optionsHere),
				array('name', 'type', 'id'), '', ' '
			);
			$tagName = Inflector::camelize(
				$attributes['id'] . '_' . Inflector::slug($optValue)
			);

			if ($label) {
				$optTitle =  sprintf($this->Html->tags['label'], $tagName, null, $optTitle);
			}
			$out[] =  sprintf(
				$this->Html->tags['radio'], $attributes['name'],
				$tagName, $parsedOptions, $optTitle
			);
		}
		$hidden = null;

		if ($hiddenField) {
			if (!isset($value) || $value === '') {
				$hidden = $this->hidden($fieldName, array(
					'id' => $attributes['id'] . '_', 'value' => '', 'name' => $attributes['name']
				));
			}
		}
		$out = $hidden . implode($inbetween, $out);

		if ($legend) {
			$out = sprintf(
				$this->Html->tags['fieldset'], '',
				sprintf($this->Html->tags['legend'], $legend) . $out
			);
		}
		return $out;
	}

/**
 * Missing method handler - implements various simple input types. Is used to create inputs 
 * of various types.  e.g. `$this->Form->text();` will create `<input type="text" />` while
 * `$this->Form->range();` will create `<input type="range" />`
 *
 * ### Usage
 *
 * `$this->Form->search('User.query', array('value' => 'test'));`
 *
 * Will make an input like:
 *
 * `<input type="search" id="UserQuery" name="data[User][query]" value="test" />`
 *
 * The first argument to an input type should always be the fieldname, in `Model.field` format.
 * The second argument should always be an array of attributes for the input.
 *
 * @param string $method Method name / input type to make.
 * @param array $params Parameters for the method call
 * @return string Formatted input method.
 * @throws CakeException When there are no params for the method call.
 */
	public function __call($method, $params) {
		$options = array();
		if (empty($params)) {
			throw new CakeException(__('Missing field name for FormHelper::%s', $method));
		}
		if (isset($params[1])) {
			$options = $params[1];
		}
		if (!isset($options['type'])) {
			$options['type'] = $method;
		}
		$options = $this->_initInputField($params[0], $options);
		return sprintf(
			$this->Html->tags['input'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		);
	}

/**
 * Creates a textarea widget.
 *
 * ### Options:
 *
 * - `escape` - Whether or not the contents of the textarea should be escaped. Defaults to true.
 *
 * @param string $fieldName Name of a field, in the form "Modelname.fieldname"
 * @param array $options Array of HTML attributes, and special options above.
 * @return string A generated HTML text input element
 * @access public
 * @link http://book.cakephp.org/view/1433/textarea
 */
	public function textarea($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options);
		$value = null;

		if (array_key_exists('value', $options)) {
			$value = $options['value'];
			if (!array_key_exists('escape', $options) || $options['escape'] !== false) {
				$value = h($value);
			}
			unset($options['value']);
		}
		return sprintf(
			$this->Html->tags['textarea'],
			$options['name'],
			$this->_parseAttributes($options, array('type', 'name'), null, ' '),
			$value
		);
	}

/**
 * Creates a hidden input field.
 *
 * @param string $fieldName Name of a field, in the form of "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string A generated hidden input
 * @access public
 * @link http://book.cakephp.org/view/1425/hidden
 */
	public function hidden($fieldName, $options = array()) {
		$secure = true;

		if (isset($options['secure'])) {
			$secure = $options['secure'];
			unset($options['secure']);
		}
		$options = $this->_initInputField($fieldName, array_merge(
			$options, array('secure' => false)
		));
		$model = $this->model();

		if ($fieldName !== '_method' && $model !== '_Token' && $secure) {
			$this->__secure(null, '' . $options['value']);
		}

		return sprintf(
			$this->Html->tags['hidden'],
			$options['name'],
			$this->_parseAttributes($options, array('name', 'class'), '', ' ')
		);
	}

/**
 * Creates file input widget.
 *
 * @param string $fieldName Name of a field, in the form "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string A generated file input.
 * @access public
 * @link http://book.cakephp.org/view/1424/file
 */
	public function file($fieldName, $options = array()) {
		$options = array_merge($options, array('secure' => false));
		$options = $this->_initInputField($fieldName, $options);
		$field = $this->_View->entity();

		foreach (array('name', 'type', 'tmp_name', 'error', 'size') as $suffix) {
			$this->__secure(array_merge($field, array($suffix)));
		}

		$attributes = $this->_parseAttributes($options, array('name'), '', ' ');
		return sprintf($this->Html->tags['file'], $options['name'], $attributes);
	}

/**
 * Creates a `<button>` tag.  The type attribute defaults to `type="submit"`
 * You can change it to a different value by using `$options['type']`.
 *
 * ### Options:
 *
 * - `escape` - HTML entity encode the $title of the button. Defaults to false.
 *
 * @param string $title The button's caption. Not automatically HTML encoded
 * @param array $options Array of options and HTML attributes.
 * @return string A HTML button tag.
 * @access public
 * @link http://book.cakephp.org/view/1415/button
 */
	public function button($title, $options = array()) {
		$options += array('type' => 'submit', 'escape' => false);
		if ($options['escape']) {
			$title = h($title);
		}
		return sprintf(
			$this->Html->tags['button'],
			$options['type'],
			$this->_parseAttributes($options, array('type'), ' ', ''),
			$title
		);
	}

/**
 * Create a `<button>` tag with `<form>` using POST method.
 *
 * This method creates an element <form>. So do not use this method in some opened form.
 *
 * ### Options:
 *
 * - `data` - Array with key/value to pass in input hidden
 * - Other options is the same of button method.
 *
 * @param string $title The button's caption. Not automatically HTML encoded
 * @param mixed $url URL as string or array
 * @param array $options Array of options and HTML attributes.
 * @return string A HTML button tag.
 */
	public function postButton($title, $url, $options = array()) {
		$out = $this->create(false, array('id' => false, 'url' => $url, 'style' => 'display:none;'));
		if (isset($options['data']) && is_array($options['data'])) {
			foreach ($options['data'] as $key => $value) {
				$out .= $this->hidden($key, array('value' => $value, 'id' => false));
			}
			unset($options['data']);
		}
		$out .= $this->button($title, $options);
		$out .= $this->end();
		return $out;
	}

/**
 * Creates an HTML link, but access the url using method POST. Requires javascript enabled in browser.
 *
 * This method creates an element <form>. So do not use this method in some opened form.
 *
 * ### Options:
 *
 * - `data` - Array with key/value to pass in input hidden
 * - Other options is the same of HtmlHelper::link() method.
 * - The option `onclick` will be replaced.
 *
 * @param string $title The content to be wrapped by <a> tags.
 * @param mixed $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array $options Array of HTML attributes.
 * @param string $confirmMessage JavaScript confirmation message.
 * @return string An `<a />` element.
 */
	public function postLink($title, $url = null, $options = array(), $confirmMessage = false) {
		if (!empty($options['confirm'])) {
			$confirmMessage = $options['confirm'];
			unset($options['confirm']);
		}

		$url = $this->url($url);
		$formName = uniqid('post_');
		$out = $this->create(false, array('url' => $url, 'name' => $formName, 'id' => $formName, 'style' => 'display:none;'));
		if (isset($options['data']) && is_array($options['data'])) {
			foreach ($options['data'] as $key => $value) {
				$out .= $this->hidden($key, array('value' => $value, 'id' => false));
			}
			unset($options['data']);
		}
		$out .= $this->end();

		$url = '#';
		$onClick = 'document.' . $formName . '.submit();';
		if ($confirmMessage) {
			$confirmMessage = str_replace(array("'", '"'), array("\'", '\"'), $confirmMessage);
			$options['onclick'] = "if (confirm('{$confirmMessage}')) { {$onClick} }";
		} else {
			$options['onclick'] = $onClick;
		}
		$options['onclick'] .= ' event.returnValue = false; return false;';

		$out .= $this->Html->link($title, $url, $options);
		return $out;
	}

/**
 * Creates a submit button element.  This method will generate `<input />` elements that
 * can be used to submit, and reset forms by using $options.  image submits can be created by supplying an
 * image path for $caption.
 *
 * ### Options
 *
 * - `div` - Include a wrapping div?  Defaults to true.  Accepts sub options similar to
 *   FormHelper::input().
 * - `before` - Content to include before the input.
 * - `after` - Content to include after the input.
 * - `type` - Set to 'reset' for reset inputs.  Defaults to 'submit'
 * - Other attributes will be assigned to the input element.
 *
 * ### Options
 *
 * - `div` - Include a wrapping div?  Defaults to true.  Accepts sub options similar to
 *   FormHelper::input().
 * - Other attributes will be assigned to the input element.
 *
 * @param string $caption The label appearing on the button OR if string contains :// or the
 *  extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
 *  exists, AND the first character is /, image is relative to webroot,
 *  OR if the first character is not /, image is relative to webroot/img.
 * @param array $options Array of options.  See above.
 * @return string A HTML submit button
 * @access public
 * @link http://book.cakephp.org/view/1431/submit
 */
	public function submit($caption = null, $options = array()) {
		if (!$caption) {
			$caption = __('Submit');
		}
		$out = null;
		$div = true;

		if (isset($options['div'])) {
			$div = $options['div'];
			unset($options['div']);
		}
		$options += array('type' => 'submit', 'before' => null, 'after' => null);
		$divOptions = array('tag' => 'div');

		if ($div === true) {
			$divOptions['class'] = 'submit';
		} elseif ($div === false) {
			unset($divOptions);
		} elseif (is_string($div)) {
			$divOptions['class'] = $div;
		} elseif (is_array($div)) {
			$divOptions = array_merge(array('class' => 'submit', 'tag' => 'div'), $div);
		}

		$before = $options['before'];
		$after = $options['after'];
		unset($options['before'], $options['after']);

		if (strpos($caption, '://') !== false) {
			unset($options['type']);
			$out .=  $before . sprintf(
				$this->Html->tags['submitimage'],
				$caption,
				$this->_parseAttributes($options, null, '', ' ')
			) . $after;
		} elseif (preg_match('/\.(jpg|jpe|jpeg|gif|png|ico)$/', $caption)) {
			unset($options['type']);
			if ($caption{0} !== '/') {
				$url = $this->webroot(IMAGES_URL . $caption);
			} else {
				$caption = trim($caption, '/');
				$url = $this->webroot($caption);
			}
			$out .= $before . sprintf(
				$this->Html->tags['submitimage'],
				$url,
				$this->_parseAttributes($options, null, '', ' ')
			) . $after;
		} else {
			$options['value'] = $caption;
			$out .= $before . sprintf(
				$this->Html->tags['submit'],
				$this->_parseAttributes($options, null, '', ' ')
			). $after;
		}

		if (isset($divOptions)) {
			$tag = $divOptions['tag'];
			unset($divOptions['tag']);
			$out = $this->Html->tag($tag, $out, $divOptions);
		}
		return $out;
	}

/**
 * Returns a formatted SELECT element.
 *
 * ### Attributes:
 *
 * - `showParents` - If included in the array and set to true, an additional option element
 *   will be added for the parent of each option group. You can set an option with the same name
 *   and it's key will be used for the value of the option.
 * - `multiple` - show a multiple select box.  If set to 'checkbox' multiple checkboxes will be
 *   created instead.
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `escape` - If true contents of options will be HTML entity encoded. Defaults to true.
 * - `value` The selected value of the input.
 * - `class` - When using multiple = checkbox the classname to apply to the divs. Defaults to 'checkbox'.
 *
 * ### Using options
 *
 * A simple array will create normal options:
 *
 * {{{
 * $options = array(1 => 'one', 2 => 'two);
 * $this->Form->select('Model.field', $options));
 * }}}
 *
 * While a nested options array will create optgroups with options inside them.
 * {{{
 * $options = array(
 *	1 => 'bill',
 *	'fred' => array(
 *		2 => 'fred',
 *		3 => 'fred jr.'
 *	 )
 * );
 * $this->Form->select('Model.field', $options);
 * }}}
 *
 * In the above `2 => 'fred'` will not generate an option element.  You should enable the `showParents`
 * attribute to show the fred option.
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
 *	SELECT element
 * @param array $attributes The HTML attributes of the select element.
 * @return string Formatted SELECT element
 * @access public
 * @link http://book.cakephp.org/view/1430/select
 */
	public function select($fieldName, $options = array(), $attributes = array()) {
		$select = array();
		$style = null;
		$tag = null;
		$attributes += array(
			'class' => null, 
			'escape' => true,
			'secure' => null,
			'empty' => '',
			'showParents' => false
		);

		$escapeOptions = $this->_extractOption('escape', $attributes);
		$secure = $this->_extractOption('secure', $attributes);
		$showEmpty = $this->_extractOption('empty', $attributes);
		$showParents = $this->_extractOption('showParents', $attributes);
		unset($attributes['escape'], $attributes['secure'], $attributes['empty'], $attributes['showParents']);

		$attributes = $this->_initInputField($fieldName, array_merge(
			(array)$attributes, array('secure' => false)
		));

		if (is_string($options) && isset($this->__options[$options])) {
			$options = $this->__generateOptions($options);
		} elseif (!is_array($options)) {
			$options = array();
		}
		if (isset($attributes['type'])) {
			unset($attributes['type']);
		}

		if (isset($attributes) && array_key_exists('multiple', $attributes)) {
			$style = ($attributes['multiple'] === 'checkbox') ? 'checkbox' : null;
			$template = ($style) ? 'checkboxmultiplestart' : 'selectmultiplestart';
			$tag = $this->Html->tags[$template];
			$hiddenAttributes = array(
				'value' => '',
				'id' => $attributes['id'] . ($style ? '' : '_'),
				'secure' => false,
				'name' => $attributes['name']
			);
			$select[] = $this->hidden(null, $hiddenAttributes);
		} else {
			$tag = $this->Html->tags['selectstart'];
		}

		if (!empty($tag) || isset($template)) {
			if (!isset($secure) || $secure == true) {
				$this->__secure();
			}
			$select[] = sprintf($tag, $attributes['name'], $this->_parseAttributes(
				$attributes, array('name', 'value'))
			);
		}
		$emptyMulti = (
			$showEmpty !== null && $showEmpty !== false && !(
				empty($showEmpty) && (isset($attributes) &&
				array_key_exists('multiple', $attributes))
			)
		);

		if ($emptyMulti) {
			$showEmpty = ($showEmpty === true) ? '' : $showEmpty;
			$options = array_reverse($options, true);
			$options[''] = $showEmpty;
			$options = array_reverse($options, true);
		}

		$select = array_merge($select, $this->__selectOptions(
			array_reverse($options, true),
			array(),
			$showParents,
			array('escape' => $escapeOptions, 'style' => $style, 'name' => $attributes['name'], 'value' => $attributes['value'], 'class' => $attributes['class'])
		));

		$template = ($style == 'checkbox') ? 'checkboxmultipleend' : 'selectend';
		$select[] = $this->Html->tags[$template];
		return implode("\n", $select);
	}

/**
 * Returns a SELECT element for days.
 *
 * ### Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `value` The selected value of the input.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param array $attributes HTML attributes for the select element
 * @return string A generated day select box.
 * @access public
 * @link http://book.cakephp.org/view/1419/day
 */
	public function day($fieldName = null, $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		$attributes = $this->__dateTimeSelected('day', $fieldName, $attributes);

		if (strlen($attributes['value']) > 2) {
			$attributes['value'] = date('d', strtotime($attributes['value']));
		} elseif ($attributes['value'] === false) {
			$attributes['value'] = null;
		}
		return $this->select($fieldName . ".day", $this->__generateOptions('day'), $attributes);
	}

/**
 * Returns a SELECT element for years
 *
 * ### Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `orderYear` - Ordering of year values in select options.
 *   Possible values 'asc', 'desc'. Default 'desc'
 * - `value` The selected value of the input.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param integer $minYear First year in sequence
 * @param integer $maxYear Last year in sequence
 * @param array $attributes Attribute array for the select elements.
 * @return string Completed year select input
 * @access public
 * @link http://book.cakephp.org/view/1416/year
 */
	public function year($fieldName, $minYear = null, $maxYear = null, $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		if ((empty($attributes['value']) || $attributes['value'] === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$attributes['value'] = $year;
			} else {
				if (empty($value)) {
					if (!$attributes['empty'] && !$maxYear) {
						$attributes['value'] = 'now';

					} elseif (!$attributes['empty'] && $maxYear && !$attributes['value']) {
						$attributes['value'] = $maxYear;
					}
				} else {
					$attributes['value'] = $value;
				}
			}
		}

		if (strlen($attributes['value']) > 4 || $attributes['value'] === 'now') {
			$attributes['value'] = date('Y', strtotime($attributes['value']));
		} elseif ($attributes['value'] === false) {
			$attributes['value'] = null;
		}
		$yearOptions = array('min' => $minYear, 'max' => $maxYear, 'order' => 'desc');
		if (isset($attributes['orderYear'])) {
			$yearOptions['order'] = $attributes['orderYear'];
			unset($attributes['orderYear']);
		}
		return $this->select(
			$fieldName . '.year', $this->__generateOptions('year', $yearOptions),
			$attributes
		);
	}

/**
 * Returns a SELECT element for months.
 *
 * ### Attributes:
 *
 * - `monthNames` - If false, 2 digit numbers will be used instead of text.
 *   If a array, the given array will be used.
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `value` The selected value of the input.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param array $attributes Attributes for the select element
 * @return string A generated month select dropdown.
 * @access public
 * @link http://book.cakephp.org/view/1417/month
 */
	public function month($fieldName, $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		$attributes = $this->__dateTimeSelected('month', $fieldName, $attributes);

		if (strlen($attributes['value']) > 2) {
			$attributes['value'] = date('m', strtotime($attributes['value']));
		} elseif ($attributes['value'] === false) {
			$attributes['value'] = null;
		}
		$defaults = array('monthNames' => true);
		$attributes = array_merge($defaults, (array) $attributes);
		$monthNames = $attributes['monthNames'];
		unset($attributes['monthNames']);

		return $this->select(
			$fieldName . ".month",
			$this->__generateOptions('month', array('monthNames' => $monthNames)),
			$attributes
		);
	}

/**
 * Returns a SELECT element for hours.
 *
 * ### Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `value` The selected value of the input.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param boolean $format24Hours True for 24 hours format
 * @param array $attributes List of HTML attributes
 * @return string Completed hour select input
 * @access public
 * @link http://book.cakephp.org/view/1420/hour
 */
	public function hour($fieldName, $format24Hours = false, $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		$attributes = $this->__dateTimeSelected('hour', $fieldName, $attributes);

		if (strlen($attributes['value']) > 2) {
			if ($format24Hours) {
				$attributes['value'] = date('H', strtotime($attributes['value']));
			} else {
				$attributes['value'] = date('g', strtotime($attributes['value']));
			}
		} elseif ($attributes['value'] === false) {
			$attributes['value'] = null;
		}
		return $this->select(
			$fieldName . ".hour",
			$this->__generateOptions($format24Hours ? 'hour24' : 'hour'),
			$attributes
		);
	}

/**
 * Returns a SELECT element for minutes.
 *
 * ### Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `value` The selected value of the input.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $attributes Array of Attributes
 * @return string Completed minute select input.
 * @access public
 * @link http://book.cakephp.org/view/1421/minute
 */
	public function minute($fieldName, $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		$attributes = $this->__dateTimeSelected('min', $fieldName, $attributes);

		if (strlen($attributes['value']) > 2) {
			$attributes['value'] = date('i', strtotime($attributes['value']));
		} elseif ($attributes['value'] === false) {
			$attributes['value'] = null;
		}
		$minuteOptions = array();

		if (isset($attributes['interval'])) {
			$minuteOptions['interval'] = $attributes['interval'];
			unset($attributes['interval']);
		}
		return $this->select(
			$fieldName . ".min", $this->__generateOptions('minute', $minuteOptions),
			$attributes
		);
	}

/**
 * Selects values for dateTime selects.
 *
 * @param string $select Name of element field. ex. 'day'
 * @param string $fieldName Name of fieldName being generated ex. Model.created
 * @param array $attributes Array of attributes, must contain 'empty' key.
 * @return array Attributes array with currently selected value.
 * @access private
 */
	function __dateTimeSelected($select, $fieldName, $attributes) {
		if ((empty($attributes['value']) || $attributes['value'] === true) && $value = $this->value($fieldName)) {
			if (is_array($value) && isset($value[$select])) {
				$attributes['value'] = $value[$select];
			} else {
				if (empty($value)) {
					if (!$attributes['empty']) {
						$attributes['value'] = 'now';
					}
				} else {
					$attributes['value'] = $value;
				}
			}
		}
		return $attributes;
	}

/**
 * Returns a SELECT element for AM or PM.
 *
 * ### Attributes:
 *
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `value` The selected value of the input.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $attributes Array of Attributes
 * @param bool $showEmpty Show/Hide an empty option
 * @return string Completed meridian select input
 * @access public
 * @link http://book.cakephp.org/view/1422/meridian
 */
	public function meridian($fieldName, $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		if ((empty($attributes['value']) || $attributes['value'] === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$attributes['value'] = $meridian;
			} else {
				if (empty($value)) {
					if (!$attribues['empty']) {
						$attributes['value'] = date('a');
					}
				} else {
					$attributes['value'] = date('a', strtotime($value));
				}
			}
		}

		if ($attributes['value'] === false) {
			$attributes['value'] = null;
		}
		return $this->select(
			$fieldName . ".meridian", $this->__generateOptions('meridian'),
			$attributes
		);
	}

/**
 * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
 *
 * ### Attributes:
 *
 * - `monthNames` If false, 2 digit numbers will be used instead of text.
 *   If a array, the given array will be used.
 * - `minYear` The lowest year to use in the year select
 * - `maxYear` The maximum year to use in the year select
 * - `interval` The interval for the minutes select. Defaults to 1
 * - `separator` The contents of the string between select elements. Defaults to '-'
 * - `empty` - If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * - `value` | `default` The default value to be used by the input.  A value in `$this->data`
 *   matching the field name will override this value.  If no default is provided `time()` will be used.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $dateFormat DMY, MDY, YMD.
 * @param string $timeFormat 12, 24.
 * @param string $attributes array of Attributes
 * @return string Generated set of select boxes for the date and time formats chosen.
 * @access public
 * @link http://book.cakephp.org/view/1418/dateTime
 */
	public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null);
		$year = $month = $day = $hour = $min = $meridian = null;

		if (empty($attributes['value'])) {
			$attributes = $this->value($attributes, $fieldName);
		}

		if ($attributes['value'] === null && $attributes['empty'] != true) {
			$attributes['value'] = time();
		}

		if (!empty($attributes['value'])) {
			if (is_array($attributes['value'])) {
				extract($attributes['value']);
			} else {
				if (is_numeric($attributes['value'])) {
					$attributes['value'] = strftime('%Y-%m-%d %H:%M:%S', strtotime(date('Y-m-d H:i:s', $attributes['value']) . ' GMT'));
				}
				$meridian = 'am';
				$pos = strpos($attributes['value'], '-');
				if ($pos !== false) {
					$date = explode('-', $attributes['value']);
					$days = explode(' ', $date[2]);
					$day = $days[0];
					$month = $date[1];
					$year = $date[0];
				} else {
					$days[1] = $attributes['value'];
				}

				if (!empty($timeFormat)) {
					$time = explode(':', $days[1]);
					$check = str_replace(':', '', $days[1]);

					if (($check > 115959) && $timeFormat == '12') {
						$time[0] = $time[0] - 12;
						$meridian = 'pm';
					} elseif ($time[0] == '00' && $timeFormat == '12') {
						$time[0] = 12;
					} elseif ($time[0] > 12) {
						$meridian = 'pm';
					}
					if ($time[0] == 0 && $timeFormat == '12') {
						$time[0] = 12;
					}
					$hour = $time[0];
					$min = $time[1];
				}
			}
		}

		$elements = array('Day', 'Month', 'Year', 'Hour', 'Minute', 'Meridian');
		$defaults = array(
			'minYear' => null, 'maxYear' => null, 'separator' => '-',
			'interval' => 1, 'monthNames' => true
		);
		$attributes = array_merge($defaults, (array) $attributes);
		if (isset($attributes['minuteInterval'])) {
			$attributes['interval'] = $attributes['minuteInterval'];
			unset($attributes['minuteInterval']);
		}
		$minYear = $attributes['minYear'];
		$maxYear = $attributes['maxYear'];
		$separator = $attributes['separator'];
		$interval = $attributes['interval'];
		$monthNames = $attributes['monthNames'];
		$attributes = array_diff_key($attributes, $defaults);

		if (isset($attributes['id'])) {
			if (is_string($attributes['id'])) {
				// build out an array version
				foreach ($elements as $element) {
					$selectAttrName = 'select' . $element . 'Attr';
					${$selectAttrName} = $attributes;
					${$selectAttrName}['id'] = $attributes['id'] . $element;
				}
			} elseif (is_array($attributes['id'])) {
				// check for missing ones and build selectAttr for each element
				$attributes['id'] += array(
					'month' => '', 'year' => '', 'day' => '',
					'hour' => '', 'minute' => '', 'meridian' => ''
				);
				foreach ($elements as $element) {
					$selectAttrName = 'select' . $element . 'Attr';
					${$selectAttrName} = $attributes;
					${$selectAttrName}['id'] = $attributes['id'][strtolower($element)];
				}
			}
		} else {
			// build the selectAttrName with empty id's to pass
			foreach ($elements as $element) {
				$selectAttrName = 'select' . $element . 'Attr';
				${$selectAttrName} = $attributes;
			}
		}

		$selects = array();
		foreach (preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
			switch ($char) {
				case 'Y':
					$selectYearAttr['value'] = $year;
					$selects[] = $this->year(
						$fieldName, $minYear, $maxYear, $selectYearAttr
					);
				break;
				case 'M':
					$selectMonthAttr['value'] = $month;
					$selectMonthAttr['monthNames'] = $monthNames;
					$selects[] = $this->month($fieldName, $selectMonthAttr);
				break;
				case 'D':
					$selectDayAttr['value'] = $day;
					$selects[] = $this->day($fieldName, $selectDayAttr);
				break;
			}
		}
		$opt = implode($separator, $selects);

		if (!empty($interval) && $interval > 1 && !empty($min)) {
			$min = round($min * (1 / $interval)) * $interval;
		}
		$selectMinuteAttr['interval'] = $interval;
		switch ($timeFormat) {
			case '24':
				$selectHourAttr['value'] = $hour;
				$selectMinuteAttr['value'] = $min;
				$opt .= $this->hour($fieldName, true, $selectHourAttr) . ':' .
				$this->minute($fieldName, $selectMinuteAttr);
			break;
			case '12':
				$selectHourAttr['value'] = $hour;
				$selectMinuteAttr['value'] = $min;
				$selectMeridianAttr['value'] = $meridian;
				$opt .= $this->hour($fieldName, false, $selectHourAttr) . ':' .
				$this->minute($fieldName, $selectMinuteAttr) . ' ' .
				$this->meridian($fieldName, $selectMeridianAttr);
			break;
			default:
				$opt .= '';
			break;
		}
		return $opt;
	}

/**
 * Gets the input field name for the current tag
 *
 * @param array $options
 * @param string $key
 * @return array
 */
	protected function _name($options = array(), $field = null, $key = 'name') {
		if ($this->requestType == 'get') {
			if ($options === null) {
				$options = array();
			} elseif (is_string($options)) {
				$field = $options;
				$options = 0;
			}

			if (!empty($field)) {
				$this->setEntity($field);
			}

			if (is_array($options) && isset($options[$key])) {
				return $options;
			}

			$name = $this->_View->field;
			if (!empty($this->_View->fieldSuffix)) {
				$name .= '[' . $this->_View->fieldSuffix . ']';
			}

			if (is_array($options)) {
				$options[$key] = $name;
				return $options;
			} else {
				return $name;
			}
		}
		return parent::_name($options, $field, $key);
	}

/**
 * Returns an array of formatted OPTION/OPTGROUP elements
 * @access private
 * @return array
 */
	function __selectOptions($elements = array(), $parents = array(), $showParents = null, $attributes = array()) {
		$select = array();
		$attributes = array_merge(
			array('escape' => true, 'style' => null, 'value' => null, 'class' => null), 
			$attributes
		);
		$selectedIsEmpty = ($attributes['value'] === '' || $attributes['value'] === null);
		$selectedIsArray = is_array($attributes['value']);

		foreach ($elements as $name => $title) {
			$htmlOptions = array();
			if (is_array($title) && (!isset($title['name']) || !isset($title['value']))) {
				if (!empty($name)) {
					if ($attributes['style'] === 'checkbox') {
						$select[] = $this->Html->tags['fieldsetend'];
					} else {
						$select[] = $this->Html->tags['optiongroupend'];
					}
					$parents[] = $name;
				}
				$select = array_merge($select, $this->__selectOptions(
					$title, $parents, $showParents, $attributes
				));

				if (!empty($name)) {
					$name = $attributes['escape'] ? h($name) : $name;
					if ($attributes['style'] === 'checkbox') {
						$select[] = sprintf($this->Html->tags['fieldsetstart'], $name);
					} else {
						$select[] = sprintf($this->Html->tags['optiongroup'], $name, '');
					}
				}
				$name = null;
			} elseif (is_array($title)) {
				$htmlOptions = $title;
				$name = $title['value'];
				$title = $title['name'];
				unset($htmlOptions['name'], $htmlOptions['value']);
			}

			if ($name !== null) {
				if (
					(!$selectedIsArray && !$selectedIsEmpty && (string)$attributes['value'] == (string)$name) ||
					($selectedIsArray && in_array($name, $attributes['value']))
				) {
					if ($attributes['style'] === 'checkbox') {
						$htmlOptions['checked'] = true;
					} else {
						$htmlOptions['selected'] = 'selected';
					}
				}

				if ($showParents || (!in_array($title, $parents))) {
					$title = ($attributes['escape']) ? h($title) : $title;

					if ($attributes['style'] === 'checkbox') {
						$htmlOptions['value'] = $name;

						$tagName = Inflector::camelize(
							$this->model() . '_' . $this->field().'_'.Inflector::slug($name)
						);
						$htmlOptions['id'] = $tagName;
						$label = array('for' => $tagName);

						if (isset($htmlOptions['checked']) && $htmlOptions['checked'] === true) {
							$label['class'] = 'selected';
						}

						$name = $attributes['name'];

						if (empty($attributes['class'])) {
							$attributes['class'] = 'checkbox';
						}
						$label = $this->label(null, $title, $label);
						$item = sprintf(
							$this->Html->tags['checkboxmultiple'], $name,
							$this->_parseAttributes($htmlOptions)
						);
						$select[] = $this->Html->div($attributes['class'], $item . $label);
					} else {
						$select[] = sprintf(
							$this->Html->tags['selectoption'],
							$name, $this->_parseAttributes($htmlOptions), $title
						);
					}
				}
			}
		}

		return array_reverse($select, true);
	}

/**
 * Generates option lists for common <select /> menus
 * @access private
 */
	function __generateOptions($name, $options = array()) {
		if (!empty($this->options[$name])) {
			return $this->options[$name];
		}
		$data = array();

		switch ($name) {
			case 'minute':
				if (isset($options['interval'])) {
					$interval = $options['interval'];
				} else {
					$interval = 1;
				}
				$i = 0;
				while ($i < 60) {
					$data[sprintf('%02d', $i)] = sprintf('%02d', $i);
					$i += $interval;
				}
			break;
			case 'hour':
				for ($i = 1; $i <= 12; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'hour24':
				for ($i = 0; $i <= 23; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'meridian':
				$data = array('am' => 'am', 'pm' => 'pm');
			break;
			case 'day':
				$min = 1;
				$max = 31;

				if (isset($options['min'])) {
					$min = $options['min'];
				}
				if (isset($options['max'])) {
					$max = $options['max'];
				}

				for ($i = $min; $i <= $max; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'month':
				if ($options['monthNames'] === true) {
// rajesh_04ag02 // 2008-12-17 // for auto localization with setlocale()
// patch taken from https://trac.cakephp.org/ticket/1410
                    for ($m = 1; $m <= 12; $m++) {
                    	$data[sprintf("%02s", $m)] = utf8_encode(strftime("%B", mktime(1, 1, 1, $m, 1, 1999)));
                    }
/*
// Commented out temporarily(?)
					$data['01'] = __('January');
					$data['02'] = __('February');
					$data['03'] = __('March');
					$data['04'] = __('April');
					$data['05'] = __('May');
					$data['06'] = __('June');
					$data['07'] = __('July');
					$data['08'] = __('August');
					$data['09'] = __('September');
					$data['10'] = __('October');
					$data['11'] = __('November');
					$data['12'] = __('December');
*/
				} else if (is_array($options['monthNames'])) {
					$data = $options['monthNames'];
				} else {
					for ($m = 1; $m <= 12; $m++) {
						$data[sprintf("%02s", $m)] = strftime("%m", mktime(1, 1, 1, $m, 1, 1999));
					}
				}
			break;
			case 'year':
				$current = intval(date('Y'));

				if (!isset($options['min'])) {
					$min = $current - 20;
				} else {
					$min = $options['min'];
				}

				if (!isset($options['max'])) {
					$max = $current + 20;
				} else {
					$max = $options['max'];
				}
				if ($min > $max) {
					list($min, $max) = array($max, $min);
				}
				for ($i = $min; $i <= $max; $i++) {
					$data[$i] = $i;
				}
				if ($options['order'] != 'asc') {
					$data = array_reverse($data, true);
				}
			break;
		}
		$this->__options[$name] = $data;
		return $this->__options[$name];
	}

/**
 * Sets field defaults and adds field to form security input hash
 *
 * ### Options
 *
 *  - `secure` - boolean whether or not the field should be added to the security fields.
 *
 * @param string $field Name of the field to initialize options for.
 * @param array $options Array of options to append options into.
 * @return array Array of options for the input.
 */
	protected function _initInputField($field, $options = array()) {
		if (isset($options['secure'])) {
			$secure = $options['secure'];
			unset($options['secure']);
		} else {
			$secure = (isset($this->request['_Token']) && !empty($this->request['_Token']));
		}
		$result = parent::_initInputField($field, $options);

		if ($secure) {
			$this->__secure();
		}
		return $result;
	}
	// rajesh_04ag02 // 2008-12-01 & 05
    // 'acFieldKey' -> string
    // 'acFields' -> array
    // 'acSearchFieldNames' -> array
    // 'acMultiple' // Siva // 2009-01-07 // for to have comma separated list
    function autocomplete($fieldName, $options = array())
    {
        // which field (Model.name) which name to save Model.id
        if (strpos($fieldName, '.') !== false) {
            $tmp = explode('.', $fieldName);
            $model = $tmp[0];
        } else {
            $model = $this->model();
        }
        if (isset($options['acSearchFieldNames'])) {
            $params['acSearchFieldNames'] = $options['acSearchFieldNames'];
            unset($options['acSearchFieldNames']);
            if (strpos($params['acSearchFieldNames'][0], '.') !== false) {
                $tmp = explode('.', $params['acSearchFieldNames'][0]);
                $model = $tmp[0];
            }
        } else {
            $params['acSearchFieldNames'] = null;
        }
        if (isset($options['acFields'])) {
            $params['acFields'] = $options['acFields'];;
            unset($options['acFields']);
        } else {
            $params['acFields'] = null;
        }
        if (isset($options['acFieldKey'])) {
            $acFieldKey = $options['acFieldKey'];
            unset($options['acFieldKey']);
            $params['acFieldKey'] = $acFieldKey;
        } else {
            $acFieldKey = $model . '.id';
            $params['acFieldKey'] = null;
        }
		if (isset($options['acMultiple']) && $options['acMultiple']) {
            $classSelector = 'js-multi-autocomplete';
			unset($options['acMultiple']);
        } else {
			$classSelector = 'js-autocomplete';
        }
        $param_encode = base64_url_encode(gzdeflate(serialize($params), 9));
        $param_hash = substr(md5(Configure::read('Security.salt') . $param_encode) , 5, 7);
        $class = 'autocomplete '.$classSelector.' {url:\'' . $this->url(array(
            'controller' => strtolower(Inflector::underscore(Inflector::Pluralize($model))) ,
            'action' => 'autocomplete',
            'admin' => false,
            $param_encode,
            $param_hash,
            'ext' => 'json'
        )) . '\', targetField:\'' . htmlentities($this->input($acFieldKey, array(
            'type' => 'hidden',
            'id' => $this->domId($acFieldKey) . '_H'
        ))) . '\', id:\'' . $this->domId($acFieldKey) . '_H\'}';
        if (isset($options['class'])) {
            $options['class'] = array_merge($options['class'], $class);
        } else {
            $options['class'] = $class;
        }
        return $this->input($fieldName, $options);
    }

    function uploader($fieldName, $options = array())
    {
        $count = 5;
        $filesize = '5242880'; // 5 MB
        $filetype = '*';
        $queuefilesize = '52428800'; // 50 MB
        $movieid = 'uploader1';
        $redirectURL = $controller = '';
        if (isset($options['uController'])) {
            $controller = $options['uController'];
            unset($options['uController']);
        }
        if (isset($options['uFilecount'])) {
            $count = $options['uFilecount'];
            unset($options['uFilecount']);
        }
        if (isset($options['uFilesize'])) {
            $filesize = $options['uFilesize'];
            unset($options['uFilesize']);
        }
        $ini_max_allowed_filesize = $this->_ini_value_higher_to_bytes(ini_get('upload_max_filesize'));
        if ($ini_max_allowed_filesize < $filesize) {
            $filesize = $ini_max_allowed_filesize;
        }
        if (isset($options['uFiletype'])) {
            foreach($options['uFiletype'] as $value) {
                $temp_arr[] = '*.' . $value;
            }
            $filetype = implode(';', $temp_arr);
            unset($options['uFiletype']);
        }
        if (isset($options['uId'])) {
            $movieid = $options['uId'];
            unset($options['uId']);
        }
        if (isset($options['uQueuefilesize'])) {
            $queuefilesize = $options['uQueuefilesize'];
            unset($options['uQueuefilesize']);
        }
        if (isset($options['uRedirectURL'])) {
            $redirectURL = Router::url($options['uRedirectURL']);
            unset($options['uRedirectURL']);
        }
        $uploader = '<div class="js-uploader {controller:\'' . $controller . '\',filecount:\'' . $count . '\',queuefilesize:\'' . $queuefilesize . '\',filesize:\'' . $filesize . '\',filetype:\'' . $filetype . '\',movieid:\'' . $movieid . '\',session:\'' . session_id() . '\',redirectURL:\'' . $redirectURL . '\'}">';
		$uploader.= '<div id="uploaderOverlay_' . $movieid . '" class="flashUploader" style="width:120px; height:30px;"></div>';
        $uploader.= '<div class="normal-uploader">';
        for ($i = 0; $i < $count; $i++) {
            $field_name_arr = explode('.', $fieldName);
            $newFieldname = $field_name_arr[0] . '.' . $i . '.' . $field_name_arr[1];
            $options['label'] = __l('File');
            $uploader.= $this->input($newFieldname, $options);
        }
        $uploader.= '</div>';        
        $uploader.= '</div>';
        return $uploader;
    }
    // INI value higher unit to bytes conversion
    function _ini_value_higher_to_bytes($val)
    {
        if (!$val) {
            return 0;
        }
        $last = strtolower(substr(trim($val) , -1));
        $val = substr(trim($val) , 0, -1);
        switch ($last) {
                // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                return higher_to_bytes($val, 'GB');
            case 'm':
                return higher_to_bytes($val, 'MB');
            case 'k':
                return higher_to_bytes($val, 'KB');
        }
    }
}
