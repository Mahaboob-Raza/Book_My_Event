<?php
session_start();
include("includes/db.php");

// 🔒 Ensure only logged-in admins can access
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle actions (Approve/Reject/Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "approve") {
        $sql = "UPDATE events SET status = 'approved' WHERE event_id = ?";
    } elseif ($action === "reject") {
        $sql = "UPDATE events SET status = 'rejected' WHERE event_id = ?";
    } elseif ($action === "delete") {
        $sql = "DELETE FROM events WHERE event_id = ?";
    }

    if (isset($sql)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        if ($stmt->execute()) {
            $msg = ucfirst($action) . "d successfully!";
        } else {
            $msg = "Error while performing action.";
        }
        header("Location: dashboard.php?msg=" . urlencode($msg));
        exit;
    }
}

// Fetch all events
$sql = "SELECT * FROM events ORDER BY date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php include("includes/navbar.php"); ?>

<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Admin Dashboard</h2>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']); ?></div>
  <?php endif; ?>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['event_id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td>
              <?php if ($row['status'] === 'approved'): ?>
                <span class="badge bg-success">Approved</span>
              <?php elseif ($row['status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php else: ?>
                <span class="badge bg-danger">Rejected</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="dashboard.php?action=approve&id=<?= $row['event_id'] ?>" class="btn btn-sm btn-success">Approve</a>
              <a href="dashboard.php?action=reject&id=<?= $row['event_id'] ?>" class="btn btn-sm btn-warning">Reject</a>
              <a href="dashboard.php?action=delete&id=<?= $row['event_id'] ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center">No events found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include("includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
