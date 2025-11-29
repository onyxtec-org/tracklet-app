<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Tracklet API Documentation",
 *     version="1.0.0",
 *     description="Multi-Organization Management System API - Complete API documentation for mobile and web developers",
 *     @OA\Contact(
 *         email="support@tracklet.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/",
 *     description="API Server (uses current host)"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token in the format: Bearer {token}"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * @OA\Tag(
 *     name="Roles",
 *     description="Role management endpoints"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 * @OA\Tag(
 *     name="Expenses",
 *     description="Expense tracking endpoints"
 * )
 * @OA\Tag(
 *     name="Inventory",
 *     description="Inventory management endpoints"
 * )
 * @OA\Tag(
 *     name="Assets",
 *     description="Asset management endpoints"
 * )
 * @OA\Tag(
 *     name="Maintenance",
 *     description="Maintenance management endpoints"
 * )
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Dashboard endpoints"
 * )
 * @OA\Tag(
 *     name="Subscription",
 *     description="Subscription endpoints"
 * )
 * @OA\Tag(
 *     name="Super Admin",
 *     description="Super Admin endpoints"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
