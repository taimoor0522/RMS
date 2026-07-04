<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Delete Category
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $adn = "DELETE FROM  rpos_product_categories  WHERE  category_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=categories.php");
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
              <a href="add_category.php" class="btn btn-outline-success">
                <i class="fas fa-tags"></i>
                Add New Category
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Category Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Check if table exists
                  $table_check = $mysqli->query("SHOW TABLES LIKE 'rpos_product_categories'");
                  if ($table_check->num_rows == 0) {
                    echo '<tr><td colspan="3" class="text-center">
                      <div class="alert alert-warning">
                        <h4>Category table not found!</h4>
                        <p>Please run the setup script to create the required tables.</p>
                        <a href="setup_supplier_tables.php" class="btn btn-primary">Setup Supplier Tables</a>
                      </div>
                    </td></tr>';
                  } else {
                    $ret = "SELECT * FROM  rpos_product_categories  ORDER BY `rpos_product_categories`.`created_at` DESC ";
                    $stmt = $mysqli->prepare($ret);
                    if ($stmt) {
                      $stmt->execute();
                      $res = $stmt->get_result();
                      if ($res->num_rows > 0) {
                        while ($category = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td><?php echo $category->category_name; ?></td>
                      <td><?php echo $category->category_description ? substr($category->category_description, 0, 50) . '...' : 'N/A'; ?></td>
                      <td>
                        <a href="categories.php?delete=<?php echo $category->category_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                          </button>
                        </a>

                        <a href="update_category.php?update=<?php echo $category->category_id; ?>">
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
                        echo '<tr><td colspan="3" class="text-center text-muted">No categories found. <a href="add_category.php">Add your first category</a></td></tr>';
                      }
                    } else {
                      echo '<tr><td colspan="3" class="text-center text-danger">Error loading categories: ' . $mysqli->error . '</td></tr>';
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
