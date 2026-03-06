<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LaravelOpenVasp\Tests\TestCase;

class OpenVaspInteropServerTest extends TestCase
{
    public function test_openvasp_reference_server_version_and_identity_endpoints(): void
    {
        if (! filter_var(env('OPENVASP_RUN_INTEGRATION_TESTS', false), FILTER_VALIDATE_BOOL)) {
            $this->markTestSkipped('Set OPENVASP_RUN_INTEGRATION_TESTS=true to run external interoperability checks.');
        }

        $versionResponse = Http::timeout(20)->get('https://api.trp.openvasp.org/version');
        $versionResponse->throw();

        $this->assertTrue(Str::contains((string) $versionResponse->body(), '.'));

        $requestIdentifier = (string) Str::uuid();
        $identityResponse = Http::timeout(20)
            ->withHeaders([
                'api-version' => '3.2.1',
                'request-identifier' => $requestIdentifier,
            ])
            ->get('https://api.trp.openvasp.org/identity');

        $identityResponse->throw();

        $this->assertSame('3.2.1', $identityResponse->header('api-version'));
        $this->assertSame($requestIdentifier, $identityResponse->header('request-identifier'));
        $this->assertArrayHasKey('lei', $identityResponse->json());
        $this->assertArrayHasKey('x509', $identityResponse->json());
    }
}
