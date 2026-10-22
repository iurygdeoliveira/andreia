<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FisiologiaExercicioCourseSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'eafs')->first()
            ?: Organization::query()->first();

        if (! $organization) {
            $organization = Organization::create([
                'name' => 'EAFS - Escola de Avaliação Física e Saúde',
                'slug' => 'eafs',
                'is_active' => true,
            ]);
        }

        /** @var Course $course */
        $course = Course::updateOrCreate(
            [
                'slug' => 'fisiologia-do-exercicio-muscular',
                'organization_id' => $organization->id,
            ],
            [
                'title' => 'Fisiologia do Exercício Muscular',
                'description' => 'Compreenda os mecanismos bioenergéticos, cardiovasculares, respiratórios, endócrinos e termorregulatórios que controlam a resposta e a adaptação do corpo humano ao exercício muscular.',
                'price_in_cents' => 197.00,
                'status' => CourseStatus::Published,
                'checkout_code' => (string) Str::uuid(),
            ]
        );

        $coverPath = public_path('images/cursos/fisiologia-exercicio/image1.jpg');
        if (file_exists($coverPath) && ! $course->hasMedia('cover')) {
            $course->addMedia($coverPath)->preservingOriginal()->toMediaCollection('cover');
        }

        // Define Modules and Lessons structure
        $curriculum = [
            [
                'title' => 'Módulo 1: Bioenergética e Trabalho Muscular',
                'sort' => 1,
                'lessons' => [
                    [
                        'title' => 'Aula 1: Conceito de Trabalho Muscular e Eficiência Mecânica',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-1-trabalho-muscular',
                    ],
                    [
                        'title' => 'Aula 2: Substratos Energéticos e Vias Metabólicas',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-2-vias-metabolicas',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 2: Ajustes Cardiovasculares e Hemodinâmicos',
                'sort' => 2,
                'lessons' => [
                    [
                        'title' => 'Aula 3: Consumo de Oxigênio (VO₂) e Débito Cardíaco',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-3-consumo-oxigenio-debito-cardiaco',
                    ],
                    [
                        'title' => 'Aula 4: Força de Cisalhamento (Shear Stress) e Ação Simpática',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-4-shear-stress-simpatica',
                    ],
                    [
                        'title' => 'Aula 5: Resposta e Controle Ortostático no Exercício',
                        'sort' => 3,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-5-controle-ortostatico',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 3: Ajustes Respiratórios e Termorregulação',
                'sort' => 3,
                'lessons' => [
                    [
                        'title' => 'Aula 6: Ajuste Respiratório e Trocas Gasosas',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-6-ajuste-respiratorio',
                    ],
                    [
                        'title' => 'Aula 7: Termorregulação, Febre do Exercício e Distúrbios Térmicos',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-7-termorregulacao-disturbios',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 4: Sistema Endócrino e Equilíbrio Hidroeletrolítico',
                'sort' => 4,
                'lessons' => [
                    [
                        'title' => 'Aula 8: Ajustes Endócrinos no Esforço Físico',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-8-ajustes-hormonais',
                    ],
                    [
                        'title' => 'Aula 9: Equilíbrio Hidroeletrolítico e Reposição Hidrolítica',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.fisiologia-exercicio.aula-9-equilibrio-hidroeletrolitico',
                    ],
                ],
            ],
        ];

        foreach ($curriculum as $modData) {
            /** @var Module $module */
            $module = Module::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $modData['title'],
                    'organization_id' => $organization->id,
                ],
                [
                    'sort' => $modData['sort'],
                ]
            );

            foreach ($modData['lessons'] as $lesData) {
                Lesson::updateOrCreate(
                    [
                        'module_id' => $module->id,
                        'title' => $lesData['title'],
                        'organization_id' => $organization->id,
                    ],
                    [
                        'sort' => $lesData['sort'],
                        'view_path' => $lesData['view_path'],
                    ]
                );
            }
        }

        // Matricular os usuários cadastrados da organização para testes no painel do aluno
        $students = User::all();
        foreach ($students as $student) {
            Enrollment::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                ],
                [
                    'organization_id' => $organization->id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]
            );
        }
    }
}
