<?php

use Nevs\Router;
use Nevs\RouteGroup;
use Nevs\Route;

const NEVS_ROUTER = new Router([
    new RouteGroup("/public/", [

        new Route("POST", "login", 'AuthController', "Login"),
        new Route("POST", "password-reset", 'AuthController', "PasswordReset"),
        new Route("GET", "heartbeat", 'AuthController', "Heartbeat")

    ],  ['CorsMiddleware']),

    new RouteGroup("/", [

        new Route("GET", "session", 'AuthController', "Session"),
        new Route("POST", "logout", 'AuthController', "Logout"),

        new Route("POST", "upload", 'UploadController', "Upload"),
        new Route("GET", "upload", 'UploadController', "Get", ['hash']),

        new Route("GET", "users", 'UserController', "GetMultiple"),
        new Route("GET", "users", 'UserController', "GetOne", ['id']),
        new Route("PUT", "users", 'UserController', "Update", ['id']),
        new Route("POST", "users", 'UserController', "Add"),

        new RouteGroup("select/", [
            new Route("GET", "permissions", 'PermissionsController', "Select"),
        ])
    ],  ['CorsMiddleware', 'AuthMiddleware'])
]);