<?php
session_start();
include("includes/db.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $event = null; // No event ID
} else {
    $event_id = intval($_GET['id']);
    $_SESSION['event_id'] = $event_id; // Store for redirect after Google login

    // Step 1: Expire event if past its date
    $sql_expire = "UPDATE events SET status = 'expired' WHERE event_id = ? AND date < CURDATE() AND status = 'approved'";
    $stmt_expire = $conn->prepare($sql_expire);
    $stmt_expire->bind_param("i", $event_id);
    $stmt_expire->execute();

    // Step 2: Fetch event details (only approved & not expired)
    $sql = "SELECT * FROM events WHERE event_id = ? AND status = 'approved'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $event = null; // Event not found or not available
    } else {
        $event = $result->fetch_assoc();
    }
}

$success = "";
$error = "";

// Handle registration only if event is valid & not expired
if ($event && $_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $tickets = intval($_POST['tickets']);

    if (!empty($name) && !empty($email)) {
        $sql_insert = "INSERT INTO registrations (event_id, name, email, phone, tickets) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isssi", $event_id, $name, $email, $phone, $tickets);

        if ($stmt_insert->execute()) {
            $success = "You have successfully registered for this event!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Name and Email are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
    <?php echo $event ? htmlspecialchars($event['title']) : "Event Not Available"; ?> - Book My Event
</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include("includes/navbar.php"); ?>

<div class="container my-5">
    <?php if (!$event): ?>
        <!-- Friendly Alert if Event Not Found or Pending -->
        <div class="alert alert-warning text-center">
            🚫 This event is not available right now. Please check back later.
        </div>
    <?php else: ?>
    <div class="row">
        <!-- Event Details -->
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($event['title']); ?></h2>
            <?php if (!empty($event['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" 
                     class="img-fluid mb-3" alt="<?php echo htmlspecialchars($event['title']); ?>">
            <?php endif; ?>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
        </div>

        <!-- Registration Form -->
        <div class="col-md-4">
            <h4>Register for this Event</h4>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name*</label>
                    <input type="text" name="name" class="form-control"
                           value="<?php echo $_SESSION['name'] ?? ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email*</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo $_SESSION['email'] ?? ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tickets</label>
                    <input type="number" name="tickets" class="form-control" value="1" min="1">
                </div>

                <div class="mb-3">
                    <a href="google_login.php" class="btn btn-danger w-100 mb-3">
                        Continue with Google
                    </a>
                </div>

                <button type="submit" class="btn bme-btn-primary w-100">Register</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
