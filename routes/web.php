<?php
/**
 * TravelMate - Route Definitions
 *
 * Maps URL paths to Controller@method pairs.
 *
 * Format:
 *   GET  /path  => [ControllerClass, methodName]
 *   POST /path  => [ControllerClass, methodName]
 *
 * Dynamic segments use {param} notation.
 * The router populates $params['param'] for matched segments.
 */

return [

    // --------------------------------------------------------
    // Public Routes (Guest & Authenticated)
    // --------------------------------------------------------
    'GET /'                         => ['HomeController',           'index'],

    // --------------------------------------------------------
    // Authentication Routes
    // --------------------------------------------------------
    'GET /auth/login'               => ['AuthController',           'showLogin'],
    'POST /auth/login'              => ['AuthController',           'login'],
    'GET /auth/register'            => ['AuthController',           'showRegister'],
    'POST /auth/register'           => ['AuthController',           'register'],
    'POST /auth/logout'             => ['AuthController',           'logout'],
    'GET /auth/logout'              => ['AuthController',           'logout'],

    // --------------------------------------------------------
    // User / Profile Routes
    // --------------------------------------------------------
    'GET /dashboard'                => ['DashboardController',      'index'],
    'GET /profile/edit'             => ['UserController',           'editProfile'],
    'POST /profile/edit'            => ['UserController',           'updateProfile'],
    'POST /profile/change-password' => ['UserController',           'changePassword'],
    'GET /profile'                  => ['UserController',           'myProfile'],
    'GET /profile/{id}'             => ['UserController',           'showProfile'],

    // --------------------------------------------------------
    // Trip Routes
    // --------------------------------------------------------
    'GET /trips'                    => ['TripController',           'index'],
    'GET /trips/create'             => ['TripController',           'create'],
    'POST /trips/create'            => ['TripController',           'store'],
    'GET /trips/{id}/edit'          => ['TripController',           'edit'],
    'POST /trips/{id}/edit'         => ['TripController',           'update'],
    'POST /trips/{id}/join'         => ['TripController',           'join'],
    'POST /trips/{id}/leave'        => ['TripController',           'leave'],
    'POST /trips/{id}/approve'      => ['TripController',           'approveMember'],
    'POST /trips/{id}/reject'       => ['TripController',           'rejectMember'],
    'POST /trips/{id}/complete'     => ['TripController',           'markComplete'],
    'POST /trips/{id}/delete'       => ['TripController',           'delete'],
    'GET /trips/{id}'               => ['TripController',           'show'],

    // --------------------------------------------------------
    // Responsibilities Routes
    // --------------------------------------------------------
    'GET /trips/{id}/responsibilities'          => ['ResponsibilityController', 'index'],
    'POST /trips/{id}/responsibilities/create'  => ['ResponsibilityController', 'store'],
    'POST /responsibilities/{id}/assign'        => ['ResponsibilityController', 'assign'],
    'POST /responsibilities/{id}/complete'      => ['ResponsibilityController', 'complete'],
    'POST /responsibilities/{id}/delete'        => ['ResponsibilityController', 'delete'],

    // --------------------------------------------------------
    // Resource Routes
    // --------------------------------------------------------
    'GET /trips/{id}/resources'         => ['ResourceController',       'index'],
    'POST /trips/{id}/resources/create' => ['ResourceController',       'store'],
    'POST /resources/{id}/claim'        => ['ResourceController',       'claim'],
    'POST /resources/{id}/unclaim'      => ['ResourceController',       'unclaim'],
    'POST /resources/{id}/delete'       => ['ResourceController',       'delete'],

    // --------------------------------------------------------
    // Chat Routes
    // --------------------------------------------------------
    'GET /trips/{id}/chat'          => ['ChatController',           'index'],
    'POST /trips/{id}/chat/send'    => ['ChatController',           'send'],
    'GET /trips/{id}/chat/poll'     => ['ChatController',           'poll'],

    // --------------------------------------------------------
    // Expense Routes
    // --------------------------------------------------------
    'GET /trips/{id}/expenses'          => ['ExpenseController',        'index'],
    'POST /trips/{id}/expenses/create'  => ['ExpenseController',        'store'],
    'POST /expenses/{id}/delete'        => ['ExpenseController',        'delete'],

    // --------------------------------------------------------
    // Album Routes
    // --------------------------------------------------------
    'GET /trips/{id}/albums'            => ['AlbumController',          'index'],
    'POST /trips/{id}/albums/create'    => ['AlbumController',          'createAlbum'],
    'GET /albums/{id}'                  => ['AlbumController',          'show'],
    'POST /albums/{id}/upload'          => ['AlbumController',          'upload'],
    'POST /media/{id}/delete'           => ['AlbumController',          'deleteMedia'],

    // --------------------------------------------------------
    // Review Routes
    // --------------------------------------------------------
    'GET /trips/{id}/reviews'           => ['ReviewController',         'index'],
    'POST /reviews/submit'              => ['ReviewController',         'submit'],

    // --------------------------------------------------------
    // Notification Routes
    // --------------------------------------------------------
    'GET /notifications/count'          => ['NotificationController',   'count'],
    'POST /notifications/read-all'      => ['NotificationController',   'markAllRead'],
    'GET /notifications'                => ['NotificationController',   'index'],
    'POST /notifications/{id}/read'     => ['NotificationController',   'markRead'],

];
