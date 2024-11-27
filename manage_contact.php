<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'config.php';
include 'header.php';

// Fetch user ID based on username in session
$username = $_SESSION['username'];
$user_id_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_id_query->bind_param("s", $username);
$user_id_query->execute();
$user_id_result = $user_id_query->get_result();
$user_data = $user_id_result->fetch_assoc();
$user_id = $user_data['id'];

// Handle deleting a contact
if (isset($_GET['delete'])) {
    $contact_id = $_GET['delete'];
    $conn->query("DELETE FROM contacts WHERE id='$contact_id' AND user_id='$user_id'");
    header("Location: manage_contact.php");
}

// Handle updating a contact
if (isset($_POST['update_contact'])) {
    $contact_id = $_POST['contact_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $note = $_POST['note'];
    $home = $_POST['home'];

    $conn->query("UPDATE contacts SET name='$name', phone='$phone', email='$email', note='$note', home='$home' WHERE id='$contact_id' AND user_id='$user_id'");
    header("Location: manage_contact.php");
}

// Fetch all contacts for the logged-in user
$stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contacts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            background-color: #121212;
            margin: 0;
            padding: 0;
            padding-top: 70px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
           text-align: center;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .contact-card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            text-align: left;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .contact-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.6);
        }

        .contact-card h3 {
            font-size: 1.5rem;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .contact-card p {
            font-size: 0.9rem;
            color: #ddd;
            margin: 5px 0;
        }

        .contact-card .actions {
            margin-top: 15px;
        }

        .contact-card .actions a {
            padding: 8px 12px;
            font-size: 0.9rem;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            color: #ffffff;
            background-color: #007bff;
            transition: background-color 0.3s;
        }

        .contact-card .actions a:hover {
            background-color: #0056b3;
        }

        .contact-card .actions .delete {
            background-color: #ff4d4d;
        }

        .contact-card .actions .delete:hover {
            background-color: #ff1a1a;
        }

        
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
           transform: translate(-50%, -50%);
            background-color: #222;
            color: #fff;
            padding: 80px; 
            width: 400px; 
            max-width: auto; 
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }


        .modal.active {
            display: block;
        }

        .modal input, .modal textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
        }

        .modal button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal button:hover {
            background-color: #0056b3;
        }

        .modal .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            height: 70px; /* Add a fixed height to standardize */
            background-color: #1e1e1e;
            color: #ffffff;
            z-index: 1000;
           box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
           box-sizing: border-box; 
        }

        .header .logo h1 {
            font-size: 1.5rem;
        }

.header .nav-links {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
}

.header .nav-links li {
    display: inline-block;
    white-space: nowrap; /* Prevents links from breaking into multiple lines */
}

.header .nav-links li a {
    color: #ffffff;
    text-decoration: none;
    font-size: 1rem;
    padding: 10px;
    transition: color 0.3s ease;
}

.header .nav-links li a:hover {
    color: #007bff;
}

/* Dropdown */
.profile-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-btn {
    background-color: transparent;
    color: #ffffff;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    padding: 10px;
    transition: color 0.3s;
}

.dropdown-btn:hover {
    color: #007bff;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: #333;
    min-width: 150px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    border-radius: 5px;
    z-index: 1000;
}

.dropdown-content a {
    color: white;
    padding: 10px;
    text-decoration: none;
    display: block;
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: #444;
}

.profile-dropdown:hover .dropdown-content {
    display: block;
}


        @media screen and (max-width: 768px) {
            h2 {
                font-size: 1.5rem;
            }

            .contact-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Your Contacts</h2>

        <div class="contact-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="contact-card">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Home:</strong> <?php echo htmlspecialchars($row['home']); ?></p>
                    <p><strong>Note:</strong> <?php echo htmlspecialchars($row['note']); ?></p>
                    <div class="actions">
                        <a href="#" onclick="openModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</a>
                        <a href="manage_contact.php?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this contact?');">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
            
        </div>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <!-- Pop-Up Modal -->
    <div id="editModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Contact</h2>
        <form method="POST" action="">
            <input type="hidden" id="contact_id" name="contact_id">
            <input type="text" id="name" name="name" placeholder="Name" required>
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <textarea id="note" name="note" placeholder="Note"></textarea>
            <input type="text" id="home" name="home" placeholder="Home">
            <button type="submit" name="update_contact">Update Contact</button>
        </form>
    </div>

    <script>
        const modal = document.getElementById('editModal');

        function openModal(contact) {
            modal.classList.add('active');
            document.getElementById('contact_id').value = contact.id;
            document.getElementById('name').value = contact.name;
            document.getElementById('phone').value = contact.phone;
            document.getElementById('email').value = contact.email;
            document.getElementById('note').value = contact.note;
            document.getElementById('home').value = contact.home;
        }

        function closeModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
