var disable_alarm = false;
var g_submitting="请等一下。";

// customizing buttong click
$.fn.aclick = function(callback) {
	$(this).click(function(e) {
		if ($(this).hasAttr('disabled')) return;
		this.cbClick = callback;
		this.cbClick(e);
	});
};
$.fn.aenable = function() {
	$(this).removeAttr('disabled');
};
$.fn.adisable = function() {
	$(this).attr('disabled', 'disabled');
};
$.fn.hasAttr = function(attr) {
	var obj = $(this)[0];
	return obj.hasAttribute ? obj.hasAttribute(attr) : obj.getAttribute(attr);
};
$.fn.isChecked = function() {
	var obj = $(this)[0];
	return obj.checked;
};

if (!String.prototype.trim) {
    String.prototype.trim = function trim() {
        return this.toString().replace(/^([\s\t\n]*)|([\s\t\n]*)$/g, '');
    };
} 
if (!String.prototype.strip_space) {
    String.prototype.strip_space = function trim() {
        return this.toString().replace(/([\s\t\n]+)/g, ' ');
    };
} 

var isIE67 = false;

$(function() {
	isIE67 = $.browser.msie && $.browser.version <= 7;

	/*
	 * MASKING
	 * Dependency: js/plugin/masked-input/
	 */
	if ($.fn.mask) {
		$('[data-mask]').each(function() {
			$this = $(this);
			var mask = $this.attr('data-mask') || 'error...', mask_placeholder = $this.attr('data-mask-placeholder') || 'X';

			$this.mask(mask, {
				placeholder : mask_placeholder
			});
		});
	}

	init_data_sort();

	if (!disable_alarm)
	{
		set_access();
	}
	
	// reset bookmark url
	$('a').each(function() {
		var href = $(this).attr('href');
		
		if (href != null && href.indexOf('#') == 0 && href.length > 1) {
			$(this).attr('href', document.location.href + href);
		}
	});
});
	
// custom validator
jQuery.validator.addMethod("isAfterThan", function(value, element, param) {
	var target=$(param);
	if (value == "")
		return true;
	return new Date(value) > new Date(target.val());
});

jQuery.validator.addMethod("bigThan", function(value, element, param) {
	var target=$(param);
	if(this.settings.onfocusout){
		target.unbind(".validate-bigThan").bind("blur.validate-bigThan",function(){$(element).valid();});
	}
	if (value == "")
		return true;
	return parseFloat(value) > parseFloat(target.val());
});

jQuery.validator.addMethod("smallThan", function(value, element, param) {
	var target=$(param);
	if(this.settings.onfocusout){
		target.unbind(".validate-bigThan").bind("blur.validate-bigThan",function(){$(element).valid();});
	}
	if (value == "")
		return true;
	return parseFloat(value) < parseFloat(target.val());
});

jQuery.validator.addMethod("imagefile", function(value, element, param) {
	var found = value.match(/\.(jpg|jpeg|png)$/gi);
	return found != null;
});

jQuery.validator.addMethod("pwd_strength", function(value, element, param) {
	var e = value.match(/([A-Z])/gi);
	var n = value.match(/([0-9])/gi);
	var s = value.match(/([^A-Z0-9])/gi);
	return this.optional( element ) || e != null && n != null && s != null;
});

jQuery.validator.addMethod('unique_login_id', function(value, element) {
	var ret = false;

	App.callAPI("api/common/is_exist", {
        login_id: value
    }, false).done(function(res) {
        if (res.err_code == 0) {
            ret = !res.is_exist;
        }
    });

	return ret;
});

jQuery.validator.addMethod('mobile', function(value, element) {	
	return this.optional( element ) || /^191\-{0,1}\d{3}\-{0,1}\d{4}$/.test( value );
});

jQuery.validator.addMethod('tel', function(value, element) {	
	return this.optional( element ) || /^(\(*02\)*){0,1}\d{3}\-{0,1}\d{4}$/.test( value );
});

jQuery.validator.addMethod('requiredOr', function(value, element, param) {	
	var check = $.trim(value).length > 0;
	if (check) 
		return true;
	
	if (param) {
		for (i = 0; i < param.length; i ++) {
			var value = $(param[i]).val();

			var check = $.trim(value).length > 0;
			if (check) 
				return true;
		}
	}

	return false;
});

