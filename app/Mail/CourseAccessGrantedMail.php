<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Avisa o comprador que o acesso aos cursos foi liberado (ADR-0006, RF-07).
 *
 * Dois caminhos, de propósito: conta nova recebe link de definição de senha;
 * conta que já existia recebe apenas o aviso e o link de login, sem nada que
 * mexa na credencial dela.
 */
class CourseAccessGrantedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 30;

    /**
     * @param  Collection<int, Course>|EloquentCollection<int, Course>  $courses
     */
    public function __construct(
        public User $user,
        public Collection|EloquentCollection $courses,
        public ?string $passwordSetupUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->courses->count();
        $firstCourse = $this->courses->first();

        if ($this->passwordSetupUrl !== null) {
            $subject = $count === 1
                ? 'Seu acesso ao curso foi liberado'
                : "Seu acesso a {$count} cursos foi liberado";
        } else {
            $subject = $count === 1
                ? "Novo curso liberado na sua conta: {$firstCourse?->title}"
                : "{$count} novos cursos liberados na sua conta";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'panels.mail.course-access-granted',
        );
    }
}
