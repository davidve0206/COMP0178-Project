<?php include_once("header.php") ?>

<div class="container my-5">

    <?php
    // Process the registration form submission

    // Connect to the database
    require_once "../database/setup.php";

    $error_messages = [];

    // we already validate several fields in the registration form initially,
    // we perform an additional validation here to ensure robustness

    // Username
    if (!isset($_POST['username']) || trim($_POST['username']) == '') {
        array_push($error_messages, 'Username is required.');
    } else {
        $username = $db->real_escape_string($_POST['username']);
        if (strlen($username) > 30) {
            array_push($error_messages, 'Username cannot exceed 30 characters.');
        }
    }
    // Check if username is already taken
    $query = "SELECT id FROM Users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        array_push($error_messages, 'Username is already taken. Please choose another.');
    }
    $stmt->close();

    // Password
    if (!isset($_POST['initialPassword']) || trim($_POST['initialPassword']) == '') {
        array_push($error_messages, 'Password is required.');
    } else {
        $password = $_POST['initialPassword'];
        $repeatPassword = $_POST['repeatPassword'];

        if (strlen($password) < 8) {
            array_push($error_messages, 'Password must be at least 8 characters long.');
        }

        if ($password !== $repeatPassword) {
            array_push($error_messages, 'Passwords do not match.');
        }
    }

    // Email
    if (!isset($_POST['userEmail']) || trim($_POST['userEmail']) == '') {
        array_push($error_messages, 'Email is required.');
    } else {
        $email = $db->real_escape_string($_POST['userEmail']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($error_messages, 'Invalid email address.');
        }

        if (strlen($email) > 50) {
            array_push($error_messages, 'Email cannot exceed 50 characters.');
        }
    }

    // First Name
    if (!isset($_POST['firstName']) || trim($_POST['firstName']) == '') {
        array_push($error_messages, 'First name is required.');
    } else {
        $first_name = $db->real_escape_string($_POST['firstName']);
        if (strlen($first_name) > 20) {
            array_push($error_messages, 'First name cannot exceed 20 characters.');
        }
    }

    // Last Name
    if (!isset($_POST['lastName']) || trim($_POST['lastName']) == '') {
        array_push($error_messages, 'Last name is required.');
    } else {
        $last_name = $db->real_escape_string($_POST['lastName']);
        if (strlen($last_name) > 30) {
            array_push($error_messages, 'Last name cannot exceed 30 characters.');
        }
    }

    // Address
    if (!isset($_POST['address']) || trim($_POST['address']) == '') {
        array_push($error_messages, 'Address is required.');
    } else {
        $address = $db->real_escape_string($_POST['address']);
        if (strlen($address) > 100) {
            array_push($error_messages, 'Address cannot exceed 100 characters.');
        }
    }

    // Account Type (Buyer/Seller/Mixed)
    if (!isset($_POST['accountType']) || trim($_POST['accountType']) == '') {
        array_push($error_messages, 'You must select an account type.');
    } else {
        $accountType = $_POST['accountType'];
        $isBuyer = ($accountType === 'buyer' || $accountType === 'mixed') ? 1 : 0;
        $isSeller = ($accountType === 'seller' || $accountType === 'mixed') ? 1 : 0;
    }

    // Check for errors
    if (count($error_messages) > 0) {
        foreach ($error_messages as $error) {
            echo "<p><span class='font-weight-bold'>Error: </span> $error</p>";
        }
        echo '<button onclick="history.back()" class="btn btn-primary">Go Back</button>';
    } else {
        // Insert user into the database
        try {
            $query = "INSERT INTO Users (username, password, email, firstName, lastName, address, isBuyer, isSeller) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ssssssii", $username, $password, $email, $first_name, $last_name, $address, $isBuyer, $isSeller);

            if ($stmt->execute()) {
                echo '<div class="text-center">Registration successful!</div>';
            } else {
                echo '<p>Error during registration: ' . $stmt->error . '</p>';
            }
            $stmt->close();
            $db->close();
        } catch (Exception $e) {
            echo '<p>Error: ' . $e->getMessage() . '</p>';
        }
    }
    ?>

</div>

<?php include_once("footer.php") ?>