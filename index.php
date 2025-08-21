<?php
include("includes/db.php");

// Always use the latest query only (removed duplicate)
$sql = "SELECT * FROM events WHERE status = 'approved' ORDER BY date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book My Event</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts (optional for modern look) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include("includes/navbar.php"); ?>

<!-- Hero Section -->
<div class="bme-hero">
  <div class="container">
    <h1 class="bme-hero-title">Find and Book Your Next Event</h1>
    <p class="bme-hero-subtitle">Discover amazing events happening near you and secure your spot today.</p>
    <a href="#events" class="btn bme-btn-primary btn-lg rounded-pill">Explore Events</a>
  </div>
</div>

<!-- Events Section -->
<div class="container my-5" id="events">
    <h2 class="text-center section-title">Upcoming Events</h2>

    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x200" 
                                 class="card-img-top" 
                                 alt="No image">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title bme-event-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text bme-event-text"><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                            <p class="card-text bme-event-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                            <p class="card-text bme-event-text text-truncate">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-0">
                            <a href="event_detail.php?id=<?php echo $row['event_id']; ?>" 
                               class="btn bme-btn-primary btn-lg w-100 rounded-pill">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No upcoming events at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>