<?php
    ini_set('display_errors',1);
    ini_set('error_reporting', E_ALL);
    error_reporting(-1);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include_once('CSRF.php');
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Login page</title>
    <style> 
    #resetPassword
    {
        margin-top: 1em;
    }
</style>
  </head>
  <body>
      <div class="container">
          <div class="row">
              <div class="col-xs-12 center-block text-center">
                  <h1>Login</h1>
              </div>
          </div>
      </div>

    <form id="login" method="POST" action="auth-process.php?action=<?php echo ($action = 'login'); ?>" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="Email" class="form-label">Email </label>
            <input type="email" class="form-control" id="Email" name="Email" required>
        </div>
        <div class="mb-3">
            <label for="Password" class="form-label">Password</label>
            <input type="password" class="form-control" id="Password" name="Password" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />
    </form>

    <a id="resetPassword" href="resetPassword.php" class="btn btn-dark">Reset Password</a>



    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  </body>
</html>