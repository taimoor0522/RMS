<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

// Get staff_id from session (cashier always has staff_id)
if (isset($_SESSION['staff_id']) && !empty($_SESSION['staff_id'])) {
    $staff_id = $_SESSION['staff_id'];
} else {
    $err = "Staff ID not found. Please login again.";
    $staff_id = null;
}

// Create Order
if (isset($_POST['createOrder'])) {
  if (empty($_POST["supplier_id"]) || empty($_POST['item_id']) || empty($_POST['item_qty'])) {
    $err = "Please Fill All Required Fields";
  } elseif ($staff_id === null) {
    $err = "Cannot create order: Staff ID not found. Please login again.";
  } else {
    $order_id = bin2hex(random_bytes('5'));
    $order_code = $alpha . '-' . $beta;
    $supplier_id = $_POST['supplier_id'];
    $item_id = $_POST['item_id'];
    $item_qty = $_POST['item_qty'];
    $order_notes = $_POST['order_notes'];
    
    // Get item details
    $ret = "SELECT * FROM rpos_supplier_items WHERE item_id = '$item_id'";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    $item = $res->fetch_object();
    
    $item_name = $item->item_name;
    $item_price = $item->item_price;
    $order_total = $item_price * $item_qty;
    
    //Insert Order
    $postQuery = "INSERT INTO rpos_supplier_orders (order_id, order_code, supplier_id, staff_id, item_id, item_name, item_qty, item_price, order_total, order_status, order_notes) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
    $postStmt = $mysqli->prepare($postQuery);
    $order_status = 'Pending';
    $rc = $postStmt->bind_param('sssisiddsss', $order_id, $order_code, $supplier_id, $staff_id, $item_id, $item_name, $item_qty, $item_price, $order_total, $order_status, $order_notes);
    $postStmt->execute();
    
    if ($postStmt) {
      $success = "Order Created Successfully" && header("refresh:1; url=supplier_orders.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}

// Update Order Status
if (isset($_POST['updateStatus'])) {
  $order_id = $_POST['order_id'];
  $order_status = $_POST['order_status'];
  
  $postQuery = "UPDATE rpos_supplier_orders SET order_status =? WHERE order_id =?";
  $postStmt = $mysqli->prepare($postQuery);
  $rc = $postStmt->bind_param('ss', $order_status, $order_id);
  $postStmt->execute();
  
  if ($postStmt) {
    $success = "Order Status Updated" && header("refresh:1; url=supplier_orders.php");
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
    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Create Order Form -->
      <div class="row mb-4">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Create New Order to Supplier</h3>
            </div>
            <div class="card-body">
              <form method="POST" id="orderForm">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required onchange="loadSupplierItems()">
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
                            $selected = (isset($_GET['supplier_id']) && $_GET['supplier_id'] == $sup->supplier_id) ? 'selected' : '';
                            echo "<option value='$sup->supplier_id' $selected>$sup->supplier_name</option>";
                          }
                        }
                      } else {
                        echo "<option value=''>Please contact administrator</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label>Item</label>
                    <select name="item_id" id="item_id" class="form-control" required>
                      <option value="">Select Supplier First</option>
                    </select>
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Quantity</label>
                    <input type="number" name="item_qty" id="item_qty" class="form-control" min="1" required>
                  </div>
                  <div class="col-md-6">
                    <label>Notes</label>
                    <input type="text" name="order_notes" class="form-control" placeholder="Optional notes for supplier">
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="createOrder" value="Create Order" class="btn btn-success">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Orders List -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Supplier Orders</h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Order Code</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Item</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT so.*, s.supplier_name, st.staff_name 
                          FROM rpos_supplier_orders so 
                          LEFT JOIN rpos_suppliers s ON so.supplier_id = s.supplier_id 
                          LEFT JOIN rpos_staff st ON so.staff_id = st.staff_id 
                          ORDER BY so.created_at DESC";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($order = $res->fetch_object()) {
                    $statusClass = '';
                    if ($order->order_status == 'Pending') $statusClass = 'warning';
                    elseif ($order->order_status == 'Confirmed') $statusClass = 'info';
                    elseif ($order->order_status == 'Delivered') $statusClass = 'success';
                    elseif ($order->order_status == 'Cancelled') $statusClass = 'danger';
                  ?>
                    <tr>
                      <td><?php echo $order->order_code; ?></td>
                      <td><?php echo $order->supplier_name; ?></td>
                      <td><?php echo $order->item_name; ?></td>
                      <td><?php echo $order->item_qty; ?></td>
                      <td>Rs. <?php echo number_format($order->item_price, 2); ?></td>
                      <td>Rs. <?php echo number_format($order->order_total, 2); ?></td>
                      <td>
                        <span class="badge badge-<?php echo $statusClass; ?>">
                          <?php echo $order->order_status; ?>
                        </span>
                      </td>
                      <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                      <td>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#statusModal<?php echo $order->order_id; ?>">
                          <i class="fas fa-edit"></i>
                          Update Status
                        </button>
                      </td>
                    </tr>
                    
                    <!-- Status Update Modal -->
                    <div class="modal fade" id="statusModal<?php echo $order->order_id; ?>" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Update Order Status</h5>
                            <button type="button" class="close" data-dismiss="modal">
                              <span>&times;</span>
                            </button>
                          </div>
                          <form method="POST">
                            <div class="modal-body">
                              <input type="hidden" name="order_id" value="<?php echo $order->order_id; ?>">
                              <div class="form-group">
                                <label>Status</label>
                                <select name="order_status" class="form-control" required>
                                  <option value="Pending" <?php echo $order->order_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                  <option value="Confirmed" <?php echo $order->order_status == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                  <option value="Delivered" <?php echo $order->order_status == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                  <option value="Cancelled" <?php echo $order->order_status == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" name="updateStatus" class="btn btn-primary">Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
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
  
  <script>
    function loadSupplierItems() {
      var supplierId = document.getElementById('supplier_id').value;
      var itemSelect = document.getElementById('item_id');
      
      if (supplierId) {
        itemSelect.innerHTML = '<option value="">Loading...</option>';
        
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'supplier_items_ajax.php?supplier_id=' + supplierId, true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
            itemSelect.innerHTML = xhr.responseText;
          }
        };
        xhr.send();
      } else {
        itemSelect.innerHTML = '<option value="">Select Supplier First</option>';
      }
    }
    
    <?php if (isset($_GET['supplier_id'])) { ?>
      window.onload = function() {
        loadSupplierItems();
      };
    <?php } ?>
  </script>
</body>

</html>
