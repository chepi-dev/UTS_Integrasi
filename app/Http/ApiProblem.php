<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiProblem
{
    public static function respond(int $status, string $title, string $detail = '', string $type = ''): JsonResponse
    {
        $payload = [
            'type' => $type ?: sprintf('https://localhost:8000/api/v1/problems/%d', $status),
            'title' => $title,
            'status' => $status,
        ];

        if ($detail !== '') {
            $payload['detail'] = $detail;
        }

        return new JsonResponse($payload, $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }
}