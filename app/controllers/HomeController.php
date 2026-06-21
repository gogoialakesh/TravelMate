<?php
/**
 * TravelMate - HomeController
 */

class HomeController
{
    private Trip $tripModel;

    public function __construct()
    {
        $this->tripModel = new Trip();
    }

    /**
     * GET / — Public landing page with featured trips.
     */
    public function index(array $params = []): void
    {
        $flash        = Security::getFlash();
        $pageTitle    = APP_NAME . ' — Plan Your Next Adventure Together';
        $featuredTrips = $this->tripModel->getAll(['status' => 'upcoming'], 6, 0);

        require_once VIEWS_PATH . '/home/index.php';
    }
}
