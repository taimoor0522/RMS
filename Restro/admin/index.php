<?php
session_start();
include('config/config.php');

// LOGIN LOGIC (PLAIN TEXT - FOR UNI PROJECT)
if (isset($_POST['login'])) {

  $admin_email = $_POST['admin_email'];
  $admin_password = $_POST['admin_password']; // NO HASHING

  $stmt = $mysqli->prepare(
    "SELECT admin_id 
     FROM rpos_admin 
     WHERE admin_email = ? AND admin_password = ?"
  );

  $stmt->bind_param('ss', $admin_email, $admin_password);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($admin_id);
    $stmt->fetch();

    $_SESSION['admin_id'] = $admin_id;
    header("Location: dashboard.php");
    exit();
  } else {
    $err = "Incorrect Authentication Credentials";
  }
}

require_once('partials/_head.php');
?>

<body class="bg-dark">
  <div class="main-content">
    <div class="header bg-gradient-primary py-7">
      <div class="container">
        <div class="header-body text-center mb-7">
          <h1 class="text-white">Restaurant Point Of Sale System</h1>
        </div>
      </div>
    </div>

    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary shadow border-0">
            <div class="card-body px-lg-5 py-lg-5">

              <?php if (isset($err)) { ?>
                <div class="alert alert-danger text-center">
                  <?php echo $err; ?>
                </div>
              <?php } ?>

              <form method="post">
                <div class="form-group mb-3">
                  <input class="form-control" required
                         name="admin_email"
                         placeholder="Email"
                         type="email">
                </div>

                <div class="form-group">
                  <input class="form-control" required
                         name="admin_password"
                         placeholder="Password"
                         type="password">
                </div>

                <div class="text-center">
                  <button type="submit"
                          name="login"
                          class="btn btn-primary my-4">
                    Log In
                  </button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require_once('partials/_footer.php'); ?>
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
