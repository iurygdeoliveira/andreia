<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\Andreia360CourseSeeder;
use Database\Seeders\UserSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\View;

beforeEach(function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(Andreia360CourseSeeder::class);
});

it('renders the andreia360 landing page with plans', function (): void {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Método Andreia 360')
        ->assertSee('Premium')
        ->assertSee('Essencial')
        ->assertSee('VIP 360')
        ->assertSee('299')
        ->assertSee('149')
        ->assertSee('499');
});

it('seeds the organization and user accounts for andreia360', function (): void {
    expect(Organization::where('slug', 'andreia360')->exists())->toBeTrue()
        ->and(User::where('email', 'admin@metodoandreia360.com.br')->exists())->toBeTrue()
        ->and(User::where('email', 'andreia@metodoandreia360.com.br')->exists())->toBeTrue()
        ->and(User::where('email', 'aluna@metodoandreia360.com.br')->exists())->toBeTrue();
});

it('verifies that all lesson view paths exist as valid blade files', function (): void {
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $lessons = Lesson::where('organization_id', $org->id)->get();

    expect($lessons)->not->toBeEmpty();

    foreach ($lessons as $lesson) {
        expect($lesson->view_path)->not->toBeEmpty()
            ->and(View::exists($lesson->view_path))->toBeTrue();
    }
});

it('allows the owner to view the lessons in user panel', function (): void {
    $owner = User::where('email', 'andreia@metodoandreia360.com.br')->firstOrFail();
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $lesson = Lesson::where('organization_id', $org->id)->firstOrFail();

    $this->actingAs($owner)
        ->get("/user/{$org->slug}/lesson/{$lesson->id}")
        ->assertSuccessful();
});

it('allows the student to view the lessons in student panel', function (): void {
    $student = User::where('email', 'aluna@metodoandreia360.com.br')->firstOrFail();
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $lesson = Lesson::where('organization_id', $org->id)->firstOrFail();

    $this->actingAs($student)
        ->get("/student/{$org->slug}/lesson/{$lesson->id}")
        ->assertSuccessful();
});
