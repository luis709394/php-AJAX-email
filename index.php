<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Untitled Document</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

		<script>
			/*
			 * This function validates form input. If input is valid, sends the email
			 */
			function validateAndSubmit() {
				var pattEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

				var from = document.getElementById("from").value;

				// check the validity of the receiver addresses
				var tos = new Array();
				tos = document.getElementById("to").value.split(";");
				var toAddressErr = 0;
				// set the number of address error to 0

				$.each(tos, function(index, value) {
					if (!pattEmail.test(value.trim())) {++toAddressErr;
					}
				});

				var subject = document.getElementById("subject").value.trim();

				//  Verify the sender address and receiver address using REGEX

				var resultOfFrom = pattEmail.test(from);

				// if email addresses are valid and subject is not empty, send email
				if (resultOfFrom && !toAddressErr && subject) {
					sendEmail(event);
				} else {//  if not  notice that the input is invalid
					// Stop event propagation
					event.stopPropagation();
					// prevent default form submit
					event.preventDefault();
					// set the resut text to notify error
					$('#result').text("invalid input!");
					$('#result').css("color", "red");

				}

			}

			/*
			 * This function uploads the files and collects the input from the the form,
			 * then send email viaAJAX
			 */
			function sendEmail(event) {

				// Stop the event from bubbling up the DOM
				event.stopPropagation();
				// prevent the default form submitting
				event.preventDefault();

				// Create a FormData object to save the form input (variables and files)
				var data = new FormData();

				// add the attached files to the data
				$('#form').find('input[type=file]').each(function(i) {
					if (this.files[0]) {
						data.append(this.files[0]["name"], this.files[0]);
					}
				});

				// add other data ($msg, $send, $from) to the formdata object
				var otherData = $('#form').serializeArray();
				$.each(otherData, function(key, input) {
					data.append(input.name, input.value);
				});

				// extract the url from form
				var formURL = $('#form').attr("action");

				//  perform AJAX
				$.ajax({
					url : formURL,
					type : 'POST',
					data : data,
					cache : false,
					processData : false, // Don't process the files
					contentType : false, // Set content type to false as jQuery will tell the server its a query string request
					success : function(msg) {
						// set the feedback message to the result text
						$('#result').html(msg);
						$('#result').css("color", "black"); //make the text black

					},
					error : function(e) {
						// show the error message if AJAX failed
						$("#result").text("failed: " + e);
						$('#result').css("color", "red"); //make the text red

					}
				});

			}

			/*
			 * bind the ValidateAndSubmit() to the form when DOM is loaded
			 */
			$(document).ready(function() {

				$('#form').on('submit', validateAndSubmit);

			});

		</script>
	</head>

	<body>

		<h2>Send An Email </h2>
		<form name="form" id="form"  action="sendEmail.php"
		method="post" enctype="multipart/form-data" >
			From:
			<input type="text" name="from" id="from">
			<br>
			To:
			<input type="text" name="to" id="to">
			<br>
			Cc:
			<input type="text" name="cc" id="cc">
			<br>
			Subject:
			<input type="text" name="subject" id="subject">
			<br>
			Message: 			<textarea name="msg" rows="10" cols="50" id="msg">
	</textarea>
			<br>
			Attachment:
			<input type="file" name="file1"  id="file">
			<br>
			Attachment:
			<input type="file" name="file2"  id="file">
			<br>
			Attachment:
			<input type="file" name="file3"  id="file">
			<br>
			<input type="submit" id='submit'>
		</form>

		<p>
			<span id='result'></span>
		</p>
	</body>
</html>