jQuery.validator.addMethod('requireCheck', function(value, element, param ) {
	// check if dependency is met
	if ( !this.depend( param, element ) ) {
		return "dependency-mismatch";
	}
	if ( element.nodeName.toLowerCase() === "select" ) {
		// could be an array for select-multiple or a string, both are fine this way
		var val = $( element ).val();
		return val && val.length > 0;
	}
	if ( this.checkable( element ) ) {
		return this.getLength( value, element ) > 0;
	}
	return $.trim( value ).length > 0;
});

jQuery.validator.addMethod("requireRating", function(value, element, param) {
	return value > 0;
});

function clearValidate(form)
{
	$(form + ' .control-group .help-block').remove();
	$(form + ' .help-block-error').remove();
	$(form + ' .control-group.has-error').removeClass('has-error');
	$(form + ' .control-group.has-success').removeClass('has-success');
}

function getValidationRules () {
	var custom = {
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,

		invalidHandler: function (event, validator) { //display error alert on form submit              
            /*success1.hide();
            error1.show();
            Metronic.scrollTo(error1, -200);*/
        },

        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.form-group').addClass('has-error'); // set error class to the control group
        },

        unhighlight: function (element) { // revert the change done by hightlight
            $(element)
                .closest('.form-group').removeClass('has-error'); // set error class to the control group

           	id = $(element).attr('id');
           	name = $(element).attr('name');
           	$('#' + id + '-error,#' + name + '-error').each(function() {
            		if ($(this).text() == '') {
            			$(this).remove();
            		}
            	});
        },

        errorPlacement: function (error, element) { // render error placement for each input type
        	if (element.siblings(".help-block-default").size() > 0) {
        		element.siblings(".help-block-default").hide();
        		error.insertAfter(element.siblings(".help-block-default"));
        	} else if (element.siblings(".form-control-focus").size() > 0) {
        		error.insertAfter(element.siblings(".form-control-focus"));
        	} else if (element.parent(".input-group").size() > 0) {
                error.insertAfter(element.parent(".input-group"));
            } else if (element.attr("data-error-container")) { 
                error.appendTo(element.attr("data-error-container"));
            } else if (element.parents('.radio-list').size() > 0) { 
                error.appendTo(element.parents('.radio-list').parent());
            } else if (element.parents('.radio-inline').size() > 0) { 
                error.appendTo(element.parents('.radio-inline'));
            } else if (element.parents('.checkbox-list').size() > 0) {
                error.appendTo(element.parents('.checkbox-list'));
            } else if (element.parents('.checkbox-inline').size() > 0) { 
                error.appendTo(element.parents('.checkbox-inline'));
            } else if (element.parent('.intl-tel-input').size() > 0) { 
                error.insertAfter(element.parent('.intl-tel-input'));
            } else {
                error.insertAfter(element); // for other inputs, just perform default behavior
            }
        },

        success: function (label) {
            label
                .closest('.form-group').removeClass('has-error'); // set success class to the control group

            label
                .closest('.form-group').find('.help-block-error').removeClass('help-block-error'); 

            label
                .closest('.form-group').find('.help-block-default').show();
        }
        	/*,
        submitHandler: function (form) {
            success1.show();
            error1.hide();
        }	*/
	};

	return custom;
}

function init_data_sort()
{
	$('[data-sort]').click(function() {
		var field = $(this).attr('data-sort');
		var f = $('#sort_field').val();
		if (f == field)
		{
			var o = $('#sort_order').val();
			if (o == "" || o == "DESC")
				$('#sort_order').val("ASC");
			else
				$('#sort_order').val("DESC");
		}
		else {
			$('#sort_field').val(field);
			$('#sort_order').val('ASC');
		}
		$(this).parents('form').submit();
	});
}

function alertBox(title, message, callback, tout)
{
	if (tout == null)
		tout = 1500;
	$.smallBox({
		title : title,
		content : message,
		color : "#3ca0ef",
		timeout: tout,
		icon : "icon-check"
	}, function() { if (callback != null) callback(); });
}

function errorBox(title, message, callback, tout)
{
	if (tout == null)
		tout = 10000;
	$.smallBox({
		title : title,
		content : message,
		color : "#e35555",
		timeout: tout,
		icon : "icon-info"
	}, function() { if (callback != null) callback(); });
}

function confirmBox(title, message, onYes, onNo)
{
	$.SmartMessageBox({
		title : title,
		content : message,
		buttons : '[确定][取消]'
	}, function(ButtonPressed) {
		if (ButtonPressed == "确定" && onYes != null) {
			onYes();
		}
		if (ButtonPressed == "取消" && onNo != null) {
			onNo();
		}
	});
}

