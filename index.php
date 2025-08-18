<?php
include("includes/db.php");
$sql = "SELECT * FROM events ORDER BY date ASC";
$sql = "SELECT * FROM events WHERE status = 'approved' ORDER BY date ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book My Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include("includes/navbar.php"); ?>

<!-- Hero Section -->
<div class="bme-hero">
    <div class="container">
        <h1 class="bme-hero-title">Find and Book Your Next Event</h1>
        <p class="bme-hero-subtitle">Discover amazing events happening near you and secure your spot today.</p>
    </div>
</div>

<!-- Events Section -->
<div class="container my-5" id="events">
    <h2 class="text-center mb-4">Upcoming Events</h2>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/400x200" class="card-img-top" alt="No image">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title bme-event-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text bme-event-text"><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                    <p class="card-text bme-event-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p class="card-text bme-event-text"><?php echo substr(htmlspecialchars($row['description']), 0, 80); ?>...</p>
                </div>
                <div class="card-footer bg-white">
                    <a href="event_detail.php?id=<?php echo $row['event_id']; ?>" class="btn bme-btn-primary w-100">View Details</a>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p class='text-center'>No upcoming events at the moment.</p>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
