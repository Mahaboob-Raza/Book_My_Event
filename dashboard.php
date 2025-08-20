<?php
session_start();
include("includes/db.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// 🔹 Auto-expire events (approved but past date)
$conn->query("UPDATE events SET status = 'expired' WHERE status = 'approved' AND date < CURDATE()");

// 🔹 Handle Approve / Reject / Delete actions
if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
    $event_id = intval($_REQUEST['id']);
    $action = $_REQUEST['action'];

    if ($action === "delete") {
        // Delete event permanently
        $sql = "DELETE FROM events WHERE event_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
    } else {
        // Approve / Reject / Expire
        $sql = "UPDATE events SET status=? WHERE event_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $action, $event_id);
        $stmt->execute();

        // Fetch organizer email
        $result = $conn->query("SELECT organizer_email, title FROM events WHERE event_id=$event_id");
        if ($result && $row = $result->fetch_assoc()) {
            $to = $row['organizer_email'];
            $subject = "Event Status Update: " . $row['title'];

            if ($action == "approved") {
                $message = "✅ Your event '" . $row['title'] . "' has been approved!";
            } elseif ($action == "rejected") {
                $message = "❌ Your event '" . $row['title'] . "' has been rejected.";
            } elseif ($action == "expired") {
                $message = "⚠️ Your event '" . $row['title'] . "' has expired.";
            }

            // Send email
            $headers = "From: no-reply@bookmyevent.page.gd";
            @mail($to, $subject, $message, $headers);
        }
    }

    // Refresh after action
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("includes/navbar.php"); ?>

<div class="container my-5">
  <h2 class="mb-4 text-center">Admin Dashboard</h2>
  <p class="text-end"><a href="logout.php" class="btn btn-danger btn-sm">Logout</a></p>

  <!-- Pending Events -->
  <h3>Pending Events</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $pending = $conn->query("SELECT * FROM events WHERE status='pending' ORDER BY date ASC");
      if ($pending->num_rows > 0) {
          while ($row = $pending->fetch_assoc()) {
              echo "<tr>
                      <td>".htmlspecialchars($row['title'])."</td>
                      <td>".htmlspecialchars($row['date'])."</td>
                      <td>".htmlspecialchars($row['location'])."</td>
                      <td>
                        <a href='dashboard.php?action=approved&id={$row['event_id']}' class='btn btn-sm btn-success'>Approve</a>
                        <a href='dashboard.php?action=rejected&id={$row['event_id']}' class='btn btn-sm btn-warning'>Reject</a>
                        <a href='dashboard.php?action=delete&id={$row['event_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?');\">Delete</a>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No pending events</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Approved Events -->
  <h3>Approved Events</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $approved = $conn->query("SELECT * FROM events WHERE status='approved' ORDER BY date ASC");
      if ($approved->num_rows > 0) {
          while ($row = $approved->fetch_assoc()) {
              echo "<tr>
                      <td>".htmlspecialchars($row['title'])."</td>
                      <td>".htmlspecialchars($row['date'])."</td>
                      <td>".htmlspecialchars($row['location'])."</td>
                      <td>
                        <a href='dashboard.php?action=rejected&id={$row['event_id']}' class='btn btn-sm btn-warning'>Reject</a>
                        <a href='dashboard.php?action=delete&id={$row['event_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?');\">Delete</a>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No approved events</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Expired Events -->
  <h3>Expired Events</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $expired = $conn->query("SELECT * FROM events WHERE status='expired' ORDER BY date DESC");
      if ($expired->num_rows > 0) {
          while ($row = $expired->fetch_assoc()) {
              echo "<tr>
                      <td>".htmlspecialchars($row['title'])."</td>
                      <td>".htmlspecialchars($row['date'])."</td>
                      <td>".htmlspecialchars($row['location'])."</td>
                      <td><span class='badge bg-secondary'>Expired</span></td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No expired events</td></tr>";
      }
      ?>
    </tbody>
  </table>

</div>

<?php include("includes/footer.php"); ?>

</body>
</html>
