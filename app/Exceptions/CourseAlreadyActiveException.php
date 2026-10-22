<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Course;
use Exception;
use Illuminate\Support\Collection;

class CourseAlreadyActiveException extends Exception
{
    /**
     * @param  Collection<int, Course>  $courses
     */
    public function __construct(
        private readonly Collection $courses,
        string $message = 'Comprador já possui matrícula ativa para um ou mais cursos.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }
}
