<?php 
include 'includes/db_connect.php'; 
// Fetch all books initially
$sql = "SELECT * FROM books WHERE stock_qty > 0 ORDER BY title ASC"; 
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Literary Hub - Book Catalog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📖 The Literary Hub</h1>
        <a href="admin_login.php" class="button">Admin Login</a>
    </header>

    <div class="container">
        <h2>Our Latest Books</h2>
        <input type="text" id="searchInput" placeholder="Search by Title, Author, or ISBN..." onkeyup="liveSearch()">
        
        <div class="book-catalog" id="bookCatalog">
            <?php
            // Initial load of books from PHP
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='book-item'>";
                    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                    echo "<p><strong>Author:</strong> " . htmlspecialchars($row["author"]) . "</p>";
                    echo "<p><strong>Price:</strong> $" . htmlspecialchars($row["price"]) . "</p>";
                    echo "<p>Stock: " . htmlspecialchars($row["stock_qty"]) . "</p>";
                    // Placeholder for ordering (requires a separate 'order.php' file for completeness)
                    echo "<a href='#' class='button'>Order Now</a>"; 
                    echo "</div>";
                }
            } else {
                echo "<p style='grid-column: 1 / -1; text-align: center;'>No books currently in stock.</p>";
            }
            ?>
        </div>
    </div>
    
    <script>
    // --- JavaScript for Live Search (AJAX) ---
    function liveSearch() {
        let input = document.getElementById('searchInput').value;
        let catalog = document.getElementById('bookCatalog');
        
        // Use the Fetch API to asynchronously request search results from search_books.php
        fetch('search_books.php?query=' + encodeURIComponent(input))
            .then(response => response.text()) 
            .then(html => {
                catalog.innerHTML = html; // Dynamically update the catalog area
            })
            .catch(err => console.error('Error fetching data:', err));
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>