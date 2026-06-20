<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_accepts_valid_submission(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Иван Петров',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'message' => 'Нужен рекламный ролик для запуска продукта.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Иван Петров',
            'email' => 'ivan@example.com',
        ]);
    }

    public function test_contact_form_accepts_submission_without_email_and_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Иван Петров',
            'phone' => '8 (999) 123-45-67',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Иван Петров',
            'phone' => '+7 (999) 123-45-67',
            'email' => null,
            'message' => '',
        ]);
    }

    public function test_contact_form_stores_source_context(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Иван Петров',
            'phone' => '8 (999) 123-45-67',
            'source_section' => 'pricing',
            'source_label' => 'Стандартный ролик (от 25 000 ₽)',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('contact_submissions', [
            'source_section' => 'pricing',
            'source_label' => 'Стандартный ролик (от 25 000 ₽)',
        ]);
    }

    public function test_contact_form_requires_name(): void
    {
        $response = $this->from('/')->post('/contact', [
            'name' => '',
            'phone' => '8 (999) 123-45-67',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['name']);
        $this->assertSame(0, ContactSubmission::count());
    }

    public function test_contact_form_rejects_invalid_phone(): void
    {
        $response = $this->from('/')->post('/contact', [
            'name' => 'Test User',
            'phone' => '12345',
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertSame(0, ContactSubmission::count());
    }

    public function test_contact_form_requires_phone(): void
    {
        $response = $this->from('/')->post('/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    public function test_contact_form_normalizes_russian_phone(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test User',
            'phone' => '8 (999) 123-45-67',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('contact_submissions', [
            'phone' => '+7 (999) 123-45-67',
        ]);
    }
}
