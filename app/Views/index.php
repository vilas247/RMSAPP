<!--
 * This file is part of the 247Commerce BigCommerce RETAIL MERCHANT App.
 *
 * ©247 Commerce Limited <info@247commerce.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.83.1">
    <title>Login</title>

    <!-- Bootstrap core CSS -->
    <link href="<?= getenv('app.ASSETSPATH') ?>css/bootstrap.min.css" rel="stylesheet">

    <!-- font-awesome css-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

    <link href="<?= getenv('app.ASSETSPATH') ?>css/style.css" rel="stylesheet">
    <link href="<?= getenv('app.ASSETSPATH') ?>css/247carstreamloader.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

  </head>
  <body>
    <main class="main-content">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-8 col-lg-6 offset-lg-3 offset-md-2">
            <div class="col-sm-6 mx-auto col-6">
                <img src="<?= getenv('app.ASSETSPATH') ?>img/card-saver.png">
            </div>
            <div class="login-form pg-30">
              <div class="text-center py-3">
                <h3>Getting Started</h3>
                <h5>Login to the dashboard</h5>
              </div>
              <form class="form-horizontal" id="validateForm" action="<?= getenv('app.baseURL') ?>settings/updatePaymetDetails" method="POST" >
                <div class="mb-3 input-group input-height">
                  <input type="text" class="form-control" id="confi-id" name="merchant_id" required placeholder="Configuration ID">
                </div>
                <div class="mb-3 input-height" id="show_hide_password">
                  <input type="password" class="form-control" id="key" name="cardstream_signature" required placeholder="Current API Key">
                </div>
                <div class="mb-3 input-group">
                  <small><b>How can I </b><a href="#">get my client id, client secret and terminal id?</a></small>
                </div>
                <button type="submit" class="btn btn-primary d-block w-100 btn-lg">Submit</button>
              </form>
            </div>
          </div>        
        </div>
      </div>
    </main>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/jquery-min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/bootstrap.min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?= getenv('app.ASSETSPATH') ?>js/247carstreamloader.js"></script>
    <script type="text/javascript">
		$(document).ready(function() {
			var text = "Please wait...";
			var current_effect = "bounce";
			$("#show_hide_password a").on('click', function(event) {
				event.preventDefault();
				if($('#show_hide_password input').attr("type") == "text"){
					  $('#show_hide_password input').attr('type', 'password');
					  $('#show_hide_password i').addClass( "fa-eye-slash" );
					  $('#show_hide_password i').removeClass( "fa-eye" );
				}else if($('#show_hide_password input').attr("type") == "password"){
					  $('#show_hide_password input').attr('type', 'text');
					  $('#show_hide_password i').removeClass( "fa-eye-slash" );
					  $('#show_hide_password i').addClass( "fa-eye" );
				}
			});
			$('body').on('submit','#validateForm',function(e){
				$("body").waitMe({
					effect: current_effect,
					text: text,
					bg: "rgba(255,255,255,0.7)",
					color: "#000",
					maxSize: "",
					waitTime: -1,
					source: "images/img.svg",
					textPos: "vertical",
					fontSize: "",
					onClose: function(el) {}
				});
			});
		});
    </script>
  </body>
</html>
