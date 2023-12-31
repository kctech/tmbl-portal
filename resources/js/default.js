console.log("TMBL Adviser Portal by Perpetual. v1.0");

/*lazy loading*/
(function() {
	var lazy = new LazyLoad({
		elements_selector: ".lazy",
		data_src: "src",
		threshold: 300
	});

	var lazyForced = new LazyLoad({
		elements_selector: ".lazy-force",
		data_src: "src",
		threshold: 9999
	});
}());

$(document).ready(function() {
	$('.tip,[data-toggle="tooltip"]').tooltip();
	formElements();
	/* esc closes modal */
	$(document).bind('keydown', 'esc', function(){
		$('.modal').modal('hide');
	});
	/* enter fires search */
	$('#search').on('keypress',function(e) {
		if(e.which == 13) {								
			$('#_search').val($(this).val());
			$('#filter_form').submit();
		}
	});
	/* meta key actions (save etc) */
	$(window).bind('keydown', function(event) {
    	if (event.ctrlKey || event.metaKey) {
	        switch (String.fromCharCode(event.which).toLowerCase()) {
	        case 's':
	        	/* cmd/ctrl + s */
	            console.log('cmd/ctrl + s');
	            event.preventDefault();
	            $('#form').submit();
	            break;
	        }
	    }
	});
});

function formElements(){
	$('.select2').select2({
		minimumResultsForSearch: 10,
		theme: 'bootstrap4',
		closeOnSelect: true,
		width:'100%'
	});
	$(".select2-tags").select2({
		theme: 'bootstrap4',
		closeOnSelect: true,
		width:'100%',
	    tags: true,
	    tokenSeparators: [',']
	});
	$('.component_datepicker').datepicker({format: 'dd/mm/yyyy',clearBtn: true});
    $('.component_future_datepicker').datepicker({format: 'dd/mm/yyyy',clearBtn: true,startDate: "now",autoclose: true});
    $('.component_past_datepicker').datepicker({format: 'dd/mm/yyyy',clearBtn: true,endDate: "now",autoclose: true});
    $(".component_month_datepicker").datepicker( {
        format: "M yyyy",
        startView: "months",
        minViewMode: "months",
        clearBtn: true,
        endDate: "now",
        autoclose: true
    });
}

