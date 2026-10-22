<!-- Footer -->
<footer class="bg-dark border-t border-dark-border pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8 mb-12 text-center md:text-left">
            <div class="md:col-span-2">
                <a href="/" class="flex items-center justify-center md:justify-start gap-2 mb-6"
                    aria-label="EAFS — Página inicial">
                    <img src="{{ asset('images/logo-nav.webp') }}" alt="EAFS" class="h-16 w-auto"
                        width="150" height="64" loading="lazy" decoding="async">
                </a>
                <p class="text-gray-400 text-sm max-w-sm mx-auto md:mx-0 mb-6">
                    Cursos online de atualização profissional para as áreas da saúde, com certificado
                    aceito pelo MEC como horas complementares.
                </p>
                {{-- Os icones so aparecem quando ha URL real em config/landing.php.
                     Antes eram tres href="#": clicar nao fazia nada, e link social
                     quebrado e sinal negativo de confianca. --}}
                @php
                    $redes = array_filter([
                        'instagram-logo' => ['url' => config('landing.social.instagram'), 'nome' => 'Instagram'],
                        'facebook-logo' => ['url' => config('landing.social.facebook'), 'nome' => 'Facebook'],
                        'youtube-logo' => ['url' => config('landing.social.youtube'), 'nome' => 'YouTube'],
                    ], fn ($rede) => filled($rede['url']));
                @endphp
                @if ($redes)
                    <div class="flex items-center justify-center md:justify-start gap-4">
                        @foreach ($redes as $icone => $rede)
                            <a href="{{ $rede['url'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="EAFS no {{ $rede['nome'] }}"
                                class="w-10 h-10 rounded-full bg-dark-surface border border-dark-border flex items-center justify-center text-gray-400 hover:text-brand hover:border-brand transition-colors">
                                <i class="ph-fill ph-{{ $icone }} text-xl" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h2 class="text-white font-bold mb-4 text-base">Navegação</h2>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#cursos" class="hover:text-brand transition-colors">Todos os Cursos</a></li>
                    <li><a href="#professor" class="hover:text-brand transition-colors">O Professor</a></li>
                    <li><a href="#faq" class="hover:text-brand transition-colors">Dúvidas Frequentes</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-white font-bold mb-4 text-base">Desenvolvido por</h2>
                <a href="https://labsis.dev.br/" target="_blank" rel="noopener noreferrer"
                    class="group relative block w-full max-w-[320px] mx-auto md:mx-0 overflow-hidden rounded-xl border border-[#a4bdbf]/20 p-5 bg-gradient-to-br from-white/[0.04] to-[#a4bdbf]/[0.06] transition-all duration-300 hover:-translate-y-0.5 hover:border-brand hover:from-white/[0.07] hover:to-[#a4bdbf]/[0.10]">
                    <div class="flex flex-col items-center justify-center text-center relative gap-2">
                        <img src="{{ asset('images/labsis_logo_bg.png') }}"
                            alt="LabSIS — Laboratório de Sistemas Inovadores"
                            width="517" height="153" loading="lazy" decoding="async"
                            class="h-10 w-auto object-contain [filter:drop-shadow(0_0_10px_rgba(164,189,191,0.15))] transition-all duration-300 group-hover:scale-105 group-hover:[filter:drop-shadow(0_0_15px_rgba(16,185,129,0.45))]">
                        <p class="text-xs text-gray-400 font-medium max-w-[220px] m-0 transition-colors group-hover:text-gray-200">
                            Laboratório de Sistemas Inovadores
                        </p>
                        <svg class="absolute -right-1 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 opacity-40 transition-all duration-300 group-hover:text-brand group-hover:opacity-100 group-hover:translate-x-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        <div
            class="pt-8 border-t border-dark-border flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <p class="text-sm text-gray-400">
                &copy; {{ now()->year }} EAFS - Escola de Avaliação Física e Saúde. Todos os direitos reservados.
            </p>
            <nav aria-label="Links legais">
                <a href="/privacidade" class="text-sm text-gray-400 hover:text-brand-light transition-colors">
                    Política de Privacidade
                </a>
            </nav>
        </div>
    </div>
</footer>
