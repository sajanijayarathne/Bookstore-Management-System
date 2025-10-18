<?php
include 'includes/db_connect.php';

// Get the search query from the GET request
$query = $_GET['query'] ?? '';

// Sanitize and prepare the search string for SQL LIKE operator
$search = "%" . $conn->real_escape_string($query) . "%";

// SQL for searching by title or author
$sql = "SELECT * FROM books WHERE (title LIKE '$search' OR author LIKE '$search' OR isbn LIKE '$search') AND stock_qty > 0 ORDER BY title ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output the HTML for the filtered results (matches the structure in index.php)
    while($row = $result->fetch_assoc()) {
        echo "<div class='book-item'>";
        echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
        echo "<p><strong>Author:</strong> " . htmlspecialchars($row["author"]) . "</p>";
        echo "<p><strong>Price:</strong> $" . htmlspecialchars($row["price"]) . "</p>";
        echo "<p>Stock: " . htmlspecialchars($row["stock_qty"]) . "</p>";
        echo "<a href='#' class='button'>Order Now</a>";
        echo "</div>";
    }
} else {
    // Styling the 'No results' message to span the grid columns
    echo "<p style='grid-column: 1 / -1; text-align: center; padding: 50px;'>No books found matching your search term. Try a different query.</p>";
}

$conn->close();
?>