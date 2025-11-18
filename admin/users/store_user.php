    <?php
    session_start();
    include('../../config/database.php');

    // Admin access only
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        $_SESSION['message'] = "Access denied. Admins only.";
        header("Location: ../login.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = ($_POST['role'] === 'admin') ? 'admin' : 'customer';

        // Insert into users
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Handle profile picture
        $profile_picture = null;
        if (!empty($_FILES['profile_picture']['name'])) {
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $profile_picture = "user_$user_id.".$ext;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], "../../assets/images/users/$profile_picture");
        }

        // Insert into user_profiles
        $stmt = $conn->prepare("INSERT INTO user_profiles (user_id, first_name, last_name, middle_initial, phone, address, town, zipcode, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issssssss",
            $user_id,
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['middle_initial'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['town'],
            $_POST['zipcode'],
            $profile_picture
        );
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "User created successfully.";
        header("Location: ../manage_users.php");
        exit;
    }
