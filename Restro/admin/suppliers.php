<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Delete Supplier
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $adn = "DELETE FROM  rpos_suppliers  WHERE  supplier_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=suppliers.php");
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
              <a href="add_supplier.php" class="btn btn-outline-success">
                <i class="fas fa-truck"></i>
                Add New Supplier
              </a>
              <a href="categories.php" class="btn btn-outline-info ml-2">
                <i class="fas fa-tags"></i>
                Manage Categories
              </a>
              <a href="supplier_items.php" class="btn btn-outline-primary ml-2">
                <i class="fas fa-box"></i>
                Manage Items
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Supplier Name</th>
                    <th scope="col">Contact Person</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Check if table exists, if not show setup message
                  $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_suppliers'");
                  if ($table_check->num_rows == 0) {
                    echo '<tr><td colspan="7" class="text-center">
                      <div class="alert alert-warning">
                        <h4>Supplier tables not found!</h4>
                        <p>Please run the setup script to create the required tables.</p>
                        <a href="setup_supplier_tables.php" class="btn btn-primary">Setup Supplier Tables</a>
                      </div>
                    </td></tr>';
                  } else {
                    $ret = "SELECT * FROM  rpos_suppliers  ORDER BY `rpos_suppliers`.`created_at` DESC ";
                    $stmt = $mysqli->prepare($ret);
                    if ($stmt) {
                      $stmt->execute();
                      $res = $stmt->get_result();
                      if ($res->num_rows > 0) {
                        while ($supplier = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td><?php echo $supplier->supplier_code; ?></td>
                      <td><?php echo $supplier->supplier_name; ?></td>
                      <td><?php echo $supplier->supplier_contact_person ? $supplier->supplier_contact_person : 'N/A'; ?></td>
                      <td><?php echo $supplier->supplier_email; ?></td>
                      <td><?php echo $supplier->supplier_phone; ?></td>
                      <td>
                        <span class="badge badge-<?php echo $supplier->supplier_status == 'Active' ? 'success' : 'danger'; ?>">
                          <?php echo $supplier->supplier_status; ?>
                        </span>
                      </td>
                      <td>
                        <a href="suppliers.php?delete=<?php echo $supplier->supplier_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                          </button>
                        </a>

                        <a href="update_supplier.php?update=<?php echo $supplier->supplier_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                            Update
                          </button>
                        </a>
                        <a href="supplier_orders.php?supplier_id=<?php echo $supplier->supplier_id; ?>">
                          <button class="btn btn-sm btn-info">
                            <i class="fas fa-shopping-cart"></i>
                            Order Items
                          </button>
                        </a>
                      </td>
                    </tr>
                  <?php 
                        }
                      } else {
                        echo '<tr><td colspan="7" class="text-center text-muted">No suppliers found. <a href="add_supplier.php">Add your first supplier</a></td></tr>';
                      }
                    } else {
                      echo '<tr><td colspan="7" class="text-center text-danger">Error loading suppliers: ' . $mysqli->error . '</td></tr>';
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
