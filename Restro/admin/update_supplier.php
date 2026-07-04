<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
//Update Supplier
if (isset($_POST['updateSupplier'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["supplier_code"]) || empty($_POST["supplier_name"]) || empty($_POST['supplier_email']) || empty($_POST['supplier_phone'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $supplier_code  = $_POST['supplier_code'];
    $supplier_name = $_POST['supplier_name'];
    $supplier_email = $_POST['supplier_email'];
    $supplier_phone = $_POST['supplier_phone'];
    $supplier_address = $_POST['supplier_address'];
    $supplier_contact_person = $_POST['supplier_contact_person'];
    $supplier_status = $_POST['supplier_status'];
    $update = $_GET['update'];

    //Insert Captured information to a database table
    $postQuery = "UPDATE rpos_suppliers SET supplier_code =?, supplier_name =?, supplier_email =?, supplier_phone =?, supplier_address =?, supplier_contact_person =?, supplier_status =? WHERE supplier_id =?";
    $postStmt = $mysqli->prepare($postQuery);
    //bind paramaters
    $rc = $postStmt->bind_param('ssssssss', $supplier_code, $supplier_name, $supplier_email, $supplier_phone, $supplier_address, $supplier_contact_person, $supplier_status, $update);
    $postStmt->execute();
    //declare a varible which will be passed to alert function
    if ($postStmt) {
      $success = "Supplier Updated" && header("refresh:1; url=suppliers.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    $update = $_GET['update'];
    $ret = "SELECT * FROM  rpos_suppliers WHERE supplier_id = '$update' ";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($supplier = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body">
          </div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <!-- Table -->
        <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3>Please Fill All Fields</h3>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Supplier Name</label>
                      <input type="text" name="supplier_name" value="<?php echo $supplier->supplier_name; ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label>Supplier Code</label>
                      <input type="text" name="supplier_code" value="<?php echo $supplier->supplier_code; ?>" class="form-control" required>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Email</label>
                      <input type="email" name="supplier_email" value="<?php echo $supplier->supplier_email; ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label>Phone</label>
                      <input type="text" name="supplier_phone" value="<?php echo $supplier->supplier_phone; ?>" class="form-control" required>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Contact Person</label>
                      <input type="text" name="supplier_contact_person" value="<?php echo $supplier->supplier_contact_person; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Status</label>
                      <select name="supplier_status" class="form-control" required>
                        <option value="Active" <?php echo $supplier->supplier_status == 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo $supplier->supplier_status == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                      </select>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-12">
                      <label>Address</label>
                      <textarea rows="3" name="supplier_address" class="form-control"><?php echo $supplier->supplier_address; ?></textarea>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="updateSupplier" value="Update Supplier" class="btn btn-success">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
      <?php
      require_once('partials/_footer.php');
    }
      ?>
      </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>

</html>
