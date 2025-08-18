<?php
include("includes/db.php");

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $date = $_POST['date'];
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    // File upload handling
    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // image uploaded successfully
        } else {
            $error = "Image upload failed.";
        }
    }

    if (!empty($title) && !empty($date) && !empty($location) && !empty($description)) {
        $sql = "INSERT INTO events (title, date, location, description, image, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $title, $date, $location, $description, $imageName);

        if ($stmt->execute()) {
            $success = "✅ Your event has been submitted for approval!";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    } else {
        $error = "All fields except image are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Submit Event - Book My Event</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include("includes/navbar.php"); ?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Submit Your Event</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Event Title*</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Event Date*</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location*</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description*</label>
            <textarea name="description" rows="4" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Event Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit Event</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
