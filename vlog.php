<?php
include 'db.php';
include 'footer_header.html';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vlog Page</title>
    <!-- Link the external CSS file -->
    <link rel="stylesheet" href="vlog.css">
</head>
<body>

<!-- Dark Mode Toggle Button -->

<div class="vlog-carousel-container">
    <!-- Image Gallery -->
    <div class="vlog-gallery" id="vlog-gallery">
        <?php
        include 'vlog_data.php'; // Separate PHP file for data fetching
        foreach ($vlogItems as $item) {
            echo '<div class="vlog-item">';
            echo '<img src="' . $item['image'] . '" alt="Vlog Image">';
            echo '<div class="vlog-description">' . $item['description'] . '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>
<script>
    function shuffleImages() {
        const gallery = document.querySelector('.vlog-gallery');
        const items = Array.from(gallery.children);
        // Shuffle the items array
        items.sort(() => Math.random() - 0.5);
        // Clear the gallery and append shuffled items
        gallery.innerHTML = '';
        items.forEach(item => gallery.appendChild(item));
    }

    // Shuffle images every 5 seconds (5000 milliseconds)
    setInterval(shuffleImages, 5000);
</script>

<!-- JavaScript for Dark Mode -->
<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }
</script>
<script>

        // Dark mode toggle functionality if needed
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                // Save preference in local storage
                if (document.body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    localStorage.removeItem('darkMode');
                }
            });
        }

        // Check local storage for dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
