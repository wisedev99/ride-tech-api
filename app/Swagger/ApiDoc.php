<?php

namespace App\Swagger;

/**
 * All OpenAPI / Swagger annotations for the RideTech API live in this single
 * file so the controllers stay clean. l5-swagger scans app/ and picks them up.
 *
 * @OA\Info(
 *     title="RideTech API",
 *     version="1.0.0",
 *     description="RESTful API for the RideTech ride-sharing service."
 * )
 *
 * @OA\Server(url=L5_SWAGGER_CONST_HOST, description="API server")
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 *
 * ----------------------------------------------------------------------------
 * AUTH
 * ----------------------------------------------------------------------------
 *
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"name","email","phone","password","password_confirmation","role"},
 *         @OA\Property(property="name", type="string", example="Bob"),
 *         @OA\Property(property="email", type="string", example="bob@example.com"),
 *         @OA\Property(property="phone", type="string", example="+998901112233"),
 *         @OA\Property(property="password", type="string", example="secret123"),
 *         @OA\Property(property="password_confirmation", type="string", example="secret123"),
 *         @OA\Property(property="role", type="string", enum={"passenger","driver"})
 *     )),
 *     @OA\Response(response=201, description="Registered"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Log in and receive a token",
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"email","password"},
 *         @OA\Property(property="email", type="string", example="bob@example.com"),
 *         @OA\Property(property="password", type="string", example="secret123")
 *     )),
 *     @OA\Response(response=200, description="Logged in"),
 *     @OA\Response(response=422, description="Invalid credentials")
 * )
 *
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Auth"},
 *     summary="Revoke the current token",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Logged out"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 *     path="/api/me",
 *     tags={"Auth"},
 *     summary="Get the authenticated user",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Current user"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * ----------------------------------------------------------------------------
 * TRIPS
 * ----------------------------------------------------------------------------
 *
 * @OA\Get(
 *     path="/api/trips",
 *     tags={"Trips"},
 *     summary="List my trips (passenger or driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"requested","accepted","completed","cancelled"})),
 *     @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="passenger_id", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="driver_id", in="query", @OA\Schema(type="integer")),
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Paginated list of trips")
 * )
 *
 * @OA\Post(
 *     path="/api/trips",
 *     tags={"Trips"},
 *     summary="Create a trip (passenger)",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"pickup_address","destination_address"},
 *         @OA\Property(property="pickup_address", type="string", example="Chilonzor"),
 *         @OA\Property(property="destination_address", type="string", example="Yunusobod"),
 *         @OA\Property(property="preferences", type="string", nullable=true)
 *     )),
 *     @OA\Response(response=201, description="Trip created"),
 *     @OA\Response(response=403, description="Not a passenger")
 * )
 *
 * @OA\Get(
 *     path="/api/trips/available",
 *     tags={"Trips"},
 *     summary="List open trips (driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Open trips"),
 *     @OA\Response(response=403, description="Not a driver")
 * )
 *
 * @OA\Get(
 *     path="/api/trips/{trip}",
 *     tags={"Trips"},
 *     summary="Show a trip (passenger or driver of it)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="trip", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Trip details"),
 *     @OA\Response(response=403, description="Not your trip"),
 *     @OA\Response(response=404, description="Not found")
 * )
 *
 * @OA\Put(
 *     path="/api/trips/{trip}",
 *     tags={"Trips"},
 *     summary="Update a requested trip (passenger owner)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="trip", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(@OA\JsonContent(
 *         @OA\Property(property="pickup_address", type="string"),
 *         @OA\Property(property="destination_address", type="string"),
 *         @OA\Property(property="preferences", type="string", nullable=true)
 *     )),
 *     @OA\Response(response=200, description="Trip updated"),
 *     @OA\Response(response=403, description="Not allowed / not requested")
 * )
 *
 * @OA\Delete(
 *     path="/api/trips/{trip}",
 *     tags={"Trips"},
 *     summary="Cancel a trip (passenger owner)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="trip", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Trip cancelled"),
 *     @OA\Response(response=403, description="Not your trip")
 * )
 *
 * @OA\Post(
 *     path="/api/trips/{trip}/accept",
 *     tags={"Trips"},
 *     summary="Accept a trip (driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="trip", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Trip accepted"),
 *     @OA\Response(response=403, description="Trip not available")
 * )
 *
 * @OA\Post(
 *     path="/api/trips/{trip}/reject",
 *     tags={"Trips"},
 *     summary="Reject a trip (driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="trip", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Trip rejected"),
 *     @OA\Response(response=403, description="Trip not available")
 * )
 *
 * @OA\Post(
 *     path="/api/trips/{trip}/complete",
 *     tags={"Trips"},
 *     summary="Complete a trip (driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="trip", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Trip completed"),
 *     @OA\Response(response=403, description="Not the assigned driver")
 * )
 *
 * ----------------------------------------------------------------------------
 * CARS
 * ----------------------------------------------------------------------------
 *
 * @OA\Get(
 *     path="/api/cars",
 *     tags={"Cars"},
 *     summary="List my cars (driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="List of cars"),
 *     @OA\Response(response=403, description="Not a driver")
 * )
 *
 * @OA\Post(
 *     path="/api/cars",
 *     tags={"Cars"},
 *     summary="Add a car (driver)",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"brand","model","plate_number"},
 *         @OA\Property(property="brand", type="string", example="Chevrolet"),
 *         @OA\Property(property="model", type="string", example="Cobalt"),
 *         @OA\Property(property="plate_number", type="string", example="01A123BC")
 *     )),
 *     @OA\Response(response=201, description="Car created"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Delete(
 *     path="/api/cars/{car}",
 *     tags={"Cars"},
 *     summary="Delete a car (owner driver)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="car", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Car deleted"),
 *     @OA\Response(response=403, description="Not your car")
 * )
 *
 * ----------------------------------------------------------------------------
 * REVIEWS
 * ----------------------------------------------------------------------------
 *
 * @OA\Get(
 *     path="/api/reviews/{driver}",
 *     tags={"Reviews"},
 *     summary="List a driver's reviews",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="driver", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Paginated reviews")
 * )
 *
 * @OA\Post(
 *     path="/api/reviews/{driver}",
 *     tags={"Reviews"},
 *     summary="Review a driver (passenger, after a completed trip)",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="driver", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(
 *         required={"rating"},
 *         @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
 *         @OA\Property(property="comment", type="string", nullable=true)
 *     )),
 *     @OA\Response(response=201, description="Review saved"),
 *     @OA\Response(response=403, description="No completed trip with this driver")
 * )
 */
class ApiDoc
{
}
