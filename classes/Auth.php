<?php
// classes/Auth.php

class Auth {
    private $db;
    
    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Register a new user
    public function register($username, $password, $fullname, $role) {
        try {
            // Check if username already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                return "Username sudah terdaftar!";
            }

            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $fullname, $role]);
            return true;
        } catch (PDOException $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }

    // Log in user
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
            return "Username atau Password salah!";
        } catch (PDOException $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }

    // Log out user
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        session_destroy();
        return true;
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // Get current user's role
    public static function getRole() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['role']) ? $_SESSION['role'] : null;
    }

    // Get current user's details (with database ID validation)
    public static function getUser() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (self::isLoggedIn()) {
            $sessId = $_SESSION['user_id'];
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

            // Verify if user_id exists in DB
            try {
                $db = new Database();
                $pdo = $db->getConnection();
                if ($pdo) {
                    $stmt = $pdo->prepare("SELECT id, username, fullname, role FROM users WHERE id = ?");
                    $stmt->execute([$sessId]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['fullname'] = $row['fullname'];
                        $_SESSION['role'] = $row['role'];
                        return $row;
                    } elseif (!empty($username)) {
                        // Fallback lookup by username
                        $stmt2 = $pdo->prepare("SELECT id, username, fullname, role FROM users WHERE username = ?");
                        $stmt2->execute([$username]);
                        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                        if ($row2) {
                            $_SESSION['user_id'] = $row2['id'];
                            $_SESSION['username'] = $row2['username'];
                            $_SESSION['fullname'] = $row2['fullname'];
                            $_SESSION['role'] = $row2['role'];
                            return $row2;
                        }
                    }
                }
            } catch (Exception $e) {
                // Ignore DB error fallback to session
            }

            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'fullname' => $_SESSION['fullname'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    // Alias for getUser()
    public static function user() {
        return self::getUser();
    }

    // Require user to be logged in
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header("Location: ../auth/login.php");
            exit();
        }
    }

    // Require specific role(s)
    public static function requireRole($allowedRoles) {
        self::requireLogin();
        $role = self::getRole();
        
        if (is_array($allowedRoles)) {
            if (!in_array($role, $allowedRoles)) {
                self::redirectUnauthorized();
            }
        } else {
            if ($role !== $allowedRoles) {
                self::redirectUnauthorized();
            }
        }
    }

    private static function redirectUnauthorized() {
        $role = self::getRole();
        if ($role === 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($role === 'vendor') {
            header("Location: ../vendor/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    }
}
?>