function confirmInputBox(title, message, placeholder, onYes, onNo)
{
	$.SmartMessageBox({
		title : title,
		content : message,
		input : "text",
		placeholder : placeholder,
		buttons : '[确定][取消]'
	}, function(ButtonPressed, Value) {
		if (ButtonPressed == "确定" && onYes != null) {
			onYes(Value);
		}
		if (ButtonPressed == "取消" && onNo != null) {
			onNo(Value);
		}
	});
}

function confirmTextarea(title, message, placeholder, value, onYes, onNo)
{
	$.SmartMessageBox({
		title : title,
		content : message,
		input : "textarea",
		placeholder : placeholder,
		value : value,
		buttons : '[确定][取消]'
	}, function(ButtonPressed, Value) {
		if (ButtonPressed == "确定" && onYes != null) {
			onYes(Value);
		}
		if (ButtonPressed == "取消" && onNo != null) {
			onNo(Value);
		}
	});
}

function confirmSelectBox(title, message, placeholder, options, onYes, onNo)
{
	$.SmartMessageBox({
		title : title,
		content : message,
		input : "select",
		options : options,
		placeholder : placeholder,
		buttons : '[确定][取消]'
	}, function(ButtonPressed, Value) {
		if (ButtonPressed == "确定" && onYes != null) {
			onYes(Value);
		}
		if (ButtonPressed == "取消" && onNo != null) {
			onNo(Value);
		}
	});
}

function confirmSelectInputBox(title, message, placeholder, options, onYes, onNo)
{
	$.SmartMessageBox({
		title : title,
		content : message,
		input : "select|input",
		options : options,
		placeholder : placeholder,
		buttons : '[确定][取消]'
	}, function(ButtonPressed, Value) {
		if (ButtonPressed == "确定" && onYes != null) {
			onYes(Value);
		}
		if (ButtonPressed == "取消" && onNo != null) {
			onNo(Value);
		}
	});
}

function confirmPasswordBox(title, message, onYes, onNo)
{
	$.SmartMessageBox({
		title : title,
		content : message,
		input : "password",
		buttons : '[确定][取消]'
	}, function(ButtonPressed, Value) {
		if (ButtonPressed == "确定" && onYes != null) {
			onYes(Value);
		}
		if (ButtonPressed == "取消" && onNo != null) {
			onNo(Value);
		}
	});
}

/* Date Formatting
-----------------------------------------------------------------------------*/
// TODO: use same function formatDate(date, [date2], format, [options])
var date_defaults = {
	// time formats
	titleFormat: {
		month: 'MMMM yyyy',
		week: "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}",
		day: 'dddd, MMM d, yyyy'
	},
	columnFormat: {
		month: 'ddd',
		week: 'ddd M/d',
		day: 'dddd M/d'
	},
	timeFormat: { // for event elements
		'': 'hh:mm' // default
	},
	
	// locale
	isRTL: false,
	firstDay: 0,
	monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
	monthNamesShort: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
	dayNames: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
	dayNamesShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
	buttonText: {
		prev: '&nbsp;&#9668;&nbsp;',
		next: '&nbsp;&#9658;&nbsp;',
		prevYear: '&nbsp;&lt;&lt;&nbsp;',
		nextYear: '&nbsp;&gt;&gt;&nbsp;',
		today: 'today',
		month: 'month',
		week: 'week',
		day: 'day'
	}	
};

function formatDate(date, format, options) {
	return formatDates(date, null, format, options);
}


function formatDates(date1, date2, format, options) {
	options = options || date_defaults;
	var date = date1,
		otherDate = date2,
		i, len = format.length, c,
		i2, formatter,
		res = '';
	for (i=0; i<len; i++) {
		c = format.charAt(i);
		if (c == "'") {
			for (i2=i+1; i2<len; i2++) {
				if (format.charAt(i2) == "'") {
					if (date) {
						if (i2 == i+1) {
							res += "'";
						}else{
							res += format.substring(i+1, i2);
						}
						i = i2;
					}
					break;
				}
			}
		}
		else if (c == '(') {
			for (i2=i+1; i2<len; i2++) {
				if (format.charAt(i2) == ')') {
					var subres = formatDate(date, format.substring(i+1, i2), options);
					if (toInt(subres.replace(/\D/, ''), 10)) {
						res += subres;
					}
					i = i2;
					break;
				}
			}
		}
		else if (c == '[') {
			for (i2=i+1; i2<len; i2++) {
				if (format.charAt(i2) == ']') {
					var subformat = format.substring(i+1, i2);
					var subres = formatDate(date, subformat, options);
					if (subres != formatDate(otherDate, subformat, options)) {
						res += subres;
					}
					i = i2;
					break;
				}
			}
		}
		else if (c == '{') {
			date = date2;
			otherDate = date1;
		}
		else if (c == '}') {
			date = date1;
			otherDate = date2;
		}
		else {
			for (i2=len; i2>i; i2--) {
				if (formatter = dateFormatters[format.substring(i, i2)]) {
					if (date) {
						res += formatter(date, options);
					}
					i = i2 - 1;
					break;
				}
			}
			if (i2 == i) {
				if (date) {
					res += c;
				}
			}
		}
	}
	return res;
};


