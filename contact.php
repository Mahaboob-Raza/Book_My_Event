<?php include("includes/navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Developer - Book My Event</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="contact-card p-4 shadow rounded text-center">
                <img src="assests/images/my_img.jpg" alt="Developer Picture" class="developer-pic mb-3">
                <h2 class="mb-3">👨‍💻 Developer Contact</h2>
                <p class="text-muted mb-4">
                    Hi, I'm <strong>Raza</strong>, the developer of <b>Book My Event</b>.  
                    If you face issues, have suggestions, or want to collaborate, feel free to reach me below.
                </p>

                <div class="contact-info text-start mx-auto" style="max-width:400px;">
                    <p><strong>Email:</strong> <a href="mailto:mahaboobraza446@gmail.com">mahaboobraza446@gmail.com</a></p>
                    <p><strong>Phone:</strong> <a href="tel:+917866009730">+91 7866009730</a></p>
                    <p><strong>LinkedIn:</strong> <a href="https://www.linkedin.com/in/mahaboob-raza-9518b7359/" target="_blank">linkedin/mahaboob-raza</a></p>
                    <p><strong>GitHub:</strong> <a href="https://github.com/Mahaboob-Raza" target="_blank">github.com/Mahaboob-Raza</a></p>
                </div>

                <hr class="my-4">

                <p class="text-muted">Or drop me a quick message:</p>
                <form>
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Your Email" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="btn bme-btn-primary w-100">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
