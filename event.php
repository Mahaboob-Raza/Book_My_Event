<?php
include("includes/db.php");

// Step 1: Mark past events as expired
$conn->query("UPDATE events 
              SET status = 'expired' 
              WHERE date < CURDATE() 
              AND status = 'approved'");

// Step 2: Fetch approved + upcoming events
$sql = "SELECT * FROM events 
        WHERE status = 'approved' 
        AND date >= CURDATE() 
        ORDER BY date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Events - Book My Event</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navbar -->
  <?php include("includes/navbar.php"); ?>

  <!-- Events Section -->
  <div class="container my-5">
    <h2 class="section-title text-center">All Events</h2>
    <div class="row">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-md-4 mb-4">
            <div class="card bme-card h-100">
              <?php if (!empty($row['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                     class="card-img-top" 
                     alt="<?php echo htmlspecialchars($row['title']); ?>">
              <?php endif; ?>

              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <p class="card-text">
                  <strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?><br>
                  <strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?>
                </p>
                <a href="event_detail.php?id=<?php echo $row['event_id']; ?>" 
                   class="btn bme-btn-primary w-100">
                  View Details
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-muted">No approved events found.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
  <?php include("includes/footer.php"); ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
