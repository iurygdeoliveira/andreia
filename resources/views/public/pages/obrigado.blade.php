<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vinda! | Método Andreia 360</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-navy': '#010d26',
                        'brand-deep': '#021f3b',
                        'brand-medium': '#023059',
                        'brand-light': '#F0F4F8',
                        'accent-gold': '#D4A843',
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-[#010d26] text-[#F0F4F8] selection:bg-accent-gold selection:text-brand-navy min-h-screen flex flex-col relative overflow-x-hidden">
    <!-- Background elements -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none" aria-hidden="true">
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-brand-deep opacity-30 blur-[120px] mix-blend-screen"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[35vw] h-[35vw] rounded-full bg-brand-medium opacity-20 blur-[100px] mix-blend-screen"></div>
    </div>

    <!-- Minimal Navbar -->
    <nav class="w-full py-6 text-center border-b border-white/5" aria-label="Navegação de confirmação">
        <a href="/" class="font-heading font-black text-2xl tracking-tighter text-white inline-block">
            ANDREIA<span class="text-accent-gold">360</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center py-20 px-6">
        <div class="max-w-3xl w-full">
            <div class="text-center mb-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-accent-gold/20 text-accent-gold mb-8 shadow-[0_0_30px_rgba(212,168,67,0.2)]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="font-heading text-4xl sm:text-6xl font-black text-white mb-6 tracking-tighter uppercase leading-[0.9]">
                    Sua Vaga Está<br><span class="text-accent-gold">Confirmada!</span>
                </h1>
                <p class="text-xl text-brand-light font-light max-w-2xl mx-auto leading-relaxed">
                    Parabéns pela decisão de priorizar você{{ session('lead_name') ? ', ' . \Illuminate\Support\Str::before(session('lead_name'), ' ') : '' }}. O primeiro passo da sua transformação já foi dado no <strong>{{ $course->title ?? 'Método Andreia 360' }}</strong>.
                </p>
            </div>

            <!-- Steps -->
            <div class="bg-[#023059] rounded-[2rem] p-8 md:p-12 border border-white/10 shadow-2xl">
                <h2 class="text-2xl font-heading font-bold text-white mb-8 border-b border-white/10 pb-4">Próximos Passos</h2>
                
                <ul class="space-y-10">
                    <li class="flex gap-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-deep border border-brand-medium flex items-center justify-center text-white font-bold text-lg">1</div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-heading">Verifique seu e-mail</h3>
                            <p class="text-brand-light leading-relaxed">Enviamos para o seu e-mail os dados de acesso à plataforma onde estão os treinos, cardápios e o material inicial.</p>
                        </div>
                    </li>
                    <li class="flex gap-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-deep border border-brand-medium flex items-center justify-center text-white font-bold text-lg">2</div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-heading">Acesse a Área da Aluna</h3>
                            <p class="text-brand-light leading-relaxed mb-4">Você já pode fazer login na plataforma para iniciar suas aulas e acessar os guias alimentares.</p>
                            <a href="/login" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-accent-gold text-brand-navy font-black hover:bg-accent-goldhover transition-all duration-300">
                                Ir para Área da Aluna &rarr;
                            </a>
                        </div>
                    </li>
                    <li class="flex gap-6">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-deep border border-brand-medium flex items-center justify-center text-white font-bold text-lg">3</div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 font-heading">Suporte e Comunidade no WhatsApp</h3>
                            <p class="text-brand-light leading-relaxed mb-4">Caso precise de qualquer orientação imediata, nosso suporte exclusivo no WhatsApp está pronto para lhe atender.</p>
                            <a href="https://wa.me/55XX9XXXXXXX" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#25D366] text-brand-navy font-bold hover:bg-[#20bd5a] transition-all duration-300">
                                Falar no WhatsApp
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="mt-16 text-center text-brand-light">
                <p>Precisa de ajuda com seu acesso?</p>
                <a href="mailto:suporte@metodoandreia360.com.br" class="text-accent-gold font-bold hover:underline">suporte@metodoandreia360.com.br</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-8 border-t border-white/5 text-center text-sm text-brand-light/40">
        &copy; {{ date('Y') }} Método Andreia 360 &bull; Todos os direitos reservados.
    </footer>
</body>
</html>
