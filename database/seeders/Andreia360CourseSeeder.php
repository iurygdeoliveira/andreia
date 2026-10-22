<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class Andreia360CourseSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'andreia360')->first();

        if (! $organization) {
            $organization = Organization::create([
                'name' => 'Método Andreia 360',
                'slug' => 'andreia360',
                'is_active' => true,
            ]);
        }

        $owner = User::query()->where('email', (string) env('OWNER_USER_EMAIL', 'andreia@metodoandreia360.com.br'))->first();
        $student = User::query()->where('email', (string) env('STUDENT_USER_EMAIL', 'aluna@metodoandreia360.com.br'))->first();

        // Limpar cursos fragmentados antigos se existirem (agora é UM ÚNICO CURSO)
        $legacySlugs = ['plano-essencial', 'plano-vip-360'];
        Course::where('organization_id', $organization->id)->whereIn('slug', $legacySlugs)->delete();

        // CURSO ÚNICO: Método Andreia 360
        /** @var Course $course */
        $course = Course::updateOrCreate(
            [
                'checkout_code' => 'b1a2c3d4-e5f6-4a1b-9c2d-3e4f5a6b7c8d',
                'organization_id' => $organization->id,
            ],
            [
                'slug' => 'metodo-andreia-360',
                'title' => 'Método Andreia 360',
                'description' => 'O método definitivo de transformação feminina 40+. Treino metabólico inteligente, nutrição hormonal adaptada e suporte contínuo para mulheres reais.',
                'price_in_cents' => 299.90,
                'status' => CourseStatus::Published,
            ]
        );

        // Se existirem outros cursos na organização, removemos para manter APENAS UM
        Course::where('organization_id', $organization->id)
            ->where('id', '!=', $course->id)
            ->delete();

        $curriculum = [
            [
                'title' => 'Módulo 1: Boas-Vindas e Fundamentos 40+',
                'sort' => 1,
                'lessons' => [
                    [
                        'title' => 'Aula 1: Seja Bem-Vinda ao Método Andreia 360',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.andreia360.aula-1-boas-vindas',
                    ],
                    [
                        'title' => 'Aula 2: Avaliação e o Mapa Hormonal 40+',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.andreia360.aula-2-avaliacao-hormonal',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 2: Treinos Estratégicos e Reativação Muscular',
                'sort' => 2,
                'lessons' => [
                    [
                        'title' => 'Aula 3: Treino A - Força e Ativação Metabólica',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.andreia360.aula-3-treino-a-forca',
                    ],
                    [
                        'title' => 'Aula 4: Treino B - Mobilidade, Postura e Core',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.andreia360.aula-4-treino-b-mobilidade',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 3: Nutrição Inteligente e Livro de Receitas',
                'sort' => 3,
                'lessons' => [
                    [
                        'title' => 'Aula 5: Nutrição Hormonal: Comer Bem sem Passar Fome',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.andreia360.aula-5-nutricao-hormonal',
                    ],
                    [
                        'title' => 'Aula 6: E-book Saladas Fit 40+: Receitas Práticas',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.andreia360.aula-6-saladas-fit',
                    ],
                    [
                        'title' => 'Aula 7: Sopas Fit e Cremes Detox: O Jantar Perfeito',
                        'sort' => 3,
                        'view_path' => 'panels.lessons.andreia360.aula-7-sopas-fit',
                    ],
                    [
                        'title' => 'Aula 8: Sobremesas Fit: Doce Aliado sem Culpa',
                        'sort' => 4,
                        'view_path' => 'panels.lessons.andreia360.aula-8-sobremesas-fit',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 4: Mentalidade e Hábitos Sustentáveis',
                'sort' => 4,
                'lessons' => [
                    [
                        'title' => 'Aula 9: Sono Reparador • O Freio do Cortisol',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.andreia360.aula-9-sono-estresse',
                    ],
                    [
                        'title' => 'Aula 10: Comunidade VIP e Mentoria Quinzenal',
                        'sort' => 2,
                        'view_path' => 'panels.lessons.andreia360.aula-10-comunidade-mentoria',
                    ],
                ],
            ],
            [
                'title' => 'Módulo 5: Acompanhamento e Mentoria 1 a 1',
                'sort' => 5,
                'lessons' => [
                    [
                        'title' => 'Aula 11: Como Agendar seus Encontros e Plantão de Dúvidas',
                        'sort' => 1,
                        'view_path' => 'panels.lessons.andreia360.aula-10-comunidade-mentoria',
                    ],
                ],
            ],
        ];

        $this->syncCurriculum($course, $curriculum, $organization->id);

        // Matrícula da Aluna Teste no curso único com Plano Premium
        if ($student) {
            Enrollment::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => EnrollmentStatus::Active,
                    'purchase_type' => PurchaseType::Premium,
                    'enrolled_at' => now(),
                    'expires_at' => now()->addYear(),
                ]
            );
        }

        // Matrícula da Owner Andreia
        if ($owner) {
            Enrollment::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'user_id' => $owner->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => EnrollmentStatus::Active,
                    'purchase_type' => PurchaseType::Vip,
                    'enrolled_at' => now(),
                    'expires_at' => now()->addYears(3),
                ]
            );
        }

        // Seed de Pedidos com diferentes Tipos de Compra para visualização imediata da Owner
        $sampleOrders = [
            [
                'order_number' => 'PED-A360-001',
                'buyer_name' => 'Carla Mendonça Silva',
                'buyer_email' => 'carla.mendonca@gmail.com',
                'buyer_phone' => '(11) 98765-4321',
                'buyer_document' => '12345678909',
                'total_amount' => PurchaseType::Vip->price(),
                'purchase_type' => PurchaseType::Vip,
                'payment_method' => OrderPaymentMethod::Pix,
                'payment_status' => OrderStatus::Confirmed,
                'paid_at' => now()->subDays(2),
            ],
            [
                'order_number' => 'PED-A360-002',
                'buyer_name' => 'Patrícia Albuquerque',
                'buyer_email' => 'patricia.albuquerque@hotmail.com',
                'buyer_phone' => '(21) 99876-5432',
                'buyer_document' => '98765432100',
                'total_amount' => PurchaseType::Premium->price(),
                'purchase_type' => PurchaseType::Premium,
                'payment_method' => OrderPaymentMethod::Pix,
                'payment_status' => OrderStatus::Confirmed,
                'paid_at' => now()->subDay(),
            ],
            [
                'order_number' => 'PED-A360-003',
                'buyer_name' => 'Renata Vasconcellos',
                'buyer_email' => 'renata.vasc@gmail.com',
                'buyer_phone' => '(31) 97654-3210',
                'buyer_document' => '45678912344',
                'total_amount' => PurchaseType::Essencial->price(),
                'purchase_type' => PurchaseType::Essencial,
                'payment_method' => OrderPaymentMethod::Pix,
                'payment_status' => OrderStatus::Confirmed,
                'paid_at' => now()->subHours(8),
            ],
            [
                'order_number' => 'PED-A360-004',
                'buyer_name' => 'Luciana Ferreira Borges',
                'buyer_email' => 'luciana.borges@uol.com.br',
                'buyer_phone' => '(41) 99123-4567',
                'buyer_document' => '78912345677',
                'total_amount' => PurchaseType::Premium->price(),
                'purchase_type' => PurchaseType::Premium,
                'payment_method' => OrderPaymentMethod::Pix,
                'payment_status' => OrderStatus::Pending,
                'paid_at' => null,
            ],
            [
                'order_number' => 'PED-A360-005',
                'buyer_name' => 'Mariana Dias Fontes',
                'buyer_email' => 'mariana.fontes@outlook.com',
                'buyer_phone' => '(71) 98888-7777',
                'buyer_document' => '32165498711',
                'total_amount' => PurchaseType::Vip->price(),
                'purchase_type' => PurchaseType::Vip,
                'payment_method' => OrderPaymentMethod::Pix,
                'payment_status' => OrderStatus::Confirmed,
                'paid_at' => now()->subHours(3),
            ],
        ];

        foreach ($sampleOrders as $orderData) {
            /** @var Order $order */
            $order = Order::updateOrCreate(
                ['order_number' => $orderData['order_number']],
                [
                    'organization_id' => $organization->id,
                    'buyer_name' => $orderData['buyer_name'],
                    'buyer_email' => $orderData['buyer_email'],
                    'buyer_phone' => $orderData['buyer_phone'],
                    'buyer_document' => $orderData['buyer_document'],
                    'total_amount' => $orderData['total_amount'],
                    'purchase_type' => $orderData['purchase_type'],
                    'payment_method' => $orderData['payment_method'],
                    'payment_status' => $orderData['payment_status'],
                    'paid_at' => $orderData['paid_at'],
                ]
            );

            OrderItem::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                ],
                [
                    'unit_price' => $orderData['total_amount'],
                ]
            );
        }
    }

    private function syncCurriculum(Course $course, array $curriculum, int $organizationId): void
    {
        foreach ($curriculum as $moduleData) {
            /** @var Module $module */
            $module = Module::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                ],
                [
                    'sort' => $moduleData['sort'],
                ]
            );

            foreach ($moduleData['lessons'] as $lessonData) {
                Lesson::updateOrCreate(
                    [
                        'organization_id' => $organizationId,
                        'module_id' => $module->id,
                        'title' => $lessonData['title'],
                    ],
                    [
                        'sort' => $lessonData['sort'],
                        'view_path' => $lessonData['view_path'],
                    ]
                );
            }
        }
    }
}
