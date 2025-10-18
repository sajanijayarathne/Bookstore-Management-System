<?php
include 'includes/auth_check.php'; // Ensures the user is logged in
include 'includes/db_connect.php';

$message = "";

// --- 1. HANDLE DELETE ---
if (isset($_GET['delete_id'])) {
    $id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM books WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Book deleted successfully! ";
    } else {
        $message = "Error deleting record: " . $conn->error;
    }
}

// --- 2. HANDLE ADD/UPDATE ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $price = $_POST['price'];
    $qty = $_POST['stock_qty'];
    $book_id = $_POST['book_id'] ?? null; // For updates

    if ($book_id) {
        // UPDATE Logic
        $sql = "UPDATE books SET title='$title', author='$author', isbn='$isbn', price='$price', stock_qty='$qty' WHERE id='$book_id'";
        if ($conn->query($sql) === TRUE) {
            $message = "Book updated successfully!";
        } else {
            $message = "Error updating book: " . $conn->error;
        }
    } else {
        // ADD Logic
        $sql = "INSERT INTO books (title, author, isbn, price, stock_qty) VALUES ('$title', '$author', '$isbn', '$price', '$qty')";
        if ($conn->query($sql) === TRUE) {
            $message = "New book added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// --- 3. FETCH BOOKS (READ) ---
$books_sql = "SELECT * FROM books ORDER BY id DESC";
$books_result = $conn->query($books_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📚 Admin Dashboard</h1>
        <div>
            <a href="index.php" class="button">Public Catalog</a>
            <a href="admin_logout.php" class="button delete-btn">Logout</a>
        </div>
    </header>

    <div class="container">
        <h2>Manage Inventory (CRUD)</h2>
        <?php if ($message) { echo "<p style='color: var(--accent); font-weight: bold; padding: 10px; border: 1px solid var(--accent); background: white; border-radius: 5px;'>$message</p>"; } ?>
        
        <div class="form-box" style="margin-bottom: 40px; padding: 20px; border: 1px solid #ccc; background: #fff;">
            <h3>Add/Edit Book Details</h3>
            <form method="post" action="manage_books.php">
                <input type="hidden" name="book_id" id="book_id" value=""> 

                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
                
                <label for="author">Author:</label>
                <input type="text" name="author" id="author" required>
                
                <label for="isbn">ISBN:</label>
                <input type="text" name="isbn" id="isbn">

                <label for="price">Price ($):</label>
                <input type="number" name="price" id="price" step="0.01" required>

                <label for="stock_qty">Stock Quantity:</label>
                <input type="number" name="stock_qty" id="stock_qty" required>

                <button type="submit" class="button" id="submit_btn">Add Book</button>
                <button type="button" class="button delete-btn" onclick="resetForm()">Reset Form</button>
            </form>
        </div>

        <h3>Current Inventory</h3>
        <table>
            <tr><th>ID</th><th>Title</th><th>Author</th><th>Price</th><th>Stock</th><th>Action</th></tr>
            <?php while($row = $books_result->fetch_assoc()): ?>
                <tr data-book='<?= json_encode($row) ?>'>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['author']) ?></td>
                    <td>$<?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['stock_qty']) ?></td>
                    <td>
                        <button class="button" onclick="editBook(this)">Edit</button>
                        <a href="?delete_id=<?= $row['id'] ?>" class="button delete-btn" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
    function editBook(button) {
        // Get the data from the table row (using custom data attribute)
        const row = button.closest('tr');
        const bookData = JSON.parse(row.getAttribute('data-book'));

        // Populate the form fields
        document.getElementById('book_id').value = bookData.id;
        document.getElementById('title').value = bookData.title;
        document.getElementById('author').value = bookData.author;
        document.getElementById('isbn').value = bookData.isbn;
        document.getElementById('price').value = bookData.price;
        document.getElementById('stock_qty').value = bookData.stock_qty;
        
        // Change button text and color to reflect update action (UX enhancement)
        document.getElementById('submit_btn').textContent = 'Update Book';
        document.getElementById('submit_btn').style.backgroundColor = 'blue';

        // Scroll to form to show the user what changed
        document.querySelector('.form-box').scrollIntoView({ behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('book_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('author').value = '';
        document.getElementById('isbn').value = '';
        document.getElementById('price').value = '';
        document.getElementById('stock_qty').value = '';
        
        document.getElementById('submit_btn').textContent = 'Add Book';
        document.getElementById('submit_btn').style.backgroundColor = 'var(--accent)';
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>