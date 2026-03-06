<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Fixtures;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockMiddleware
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        return response()->json(['message' => 'blocked'], 401);
    }
}
