<?php
/**
 * TravelMate - UserController
 *
 * Handles user profile display, editing, and password changes.
 */

class UserController
{
    private UserService $userService;
    private User        $userModel;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->userModel   = new User();
    }

    /**
     * GET /profile — Current user's own profile.
     */
    public function myProfile(array $params = []): void
    {
        Security::requireLogin();
        $this->showProfile(['id' => Security::userId()]);
    }

    /**
     * GET /profile/{id} — Public profile view.
     */
    public function showProfile(array $params = []): void
    {
        Security::requireLogin();

        $userId    = (int)($params['id'] ?? Security::userId());
        $flash     = Security::getFlash();
        $pageTitle = 'Profile — ' . APP_NAME;

        try {
            $profile = $this->userService->getProfile($userId);
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $isOwnProfile = ($userId === Security::userId());

        require_once VIEWS_PATH . '/user/profile.php';
    }

    /**
     * GET /profile/edit — Edit profile form.
     */
    public function editProfile(array $params = []): void
    {
        Security::requireLogin();

        $userId     = Security::userId();
        $user       = $this->userModel->findById($userId);
        $pageTitle  = 'Edit Profile — ' . APP_NAME;
        $flash      = Security::getFlash();
        $formErrors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        require_once VIEWS_PATH . '/user/edit-profile.php';
    }

    /**
     * POST /profile/edit — Update profile.
     */
    public function updateProfile(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $userId    = Security::userId();
        $validator = new Validator($_POST);
        $validator->required('full_name', 'Full Name')
                  ->maxLength('full_name', 100, 'Full Name');

        if ($validator->fails()) {
            $_SESSION['form_errors'] = $validator->errors();
            header('Location: ' . BASE_URL . '/profile/edit');
            exit;
        }

        try {
            $this->userService->updateProfile(
                $userId,
                [
                    'full_name' => Security::sanitize($_POST['full_name']),
                    'bio'       => Security::sanitize($_POST['bio']      ?? ''),
                    'location'  => Security::sanitize($_POST['location'] ?? ''),
                ],
                $_FILES['profile_photo'] ?? null
            );

            Security::setFlash('success', 'Profile updated successfully!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/profile/edit');
        exit;
    }

    /**
     * POST /profile/change-password — Update password.
     */
    public function changePassword(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $userId    = Security::userId();
        $validator = new Validator($_POST);
        $validator->required('current_password', 'Current Password')
                  ->required('new_password', 'New Password')
                  ->minLength('new_password', 8, 'New Password')
                  ->matches('confirm_new_password', 'new_password', 'Password confirmation');

        if ($validator->fails()) {
            Security::setFlash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            header('Location: ' . BASE_URL . '/profile/edit');
            exit;
        }

        try {
            $this->userService->changePassword(
                $userId,
                $_POST['current_password'],
                $_POST['new_password']
            );
            Security::setFlash('success', 'Password changed successfully!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/profile/edit');
        exit;
    }
}
