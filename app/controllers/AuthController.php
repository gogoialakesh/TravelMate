<?php
/**
 * TravelMate - AuthController
 *
 * Handles login, registration, and logout HTTP requests.
 */

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * GET /auth/login — Show login form.
     */
    public function showLogin(array $params = []): void
    {
        Security::requireGuest();
        $pageTitle = 'Login — ' . APP_NAME;
        require_once VIEWS_PATH . '/auth/login.php';
    }

    /**
     * POST /auth/login — Process login form.
     */
    public function login(array $params = []): void
    {
        Security::requireGuest();
        Security::validateCsrf();

        $email    = Security::sanitize($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        // Validate
        $validator = new Validator($_POST);
        $validator->required('email', 'Email')
                  ->email('email')
                  ->required('password', 'Password');

        if ($validator->fails()) {
            Security::setFlash('error', implode(' ', array_column($validator->errors(), 0)));
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        try {
            $user = $this->authService->login($email, $password);
            $this->authService->createSession($user);
            Security::setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * GET /auth/register — Show registration form.
     */
    public function showRegister(array $params = []): void
    {
        Security::requireGuest();
        $pageTitle = 'Register — ' . APP_NAME;
        require_once VIEWS_PATH . '/auth/register.php';
    }

    /**
     * POST /auth/register — Process registration form.
     */
    public function register(array $params = []): void
    {
        Security::requireGuest();
        Security::validateCsrf();

        $validator = new Validator($_POST);
        $validator->required('full_name', 'Full Name')
                  ->maxLength('full_name', 100, 'Full Name')
                  ->required('username', 'Username')
                  ->minLength('username', 3, 'Username')
                  ->maxLength('username', 50, 'Username')
                  ->alphanumeric('username', 'Username')
                  ->required('email', 'Email')
                  ->email('email')
                  ->required('password', 'Password')
                  ->minLength('password', 8, 'Password')
                  ->required('confirm_password', 'Confirm Password')
                  ->matches('confirm_password', 'password', 'Password confirmation');

        if ($validator->fails()) {
            $_SESSION['form_errors']   = $validator->errors();
            $_SESSION['form_old_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }

        try {
            $userId = $this->authService->register([
                'full_name' => Security::sanitize($_POST['full_name']),
                'username'  => Security::sanitize($_POST['username']),
                'email'     => Security::sanitize($_POST['email']),
                'password'  => $_POST['password'],
            ]);

            Security::setFlash('success', 'Registration successful! Please log in.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
            $_SESSION['form_old_data'] = $_POST;
            header('Location: ' . BASE_URL . '/auth/register');
            exit;
        }
    }

    /**
     * POST /auth/logout — Destroy session and redirect.
     */
    public function logout(array $params = []): void
    {
        $this->authService->logout();
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}
