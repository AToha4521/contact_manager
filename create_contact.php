<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

include 'config.php'; // Include database connection
include 'header.php';

$message = "";
$redirect = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_contact'])) {
    // Get input values from the form
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $home = $_POST['home'];
    $note = $_POST['note'];

    // Fetch user_id based on username in session
    $username = $_SESSION['username'];
    $user_id_query = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $user_id_query->bind_param("s", $username);
    $user_id_query->execute();
    $result = $user_id_query->get_result();
    $user_data = $result->fetch_assoc();
    $user_id = $user_data['id'];

    // Prepare SQL statement to insert contact
    $stmt = $conn->prepare("INSERT INTO contacts (user_id, name, phone, email, home, note) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $name, $phone, $email, $home, $note);

    if ($stmt->execute()) {
        $message = "Contact created successfully!";
        $redirect = "dashboard.php";
    } else {
        $message = "Contact creation failed! Error: " . $stmt->error;
        $redirect = "create_contact.php";
    }

    $stmt->close(); // Close statement
    $conn->close(); // Close database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contact</title>
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
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #ddd;
        }

        input[type="text"], input[type="email"], textarea {
             width: 100%; /* Ensure it spans the width of the container */
             padding: 10px;
             margin-bottom: 15px;
             background-color: rgba(255, 255, 255, 0.2);
             color: #ffffff;
             border: 1px solid #444;
             border-radius: 5px;
             font-size: 1rem;
             box-sizing: border-box; /* Include padding and borders in the element's total width */
}
        input:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 1rem;
        }

        a:hover {
            text-decoration: underline;
        }

        .container {
           max-width: 500px;
            margin: 50px auto;
            padding: 20px; /* Adjust this if necessary */
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            text-align: center;
            box-sizing: border-box; /* Ensure padding doesn't break the layout */
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

        @media screen and (max-width: 768px) {
                    h2 {
                        font-size: 1.5rem;
                    }

                    .contact-card {
                        padding: 15px;
                    }

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



    </style>
    <script>
        // Function to show alert with message
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body onload="showAlert('<?php echo $message; ?>')">
    <div class="container">
        <h2>Create New Contact</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" placeholder="Enter phone number" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label for="home">Home Address:</label>
                <input type="text" id="home" name="home" placeholder="Enter home address">
            </div>
            <div class="form-group">
                <label for="note">Note:</label>
                <textarea id="note" name="note" placeholder="Add a note" rows="3"></textarea>
            </div>
            <button type="submit" name="create_contact">Add Contact</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
