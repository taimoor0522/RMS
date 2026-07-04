<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Delete Item
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $adn = "DELETE FROM  rpos_supplier_items  WHERE  item_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=supplier_items.php");
  } else {
    $err = "Try Again Later";
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
              <a href="add_supplier_item.php" class="btn btn-outline-success">
                <i class="fas fa-box"></i>
                Add New Item
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Item Code</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Price</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Unit</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Check if table exists
                  $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_supplier_items'");
                  if ($table_check->num_rows == 0) {
                    echo '<tr><td colspan="8" class="text-center">
                      <div class="alert alert-warning">
                        <h4>Supplier items table not found!</h4>
                        <p>Please run the setup script to create the required tables.</p>
                        <a href="setup_supplier_tables.php" class="btn btn-primary">Setup Supplier Tables</a>
                      </div>
                    </td></tr>';
                  } else {
                    $ret = "SELECT si.*, c.category_name, s.supplier_name 
                            FROM rpos_supplier_items si 
                            LEFT JOIN rpos_product_categories c ON si.category_id = c.category_id 
                            LEFT JOIN rpos_suppliers s ON si.supplier_id = s.supplier_id 
                            ORDER BY si.created_at DESC";
                    $stmt = $mysqli->prepare($ret);
                    if ($stmt) {
                      $stmt->execute();
                      $res = $stmt->get_result();
                      if ($res->num_rows > 0) {
                        while ($item = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td><?php echo $item->item_code; ?></td>
                      <td><?php echo $item->item_name; ?></td>
                      <td><?php echo $item->category_name; ?></td>
                      <td><?php echo $item->supplier_name; ?></td>
                      <td>Rs. <?php echo number_format($item->item_price, 2); ?></td>
                      <td>
                        <span class="badge badge-<?php echo $item->item_stock <= $item->item_min_stock ? 'danger' : 'success'; ?>">
                          <?php echo $item->item_stock; ?>
                        </span>
                      </td>
                      <td><?php echo $item->item_unit; ?></td>
                      <td>
                        <a href="supplier_items.php?delete=<?php echo $item->item_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                          </button>
                        </a>

                        <a href="update_supplier_item.php?update=<?php echo $item->item_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                            Update
                          </button>
                        </a>
                      </td>
                    </tr>
                  <?php 
                        }
                      } else {
                        echo '<tr><td colspan="8" class="text-center text-muted">No items found. <a href="add_supplier_item.php">Add your first item</a></td></tr>';
                      }
                    } else {
                      echo '<tr><td colspan="8" class="text-center text-danger">Error loading items: ' . $mysqli->error . '</td></tr>';
                    }
                  }
                  ?>
                </tbody>
              </table>
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
