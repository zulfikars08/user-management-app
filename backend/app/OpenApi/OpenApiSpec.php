<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'User Management API', version: '1.0.0', description: 'REST API documentation for the User Management App coding test.')]
#[OA\Server(url: 'http://127.0.0.1:8000/api', description: 'Local API server')]
#[OA\SecurityScheme(securityScheme: 'sanctum', type: 'http', scheme: 'bearer', bearerFormat: 'Sanctum')]
#[OA\Schema(schema: 'User', required: ['id', 'name', 'email', 'role', 'created_at', 'updated_at'], properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'name', type: 'string', example: 'Demo User'),
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.test'),
    new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user']),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
])]
#[OA\Schema(schema: 'LoginRequest', required: ['email', 'password'], properties: [new OA\Property(property: 'email', type: 'string', format: 'email'), new OA\Property(property: 'password', type: 'string', format: 'password')])]
#[OA\Schema(schema: 'CreateUserRequest', required: ['name', 'email', 'password'], properties: [new OA\Property(property: 'name', type: 'string', maxLength: 255), new OA\Property(property: 'email', type: 'string', format: 'email'), new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8), new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], default: 'user')])]
#[OA\Schema(schema: 'UpdateUserRequest', properties: [new OA\Property(property: 'name', type: 'string', maxLength: 255), new OA\Property(property: 'email', type: 'string', format: 'email'), new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, nullable: true), new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'])])]
#[OA\Schema(schema: 'ErrorResponse', required: ['success', 'message', 'errors'], properties: [new OA\Property(property: 'success', type: 'boolean', example: false), new OA\Property(property: 'message', type: 'string'), new OA\Property(property: 'errors', nullable: true)])]
#[OA\Schema(schema: 'ValidationErrorResponse', required: ['success', 'message', 'errors'], properties: [new OA\Property(property: 'success', type: 'boolean', example: false), new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'), new OA\Property(property: 'errors', type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')))])]
#[OA\Schema(schema: 'PaginatedUsers', properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string'), new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/User')), new OA\Property(property: 'meta', properties: [new OA\Property(property: 'current_page', type: 'integer'), new OA\Property(property: 'last_page', type: 'integer'), new OA\Property(property: 'per_page', type: 'integer'), new OA\Property(property: 'total', type: 'integer')], type: 'object'), new OA\Property(property: 'links', properties: [new OA\Property(property: 'first', type: 'string', nullable: true), new OA\Property(property: 'last', type: 'string', nullable: true), new OA\Property(property: 'prev', type: 'string', nullable: true), new OA\Property(property: 'next', type: 'string', nullable: true)], type: 'object')])]
final class OpenApiSpec
{
    #[OA\Post(path: '/login', tags: ['Authentication'], summary: 'Authenticate and issue a Sanctum token', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')), responses: [new OA\Response(response: 200, description: 'Login successful'), new OA\Response(response: 401, description: 'Invalid credentials', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')), new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')), new OA\Response(response: 429, description: 'Too many attempts'), new OA\Response(response: 500, description: 'Unexpected error', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'))])]
    public function login(): void {}

    #[OA\Get(path: '/me', tags: ['Authentication'], summary: 'Get authenticated user', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Current user'), new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function me(): void {}

    #[OA\Post(path: '/logout', tags: ['Authentication'], summary: 'Revoke current access token', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Logout successful'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function logout(): void {}

    #[OA\Get(path: '/users', tags: ['Users'], summary: 'List, search, and paginate users', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Name or numeric ID'), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)), new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 10))], responses: [new OA\Response(response: 200, description: 'Paginated users', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedUsers')), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 422, description: 'Invalid query'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function index(): void {}

    #[OA\Post(path: '/users', tags: ['Users'], summary: 'Create user (admin only)', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CreateUserRequest')), responses: [new OA\Response(response: 201, description: 'User created'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 403, description: 'Admin role required'), new OA\Response(response: 422, description: 'Validation failed'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function store(): void {}

    #[OA\Get(path: '/users/{user}', tags: ['Users'], summary: 'Show user', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'User retrieved'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 404, description: 'Not found'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function show(): void {}

    #[OA\Put(path: '/users/{user}', tags: ['Users'], summary: 'Update user (admin only)', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequest')), responses: [new OA\Response(response: 200, description: 'User updated'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 403, description: 'Admin role required'), new OA\Response(response: 404, description: 'Not found'), new OA\Response(response: 422, description: 'Validation failed'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function put(): void {}

    #[OA\Patch(path: '/users/{user}', tags: ['Users'], summary: 'Partially update user; password optional (admin only)', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequest')), responses: [new OA\Response(response: 200, description: 'User updated'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 403, description: 'Admin role required'), new OA\Response(response: 404, description: 'Not found'), new OA\Response(response: 422, description: 'Validation failed'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function patch(): void {}

    #[OA\Delete(path: '/users/{user}', tags: ['Users'], summary: 'Delete user (admin only)', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'User deleted'), new OA\Response(response: 401, description: 'Unauthenticated'), new OA\Response(response: 403, description: 'Admin role required'), new OA\Response(response: 404, description: 'Not found'), new OA\Response(response: 500, description: 'Unexpected error')])]
    public function destroy(): void {}
}
