<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
//Update Item
if (isset($_POST['updateItem'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["item_code"]) || empty($_POST["item_name"]) || empty($_POST['category_id']) || empty($_POST['supplier_id']) || empty($_POST['item_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $item_code  = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $item_description = $_POST['item_description'];
    $item_price = $_POST['item_price'];
    $item_unit = $_POST['item_unit'];
    $item_stock = $_POST['item_stock'];
    $item_min_stock = $_POST['item_min_stock'];
    $update = $_GET['update'];

    //Insert Captured information to a database table
    $postQuery = "UPDATE rpos_supplier_items SET item_code =?, item_name =?, category_id =?, supplier_id =?, item_description =?, item_price =?, item_unit =?, item_stock =?, item_min_stock =? WHERE item_id =?";
    $postStmt = $mysqli->prepare($postQuery);
    //bind paramaters
    $rc = $postStmt->bind_param('sssssdsiis', $item_code, $item_name, $category_id, $supplier_id, $item_description, $item_price, $item_unit, $item_stock, $item_min_stock, $update);
    $postStmt->execute();
    //declare a varible which will be passed to alert function
    if ($postStmt) {
      $success = "Item Updated" && header("refresh:1; url=supplier_items.php");
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
    $update = $_GET['update'] ?? '';
    
    // Check if table exists
    $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_supplier_items'");
    if ($table_check->num_rows == 0 || empty($update)) {
      echo '<div class="container-fluid mt--8">
        <div class="alert alert-warning">
          <h4>Table not found or invalid item ID!</h4>
          <p>Please run the setup script to create the required tables.</p>
          <a href="setup_supplier_tables.php" class="btn btn-primary">Setup Supplier Tables</a>
          <a href="supplier_items.php" class="btn btn-secondary">Back to Items</a>
        </div>
      </div>';
      require_once('partials/_footer.php');
      require_once('partials/_scripts.php');
      exit;
    }
    
    $ret = "SELECT * FROM  rpos_supplier_items WHERE item_id = ? ";
    $stmt = $mysqli->prepare($ret);
    if (!$stmt) {
      echo '<div class="container-fluid mt--8">
        <div class="alert alert-danger">
          <h4>Error loading item!</h4>
          <p>' . $mysqli->error . '</p>
          <a href="supplier_items.php" class="btn btn-secondary">Back to Items</a>
        </div>
      </div>';
      require_once('partials/_footer.php');
      require_once('partials/_scripts.php');
      exit;
    }
    $stmt->bind_param('s', $update);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 0) {
      echo '<div class="container-fluid mt--8">
        <div class="alert alert-warning">
          <h4>Item not found!</h4>
          <a href="supplier_items.php" class="btn btn-secondary">Back to Items</a>
        </div>
      </div>';
      require_once('partials/_footer.php');
      require_once('partials/_scripts.php');
      exit;
    }
    $item = $res->fetch_object();
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
                      <label>Item Name</label>
                      <input type="text" name="item_name" value="<?php echo $item->item_name; ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                      <label>Item Code</label>
                      <input type="text" name="item_code" value="<?php echo $item->item_code; ?>" class="form-control" required>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Category</label>
                      <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php
                        $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_product_categories'");
                        if ($table_check->num_rows > 0) {
                          $ret = "SELECT * FROM rpos_product_categories";
                          $stmt2 = $mysqli->prepare($ret);
                          if ($stmt2) {
                            $stmt2->execute();
                            $res2 = $stmt2->get_result();
                            while ($cat = $res2->fetch_object()) {
                              $selected = ($cat->category_id == $item->category_id) ? 'selected' : '';
                              echo "<option value='$cat->category_id' $selected>$cat->category_name</option>";
                            }
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label>Supplier</label>
                      <select name="supplier_id" class="form-control" required>
                        <option value="">Select Supplier</option>
                        <?php
                        $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_suppliers'");
                        if ($table_check->num_rows > 0) {
                          $ret = "SELECT * FROM rpos_suppliers WHERE supplier_status = 'Active'";
                          $stmt3 = $mysqli->prepare($ret);
                          if ($stmt3) {
                            $stmt3->execute();
                            $res3 = $stmt3->get_result();
                            while ($sup = $res3->fetch_object()) {
                              $selected = ($sup->supplier_id == $item->supplier_id) ? 'selected' : '';
                              echo "<option value='$sup->supplier_id' $selected>$sup->supplier_name</option>";
                            }
                          }
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-4">
                      <label>Price</label>
                      <input type="number" step="0.01" name="item_price" value="<?php echo $item->item_price; ?>" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label>Unit</label>
                      <input type="text" name="item_unit" value="<?php echo $item->item_unit; ?>" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                      <label>Stock</label>
                      <input type="number" name="item_stock" value="<?php echo $item->item_stock; ?>" class="form-control" required>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Minimum Stock</label>
                      <input type="number" name="item_min_stock" value="<?php echo $item->item_min_stock; ?>" class="form-control" required>
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-12">
                      <label>Description</label>
                      <textarea rows="5" name="item_description" class="form-control"><?php echo $item->item_description; ?></textarea>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="updateItem" value="Update Item" class="btn btn-success">
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
      ?>
      </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>

</html>
