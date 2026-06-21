<?php
/**
 * TravelMate - AuthService
 *
 * Business logic for user authentication.
 */

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Register a new user.
     *
     * @param array $data  Validated: full_name, username, email, password
     * @return int         New user ID
     * @throws RuntimeException on duplicate email/username
     */
    public function register(array $data): int
    {
        // Check uniqueness
        if ($this->userModel->emailExists($data['email'])) {
            throw new RuntimeException('This email address is already registered.');
        }

        if ($this->userModel->usernameExists($data['username'])) {
            throw new RuntimeException('This username is already taken.');
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $userId = $this->userModel->create([
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => $hashedPassword,
        ]);

        Logger::authEvent('REGISTER_SUCCESS', $data['email']);

        return $userId;
    }

    /**
     * Authenticate a user with email and password.
     *
     * @param string $email
     * @param string $password
     * @return array  User data
     * @throws RuntimeException on failure
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            Logger::authEvent('LOGIN_FAIL_NOT_FOUND', $email);
            throw new RuntimeException('Invalid email or password.');
        }

        if ($user['status'] === 'suspended') {
            Logger::authEvent('LOGIN_FAIL_SUSPENDED', $email);
            throw new RuntimeException('Your account has been suspended. Please contact support.');
        }

        if (!password_verify($password, $user['password'])) {
            Logger::authEvent('LOGIN_FAIL_PASSWORD', $email);
            throw new RuntimeException('Invalid email or password.');
        }

        Logger::authEvent('LOGIN_SUCCESS', $email);

        return $user;
    }

    /**
     * Create a user session after successful login.
     *
     * @param array $user
     */
    public function createSession(array $user): void
    {
        Security::regenerateSession();

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_photo'] = $user['profile_photo'];
    }

    /**
     * Log the user out and destroy the session.
     */
    public function logout(): void
    {
        $email = $_SESSION['user_email'] ?? '';
        Logger::authEvent('LOGOUT', $email);
        Security::destroySession();
    }
}
