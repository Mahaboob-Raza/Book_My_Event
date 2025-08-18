<?php
include("includes/db.php");

// Fetch only approved events
$sql = "SELECT * FROM events WHERE status = 'approved' ORDER BY date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Events - Book My Event</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include("includes/navbar.php"); ?>

<div class="container my-5">
    <h2>All Events</h2>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['title']); ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="card-text">
                        <strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?><br>
                        <strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?>
                    </p>
                    <a href="event_detail.php?id=<?php echo $row['event_id']; ?>" class="btn bme-btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <?php
            endwhile;
        } else {
            echo "<p>No approved events found.</p>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