function regexValidate(value,type='required'){
    switch(type) {
        case "required":
            var regex = /^\s*$/;
            break;
        case "date":
            /*DOB: dd/mm/yyyy*/
            var regex = /^(?:(?:31(\/)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/;
            break;
        case "time":
            /*Time: hh:mm*/
            var regex = /^([0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
            break;
        case "alphanumeric":
            var regex = /^[a-zA-Z0-9]*$/;
            break;
        case "text":
            var regex = /^[a-zA-Z]*$/;
            break;
        case "numeric":
            var regex = /^[0-9]*$/;
            break;
        case "phone":
            var regex = /^((\+44\s?|0044\s?)?[0123456789]\d{3}|\(?0[0123456789]\d{3}\)?)\s?\d{3}\s?\d{3}$/;
            break;
        case "email":
            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            break;
    }

    if(!regex.test(value)){
        return false;
    }else{
        return true;
    }
}

app = {};

app.alerts = {};

app.formElements = formElements;
app.regexValidate = regexValidate;

app.alerts.confirmDelete = function(form_id,name) {
	swal.fire({
	  title: 'Confirm Deletion',
	  text: "Are you sure you want to delete "+name+'?',
	  type: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#d33',
	  confirmButtonText: 'Confirm'
	}).then((result) => {
	  if (result.value) {
		document.getElementById(form_id).submit();
	  }
	});
	return false;
};

app.alerts.notice = function(title,text) {
	swal.fire({
	  title: title,
	  html: text,
	  type: 'info',
	  showCancelButton: false,
	  confirmButtonColor: '#3fc2ee',
	  confirmButtonText: 'OK'
	});
	return false;
};

app.alerts.response = function(title, text, type="success") {
	swal.fire({
	  title: title,
	  html: text,
	  type: type,
	  showCancelButton: false,
	  confirmButtonColor: '#3fc2ee',
	  confirmButtonText: 'OK',
	  timer: 3000
	});
	return false;
};

app.alerts.toast = function(message, type="success"){
	swal.fire({
	  text: message,
	  type: type,
	  toast: true,
	  position: 'top-end',
	  showConfirmButton: false,
	  timer: 3000
	});
}

app.status = {};
app.status.OK = 0;
app.status.ERROR = 500;
app.status.VALIDATION_ERROR = 400;
app.status.NOT_FOUND = 404;
app.status.PERMISSION_DENIED = 401;

app.modal = {
	load: function(url,title,showPopOutButton=0,size='modal-lg',showFooter=1){
		$('#remoteModal .modal-dialog').removeClass('modal-sm modal-lg modal-xl').addClass(size);
		if(showPopOutButton == 0){
			$('#modal-open-link').hide(0);
		}else{
			$('#modal-open-link').show(0);
		}
		if(showFooter == 0){
			$('#remoteModalFooter').hide(0);
		}else{
			$('#remoteModalFooter').show(0);
		}
		$('#remoteModalTitle').html(title);
		$('#modal-open-link').prop('href',url);
		$('#remoteModalBody').load(url,function() {
			formElements();
			$('#remoteModal').modal('show');
		});
	},
	post: function(url,formId){
		$.post(url,$( "#"+formId ).serialize())
		.done(function(response) {
			response = JSON.parse(response);
			console.log(response);
			if(response.status == app.status.VALIDATION_ERROR){
				$( "#"+formId+" .invalid-feedback" ).remove();
				$( "#"+formId+" .form-control" ).removeClass("is-invalid");
				if(response.validation.length==1){
					$( "#alert-" + formId ).html('<i class="fas fa-exclamation-circle"></i> ' + response.validation[0].value).removeClass("d-none");
				}else{
					var errors = '';
					for(item in response.validation){
						errors = errors + '<li>' + response.validation[item].value + '</li>';
					}
					$( "#alert-" + formId ).html('<i class="fas fa-exclamation-circle"></i> There are ' + response.validation.length + " problems, please review the items below.<ul class=\"mb-0\">" + errors + '</ul>').removeClass("d-none");
				}
				
			
				$("select + span.select2 span.select2-selection").css('border-color','#ced4da');
				$.each(response.validation, function( index,validation_item ) {
					field = validation_item.key;	
					feedback = validation_item.value;	
					/*console.log(value);*/
					$( "#"+formId+" .form-control[name="+field+"]" ).addClass("is-invalid");
					$("select.is-invalid + span.select2 span.select2-selection").css('border-color','#b20000');
				});
				$('.element-scroll-top').animate({ scrollTop: 0 }, 'slow');
				return false;
			}
			if(response.status == app.status.OK){
				if(typeof response.data!='undefined' && typeof response.data.redirect_url!='undefined'){
					window.location.href = response.data.redirect_url;
				}else{
					window.location.reload();
				}
				
				return true;
			}
		});
	}
};

_ticker = null;
_countdown = null;

app.ticktock = function(warn,kill) {
	console.log("Timer Started");
	var countTimer = 0;
		setInterval(function() {
		countTimer++;
		console.log("App running: "+countTimer+"mins, has "+((warn+kill)-countTimer)+"mins left before logout.");
	}, 60000);
	_ticker = setTimeout(function(){ 
		swal.fire({
		  title: 'Inactive Session',
		  html: 'Due to a period of inactivity, your session will be signed out shortly ('+ (kill*60) +'s).<br />Please refresh the page you\'re on to stay logged in.',
		  type: 'warning',
		  showCancelButton: true,
		  cancelButtonText: 'Sign me out now',
		  confirmButtonColor: '#3490dc;',
		  confirmButtonText: 'I don\'t want to go!'
		}).then(function(result) {
			if(result.value){
				clearTimeout(_countdown);
				app.ticktock(warn,kill);
			}else{
				app.logout();
			}
		});
		_countdown = setTimeout(function() {
			app.logout();
		}, kill * 60 * 1000);
	}, warn * 60 * 1000);
};

app.logout = function() {
	var csrf = document.querySelector("meta[name=csrf-token]").getAttribute("content");
	$.post('/admin/logout',{_token:csrf},function() {
		window.location.href='/';
	});
};

app.ticktock_frontend = function(warn,kill) {
	_ticker_frontend = setTimeout(function(){ 
		swal.fire({
		  title: 'Hello?',
		  html: 'This page has been idle for a while, so to protect your privacy the page will redirect soon ('+ (kill*60) +'s)...',
		  type: 'warning',
		  showCancelButton: false,
		  confirmButtonColor: '#3490dc;',
		  confirmButtonText: 'I don\'t want to go!'
		}).then(function(result) {
			if(result.value){
				clearTimeout(_countdown_frontend);
				app.ticktock_frontend(warn,kill);
			}else{
				window.location.href='/idle';
			}
		});
		_countdown_frontend = setTimeout(function() {
			window.location.href='/idle';
		}, kill * 60 * 1000);
	}, warn * 60 * 1000);
};

accounting.settings = {
	currency: {
		symbol : "£", 
		format: "%s%v",
		decimal : ".",
		thousand: ",",
		precision : 2
	},
	number: {
		precision : 0,
		thousand: ",",
		decimal : "."
	}
};