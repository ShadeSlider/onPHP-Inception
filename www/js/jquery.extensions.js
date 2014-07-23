/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
jQuery.fn.extend({
	makeFormFieldsReadOnly:
	function() {
		var $fieldsWrapper = $(this);
		var $formFields = $fieldsWrapper.getFormFields(false);

		$formFields.attr('readonly', 'readonly');

		return $(this);
	},
	makeFormFieldsNotReadOnly:
	function() {
		var $fieldsWrapper = $(this);
		var $formFields = $fieldsWrapper.getFormFields(false);

		$formFields.removeAttr('readonly');

		return $(this);
	},
	enableFormFields:
	function(exclude) {
		exclude = exclude || [];
		var $fieldsWrapper = $(this);
		var $formFields = $fieldsWrapper.getFormFields(false);

		$(exclude).each(function (idx, value) {
			$formFields = $formFields.not(value);
		});

		$formFields.removeAttr('disabled').removeProp('disabled');

		return $(this);
	},

	disableFormFields:
	function(exclude) {
		exclude = exclude || [];
		var $fieldsWrapper = $(this);
		var $formFields = $fieldsWrapper.getFormFields();

		$(exclude).each(function (idx, value) {
			$formFields = $formFields.not(value);
		});

		$formFields.attr('disabled', 'disabled');

		return $(this);
	},

	getFormFields:
	function(skipDisabled) {
		skipDisabled = skipDisabled == undefined ? true : skipDisabled;
		var $fieldsWrapper = $(this);

		var $formFields = $fieldsWrapper.find('input, select, textarea, button');

		if(skipDisabled) {
			$formFields = $formFields.not('[disabled]');
		}

		return $formFields;
	},

	getFormFieldsArray:
	function(ignoreEmpty) {
		var $fieldsWrapper = $(this);
		ignoreEmpty = ignoreEmpty == undefined ? true : ignoreEmpty;

		fieldsData = {};
		$fieldsWrapper.getFormFields().each(function () {

			var $field = $(this);
			var fieldValue = '';

			if(!$field.prop('name')) {
				return;
			}

			switch($field.prop('tagName')) {
				case 'SELECT':
					fieldValue = $field.find('option:selected').val();
					break;
				case 'TEXTAREA':
					fieldValue = $field.text();
					break;
				default:
					fieldValue = $field.val();
			}

			if((fieldValue == '' || fieldValue == null) && ignoreEmpty) {
				return;
			}

			fieldsData[$field.prop('name')] = fieldValue;
		});

		return fieldsData;
	},

	fillFormFields:
	function(data, prefix, exclude, checkboxAsBoolean) {
		prefix = prefix || '';
		exclude = exclude || [];
		checkboxAsBoolean = checkboxAsBoolean || true;

		var $formFields = $(this).getFormFields(false);

		jQuery.each(data, function (fieldName, fieldValue) {
			var fieldNameFull = fieldName;

			if(prefix != '') {
				fieldNameFull = prefix + '[' + fieldName + ']';
			}

			var $field = $formFields.filter('[name="'+fieldNameFull+'"]');
			if(!$field.length) {
				return;
			}

			if(exclude.indexOf($field.prop('tagName')) != -1) {
				return;
			}


			switch($field.prop('tagName').toLowerCase()) {
				case 'select':
					$field.find('option[value="'+fieldValue+'"]').attr('selected', 'selected');
					break;
				case 'textarea':
					$field.text(fieldValue);
					break;
				default:
					switch($field.prop('type').toLowerCase()) {
						case 'checkbox':
							if(checkboxAsBoolean && fieldValue != '0' && fieldValue !== false && fieldValue != "false" && fieldValue != '') {
								$field.prop('checked', 'checked');
							}
							else {
								$field.val(fieldValue);
							}
							break;
						default:
							$field.val(fieldValue);
					}
			}
		});
	},

	attrEscaped:
	function(attrName, attrValue) {
		if(attrValue != undefined) {
			$(this).attr(attrName, encodeURI(attrValue));

			return $(this);
		}
		else {
			return $(this).attr(decodeURI(attrName));
		}
	},

	attrList:
	function(decode) {
		var decode = decode == undefined ? true : decode;
		var obj = {};
		$.each(this[0].attributes, function() {
			if(this.specified) {
				obj[this.name] = decode ? decodeURI(this.value) : this.value;
			}
		});
		return obj;
	}
});