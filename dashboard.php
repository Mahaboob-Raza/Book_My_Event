<?php
session_start();
include("includes/db.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
require 'vendor/autoload.php'; // Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 🔹 Auto-expire events
$conn->query("UPDATE events SET status = 'expired' WHERE status = 'approved' AND date < CURDATE()");

// Handle Approve / Reject / Delete actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "delete") {
        $conn->query("DELETE FROM events WHERE event_id=$event_id");
    } else {
        // Fetch event details BEFORE update
        $result = $conn->query("SELECT organizer_email, title FROM events WHERE event_id=$event_id");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $to = $row['organizer_email'];
            $eventTitle = $row['title'];

            // Update status
            $sql = "UPDATE events SET status=? WHERE event_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $action, $event_id);

            if ($stmt->execute()) {
                // Prepare mail content
                $subject = "Event Status Update: " . $eventTitle;

                if ($action == "approved") {
                    $message = "
                        <h2 style='color:green;'>Event Approved ✅</h2>
                        <p>Your event '<b>{$eventTitle}</b>' has been approved and is now live on our platform.</p>
                        <p>Thank you for choosing <b>Book My Event</b>!</p>
                    ";
                } elseif ($action == "rejected") {
                    $message = "
                        <h2 style='color:red;'>Event Rejected ❌</h2>
                        <p>We’re sorry to inform you that your event '<b>{$eventTitle}</b>' has been rejected.</p>
                        <p>You may contact support for more details.</p>
                    ";
                }

                // Send mail with PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = "chinday101@gmail.com"; // ✅ replace
                    $mail->Password = "ndxs efsy gzvk wood";   // ✅ use App Password
                    $mail->SMTPSecure = "tls";
                    $mail->Port = 587;

                    $mail->setFrom("chinday101@gmail.com", "Book My Event");
                    $mail->addAddress($to);

                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                }
            }
        }
    }

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
        <th>Description</th>
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
                      <td>".htmlspecialchars($row['description'])."</td>
                      <td>
                        <a href='dashboard.php?action=approved&id={$row['event_id']}' class='btn btn-sm btn-success'>Approve</a>
                        <a href='dashboard.php?action=rejected&id={$row['event_id']}' class='btn btn-sm btn-warning'>Reject</a>
                        <a href='dashboard.php?action=delete&id={$row['event_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?');\">Delete</a>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No pending events</td></tr>";
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
        <th>Description</th>
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
                      <td>".htmlspecialchars($row['description'])."</td>
                      <td>
                        <a href='dashboard.php?action=rejected&id={$row['event_id']}' class='btn btn-sm btn-warning'>Reject</a>
                        <a href='dashboard.php?action=delete&id={$row['event_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?');\">Delete</a>
                      </td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No approved events</td></tr>";
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
        <th>Description</th>
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
                      <td>".htmlspecialchars($row['description'])."</td>
                      <td><span class='badge bg-secondary'>Expired</span></td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No expired events</td></tr>";
      }
      ?>
    </tbody>
  </table>

</div>

<?php include("includes/footer.php"); ?>

</body>
</html>
