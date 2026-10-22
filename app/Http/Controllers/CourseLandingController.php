<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\PurchaseType;
use App\Models\Course;
use App\Models\Lead;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Ponte de conversao enquanto o checkout (UC-04) nao existe.
 *
 * A landing nao tinha para onde converter: todos os CTAs terminavam em
 * route('login'), e o painel de auth nao expoe ->registration(). Ate haver
 * gateway de pagamento, o visitante frio deixa os dados aqui e a venda e
 * fechada manualmente pelo WhatsApp.
 */
final class CourseLandingController
{
    public function show(Course $cursoPublicado, Request $request): View
    {
        $planSlug = (string) $request->query('plano', 'premium');
        $selectedPlan = PurchaseType::tryFromSlug($planSlug) ?? PurchaseType::Premium;

        return view('public.pages.curso', [
            'course' => $cursoPublicado,
            'selectedPlan' => $selectedPlan,
        ]);
    }

    public function storeLead(Request $request, Course $cursoPublicado): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'profile' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required' => 'Precisamos do seu nome para emitir o certificado corretamente.',
            'email.required' => 'Informe o e-mail onde você quer receber o acesso.',
            'email.email' => 'Esse e-mail parece incompleto. Confira e tente de novo.',
            'phone.required' => 'Informe um WhatsApp para liberarmos seu acesso.',
        ]);

        Lead::create([...$data, 'course_id' => $cursoPublicado->getKey()]);

        return to_route('curso.obrigado', $cursoPublicado)
            ->with('lead_name', $data['name']);
    }

    public function obrigado(Course $cursoPublicado): View
    {
        // Peak-End: quem chega aqui sem ter acabado de enviar o formulario
        // (refresh, link colado) volta para a pagina do curso.
        if (! session()->has('lead_name')) {
            return view('public.pages.curso', ['course' => $cursoPublicado]);
        }

        return view('public.pages.obrigado', ['course' => $cursoPublicado]);
    }

    /**
     * So cursos publicados sao acessiveis pela landing.
     *
     * A chave e o checkout_code (uuid), que ja e o getRouteKeyName() do model
     * e tem indice unique. O slug foi descartado de proposito: a coluna nao e
     * unique e o app e multi-tenant, entao dois cursos de organizacoes
     * diferentes podem gerar o mesmo slug e o binding ficaria ambiguo.
     */
    public static function resolvePublished(string $chave): ?Course
    {
        if (! Str::isUuid($chave)) {
            return null;
        }

        return Course::query()
            ->where('checkout_code', $chave)
            ->where('status', CourseStatus::Published)
            ->with(['media', 'tags'])
            ->first();
    }
}
