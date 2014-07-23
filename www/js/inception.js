$(function () {
	initCommon();
	initCommonUI();


	function initCommonUI() {
		/**
		 * Submit main form via external button click
		 */
		$(document).on('click', '.main-form-submit', function (e) {
			$('#mainForm').submit();
		});


		/**
		 * Confirm delete entity
		 */
		$(document).on('click', '.delete-entity', function (e) {
			return confirm('Remove object?');
		});

		/**
		 * Modal popup on 'shown'
		 */
		$(document).on('show shown', '.modal', function (e) {
			var $modal = $(e.target);

			$modal.css({
				'marginLeft': - ($modal.outerWidth() / 2)
			});
		});

		/**
		 * This code is to be removed once
		 * bootstrap fixed its modal scrolling issues
		 */
		$('.modal')
			.on('shown', function(e){
				$('body').css('overflow', 'hidden');
			})
			.on('hidden', function(e){
				$('body').css('overflow', 'visible');
			});
		/*********************************************/
	}


	function initCommon()
	{
		$('.datetime-picker').datetimepicker({
			weekStart: 1
		}).on('changeDate', function(e){
			$(this).datetimepicker('hide');
		});
		
		$('.date-picker').datetimepicker({
			pickTime: false,
			weekStart: 1
		}).on('changeDate', function(e){
			$(this).datetimepicker('hide');
		});

		$('.time-picker').datetimepicker({
			language: 'ru-RU',
			pickDate: false
		});
	}
});


/****************************************************/
/**************** DOCUMENT READY MISC ***************/
/****************************************************/
$(function(e){
	/**
	 * JS Links
	 **/
	$(document).on('click', 'a.jsLink', function(e){
		e.preventDefault();
	});
});


function log($val)
{
	if(console != undefined && console.log != undefined) {
		console.log($val);
	}
}


function isEmptyObject(obj) {
	var name;
	for (name in obj) {
		return false;
	}
	return true;
}

function objectSize(obj) {
	var name;
	var size = 0;
	for (name in obj) {
		size++;
	}
	return size;
}

function countObjectNonEmptyFields(obj) {
	var name;
	var size = 0;
	for (name in obj) {

		if(obj[name] && !isEmptyObject(obj[name])) {
			size++;
		}
	}
	return size;
}

function getPriceFormatted(price) {
	var parts = price.toFixed(2).split(".");
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
	return parts.join(",");
}