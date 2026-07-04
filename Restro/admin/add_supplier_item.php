<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['addItem'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["item_code"]) || empty($_POST["item_name"]) || empty($_POST['category_id']) || empty($_POST['supplier_id']) || empty($_POST['item_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $item_id = bin2hex(random_bytes('5'));
    $item_code  = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $item_description = $_POST['item_description'];
    $item_price = $_POST['item_price'];
    $item_unit = $_POST['item_unit'];
    $item_stock = $_POST['item_stock'];
    $item_min_stock = $_POST['item_min_stock'];
    
    //Insert Captured information to a database table
    $postQuery = "INSERT INTO rpos_supplier_items (item_id, item_code, item_name, category_id, supplier_id, item_description, item_price, item_unit, item_stock, item_min_stock) VALUES(?,?,?,?,?,?,?,?,?,?)";
    $postStmt = $mysqli->prepare($postQuery);
    //bind paramaters
    $rc = $postStmt->bind_param('ssssssdsii', $item_id, $item_code, $item_name, $category_id, $supplier_id, $item_description, $item_price, $item_unit, $item_stock, $item_min_stock);
    $postStmt->execute();
    //declare a varible which will be passed to alert function
    if ($postStmt) {
      $success = "Item Added" && header("refresh:1; url=supplier_items.php");
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
                    <input type="text" name="item_name" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label>Item Code</label>
                    <input type="text" name="item_code" value="<?php echo isset($alpha) && isset($beta) ? $alpha . '-' . $beta : ''; ?>" class="form-control" required>
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
                        $stmt = $mysqli->prepare($ret);
                        if ($stmt) {
                          $stmt->execute();
                          $res = $stmt->get_result();
                          while ($cat = $res->fetch_object()) {
                            echo "<option value='$cat->category_id'>$cat->category_name</option>";
                          }
                        }
                      } else {
                        echo "<option value=''>Please setup tables first</option>";
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
                        $stmt = $mysqli->prepare($ret);
                        if ($stmt) {
                          $stmt->execute();
                          $res = $stmt->get_result();
                          while ($sup = $res->fetch_object()) {
                            echo "<option value='$sup->supplier_id'>$sup->supplier_name</option>";
                          }
                        }
                      } else {
                        echo "<option value=''>Please setup tables first</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-4">
                    <label>Price</label>
                    <input type="number" step="0.01" name="item_price" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label>Unit</label>
                    <input type="text" name="item_unit" value="unit" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label>Stock</label>
                    <input type="number" name="item_stock" value="0" class="form-control" required>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Minimum Stock</label>
                    <input type="number" name="item_min_stock" value="0" class="form-control" required>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-12">
                    <label>Description</label>
                    <textarea rows="5" name="item_description" class="form-control"></textarea>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addItem" value="Add Item" class="btn btn-success">
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
