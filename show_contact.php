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

// Handling search functionality
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ? AND name LIKE ?");
    $like_query = "%" . $search_query . "%";
    $stmt->bind_param("is", $user_id, $like_query);
} else {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            background-color: #121212;
            margin: 0;
            padding: 0;
            padding-top: 70px; /* Space for the header */
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

    /* Profile Dropdown */
    .profile-dropdown {
        position: relative;
        display: inline-block;
        color: #ffffff;
    }

    .profile-dropdown .dropdown-btn {
        background-color: transparent;
        border: none;
        font-size: 1rem;
        color: #ffffff;
        cursor: pointer;
        padding: 10px;
        transition: color 0.3s;
    }

    .profile-dropdown .dropdown-btn:hover {
        color: #007bff;
    }

    .profile-dropdown .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #333;
        min-width: 150px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        border-radius: 5px;
        z-index: 1000;
        text-align: left;
    }

    .profile-dropdown .dropdown-content a {
        color: white;
        padding: 10px;
        text-decoration: none;
        display: block;
        transition: background-color 0.3s;
    }

    .profile-dropdown .dropdown-content a:hover {
        background-color: #444;
    }

    .profile-dropdown:hover .dropdown-content {
        display: block;
    }

    /* Media Queries for Smaller Screens */
    @media screen and (max-width: 768px) {
        .header {
            flex-wrap: wrap; /* Allow items to wrap to the next line */
            height: auto; /* Adjust height dynamically */
        }

        .header .nav-links {
            justify-content: center; /* Center links on smaller screens */
        }

        .header .nav-links li a {
            font-size: 0.9rem; /* Slightly smaller font for links */
        }
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

        .search-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #333;
            color: #fff;
        }

        .search-form button {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #0056b3;
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

        .contact-card .details {
            font-size: 0.9rem;
            color: #ddd;
            margin-top: 10px;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 768px) {
            .search-form input, .search-form button {
                width: 100%;
            }

            .contact-card h3 {
                font-size: 1.3rem;
            }

            .contact-card .details {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Contacts</h2>
        <form method="POST" action="show_contact.php" class="search-form">
            <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="contact-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="contact-card">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="details">
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                            <p><strong>Home:</strong> <?php echo htmlspecialchars($row['home']); ?></p>
                            <p><strong>Note:</strong> <?php echo htmlspecialchars($row['note']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No contacts found. Consider adding some from "Create Contact".</p>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
