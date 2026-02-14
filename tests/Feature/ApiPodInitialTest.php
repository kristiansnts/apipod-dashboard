<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPodInitialTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_filament_login(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }
}
