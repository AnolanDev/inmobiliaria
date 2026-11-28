<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get(route('profile.edit'))->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test('pages::settings.profile')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('updateProfileInformation');

        $response->assertHasNoErrors();

        $user->refresh();

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_is_stored_in_lowercase_so_the_user_can_still_log_in(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('pages::settings.profile')
            ->set('name', 'Test User')
            ->set('email', 'Test@Example.com')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->assertEquals('test@example.com', $user->refresh()->email);

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'email' => 'Test@Example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_email_must_be_unique_regardless_of_case(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs(User::factory()->create());

        Livewire::test('pages::settings.profile')
            ->set('name', 'Test User')
            ->set('email', 'Taken@Example.com')
            ->call('updateProfileInformation')
            ->assertHasErrors(['email']);
    }

    public function test_email_verification_status_is_unchanged_when_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test('pages::settings.profile')
            ->set('name', 'Test User')
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $response->assertHasNoErrors();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_page_does_not_offer_account_deletion(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertDontSee(__('Delete account'))
            ->assertDontSee(__('Delete your account and all of its resources'));
    }

    public function test_account_deletion_components_no_longer_exist(): void
    {
        $this->actingAs(User::factory()->create());

        $this->expectException(ComponentNotFoundException::class);

        Livewire::test('pages::settings.delete-user-modal');
    }
}