var dateFormatters = {
	s	: function(d)	{ return d.getSeconds() },
	ss	: function(d)	{ return zeroPad(d.getSeconds()) },
	m	: function(d)	{ return d.getMinutes() },
	mm	: function(d)	{ return zeroPad(d.getMinutes()) },
	h	: function(d)	{ return d.getHours() % 12 || 12 },
	hh	: function(d)	{ return zeroPad(d.getHours() % 12 || 12) },
	H	: function(d)	{ return d.getHours() },
	HH	: function(d)	{ return zeroPad(d.getHours()) },
	d	: function(d)	{ return d.getDate() },
	dd	: function(d)	{ return zeroPad(d.getDate()) },
	ddd	: function(d,o)	{ return o.dayNamesShort[d.getDay()] },
	dddd: function(d,o)	{ return o.dayNames[d.getDay()] },
	M	: function(d)	{ return d.getMonth() + 1 },
	MM	: function(d)	{ return zeroPad(d.getMonth() + 1) },
	MMM	: function(d,o)	{ return o.monthNamesShort[d.getMonth()] },
	MMMM: function(d,o)	{ return o.monthNames[d.getMonth()] },
	yy	: function(d)	{ return (d.getFullYear()+'').substring(2) },
	yyyy: function(d)	{ return d.getFullYear() },
	t	: function(d)	{ return d.getHours() < 12 ? 'a' : 'p' },
	tt	: function(d)	{ return d.getHours() < 12 ? 'am' : 'pm' },
	T	: function(d)	{ return d.getHours() < 12 ? 'A' : 'P' },
	TT	: function(d)	{ return d.getHours() < 12 ? 'AM' : 'PM' },
	u	: function(d)	{ return formatDate(d, "yyyy-MM-dd'T'HH:mm:ss'Z'") },
	S	: function(d)	{
		var date = d.getDate();
		if (date > 10 && date < 20) {
			return 'th';
		}
		return ['st', 'nd', 'rd'][date%10-1] || 'th';
	}
};

function zeroPad(n) {
	return (n < 10 ? '0' : '') + n;
}

function diff_times(start/*YY:mm*/, end/*YY:mm*/) {
	try
	{
		if (start == "" || end == "")
		{
			return '';
		}
		var s = start.split(':');
		var e = end.split(':');
		s = toInt(s[0], 10) * 60 + toInt(s[1], 10);
		e = toInt(e[0], 10) * 60 + toInt(e[1], 10);
		d = e - s;
		return zeroPad(Math.floor(d / 60)) + ":" + zeroPad(d % 60);
	}
	catch (e)
	{
	}
}

function goto_url(url)
{
	base_url = $('base').attr('href');
	if (url.charAt(0) != '/' && url.charAt(0) != '.')
	{
		document.location = base_url + url;
	}
}

/* access related */
var first_access = 1;
function set_access()
{
	/*
	operation = $('h2:first').text();

	$.ajax({
		url :"batch/access/" + first_access,
		type : "post",
		dataType : 'json',
		data : {
			operation : operation,
			url: document.location.href
		},
		success : function(ret) {
			if (ret.err_code == 0 && ret.alerts != null && ret.alerts.length > 0)
			{
				for (i = 0; i < ret.alerts.length; i ++)
				{
					msg = ret.alerts[i];
					alertBox(msg.title, msg.body, null, 30000);
				}
			}
		},
		error : function() {
		},
		complete : function() {
		}
	});

	if (first_access == 1)
	{
		first_access = 0;
	}
	setTimeout("set_access()", 60000);
	*/
}

