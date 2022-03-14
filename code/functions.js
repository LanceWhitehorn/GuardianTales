function csrf() {
	$(document).ready(function() {
		$.ajax ({
			type: 'GET',
			url: 'csrf_token.php',
			dataType: 'html',
			success: function(data) {
				$('#csrf_token').val(data);
			}
		});
	});
}
function filter(str) {
	$('#data').html('');
	$('#dropdown').prop('disabled', false);
	var token = $('#csrf_token').val();
	if (str=='') {
		$('#dropdown').html('');
	} else {
		$(document).ready(function() {
			$.ajax ({
				type: 'GET',
				headers: {'token': token},
				url: `dropdown.php?filter=${str}`,
				dataType: 'html',
				success: function(data) {
					$('#dropdown').html(data);
				}
			});
		});
	}
}
function showTable(str) {
	if (str=='') {
		$('#data').html('');
	} else {
		var filter = $('input[name="filter"]:checked').val();
		var token = $('#csrf_token').val();
		$(document).ready(function() {
			$.ajax ({
				type: 'GET',
				headers: {'token': token},
				url: `getData.php?filter=${filter}&id=${str}`,
				dataType: 'html',
				success: function(data) {
					$('#data').html(data);
				}
			})
		})
	}
}
function customReset() {
	$('#dropdown').prop('disabled', true);
	$('#dropdown').html('<option>Select an option above</option>');
	$('input[name="filter"]').prop('checked', false);
	$('#data').html('');
}


/*
This is the usual JS way of doing things

function showTable(str) {
	if (str=='') {
		$('#data').html('');
		return;
	} else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState==4 && this.status==200) {
				document.getElementById("data").innerHTML = this.responseText;
			}
		};
		var filter = document.querySelector('input[name="filter"]:checked').value;
		var csrf_token = document.getElementById("csrf_token").value;
		xmlhttp.open("GET", `getData.php?filter=${filter}&id=${str}&token=${csrf_token}`, true);
		xmlhttp.send();
	}
}
*/