/* get server time now */
function get_servertime(callback) {
	$.ajax({
		url :"common/now_ajax",
		type : "post",
		dataType : 'json',
		success : function(ret) {
			if (ret.err_code == 0)
			{
				callback(ret.now);
			}
		},
		error : function() {
		},
		complete : function() {
		}
	});
}

var g_first_refresh = true;
function page_refresh(refresh_url, refresh_obj, callback, step) {
	if (g_first_refresh == false)
	{
		$.ajax({
			url : refresh_url,
			type : "post",
			success : function(data){ 
				$(refresh_obj).html(data);
				eval(callback);
			},
			error : function() {
			},
			complete : function() {
			}
		}); 
	}
	else {
		eval(callback);
	}
	if (step == undefined)
		step_seconds = 10000;
	else
		step_seconds = step * 1000;

	if (step_seconds > 0)
		setTimeout("page_refresh('" + refresh_url + "','" + refresh_obj + "','" + callback + "'," + step +")", step_seconds);
	g_first_refresh = false;
}

function toInt(n)
{
	return parseInt(n, 10);
}

function showMask(msg)
{
	if (msg === null || msg === undefined)
		msg = g_submitting;

	if (msg !== "")
		msg = '<h2>' + msg + '</h2>';

	hideMask();
	var mask = $('<table class="submit-mask"><tr><td align="center" valign="middle">' 
		+ msg + '</td></tr></table>');
	mask.width($(window).width());
	mask.height($(window).height());
	$('body').append(mask);
}

function hideMask()
{
	$('.submit-mask').remove();
}

function modalAlert(title, message, callback)
{
	title = title || "提示";

	tmpl = '<table class="modal-alert-box-container"><tr><td align="center" valign="middle">';
	tmpl +=  '<section class="modal-alert-box">';
	tmpl += ' <header>' + title + '</header>';
	tmpl += ' <div class="body">' + message + '</div>';
	tmpl += ' <footer>';
	tmpl += '  <button id="modal_alert_ok" type="button" class="btn btn-primary">确定</button>';
	tmpl += ' </footer>';
	tmpl += '</section>';
	tmpl += '</td></tr></table>';

	showMask("");

	var box = $(tmpl);
	box.width($(window).width());
	box.height($(window).height());
	$('body').append(box);

	$('#modal_alert_ok').click(function() {
		hideMask();
		$('.modal-alert-box-container').remove();
		callback();
	});
}

function getFileExtension(file_name)
{
	var fs = file_name.split('.');

	if (fs.length < 2)
		return "";

	return fs[fs.length - 1].toLowerCase(); 
}

function checkFileExtension(file_name, extensions)
{
	ext = getFileExtension(file_name);

	for (var i = 0; i < extensions.length; i ++)
	{
		if (ext == extensions[i]) {
			return true;
		}
	}

	return false;
}

function downLink(path, file_name, file_size)
{
	var down_url = "common/down/" + path + "/" + file_name;

	lnk = "";
	if (checkFileExtension(file_name, ["jpg", "jpeg", "png", "bmp", "gif", "tiff"])) {
		var thumb = "common/down/" + path + "_thumb.jpg/" + file_name;
		return "<a href='" + down_url + "' class='attach fancybox' fancy-width='max' fancy-height='max' title='" + file_name + " (" + file_size + ")'><img src='" + thumb + "'></a>";
	}
	else if (checkFileExtension(file_name, ["doc", "docx"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-word-o'></i></a>";
	}
	else if (checkFileExtension(file_name, ["xls", "xlsx"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-excel-o'></i></a>";
	}
	else if (checkFileExtension(file_name, ["ppt", "pptx"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-powerpoint-o'></i></a>";
	}
	else if (checkFileExtension(file_name, ["pdf"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-pdf-o'></i></a>";
	}
	else if (checkFileExtension(file_name, ["avi", "mpg", "mp4", "mov"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-movie-o'></i></a>";
	}
	else if (checkFileExtension(file_name, ["wav", "mp3", "ogg", "midi"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-sound-o'></i></a>";
	}
	else if (checkFileExtension(file_name, ["rar", "zip", "7z"])) {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-archive-o'></i></a>";
	}
	else {
		lnk += "<a href='" + down_url + "' class='attach' title='" + file_name + " (" + file_size + ")'><i class='fa fa-file-text-o'></i></a>";
	}

	lnk += "<a href='" + down_url + "' class='file_name'>" + file_name + " (" + file_size + ")'</a>";

	return lnk;

}

function numberFormat(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}