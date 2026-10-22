@extends('public.layouts.app')

@section('title', 'Política de Privacidade | EAFS')
@section('description', 'Saiba como a EAFS coleta, usa e protege seus dados pessoais em conformidade com a LGPD (Lei Geral de Proteção de Dados).')

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
    <h1 class="text-4xl font-extrabold text-white mb-4">Política de Privacidade</h1>
    <p class="text-gray-400 text-sm mb-10">Última atualização: {{ now()->format('d/m/Y') }}</p>

    <div class="prose prose-invert prose-brand max-w-none text-gray-300 space-y-8">

        <section aria-labelledby="intro-titulo">
            <h2 id="intro-titulo" class="text-xl font-bold text-white mb-3">1. Quem Somos</h2>
            <p>
                A <strong>EAFS — Escola de Avaliação Física e Saúde</strong> é responsável pelo tratamento dos seus dados
                pessoais coletados por meio deste site e plataforma de cursos. Em caso de dúvidas, entre em contato
                através do e-mail: <a href="mailto:[SEU_EMAIL]" class="text-brand hover:underline">[SEU_EMAIL]</a>.
            </p>
        </section>

        <section aria-labelledby="coleta-titulo">
            <h2 id="coleta-titulo" class="text-xl font-bold text-white mb-3">2. Dados que Coletamos</h2>
            <ul class="list-disc pl-5 space-y-2">
                <li>Dados de cadastro: nome, e-mail, CPF (obrigatório para emissão de certificado).</li>
                <li>Dados de pagamento: processados por gateway seguro (não armazenamos dados de cartão).</li>
                <li>Dados de uso: páginas visitadas, tempo de acesso, progresso nos cursos.</li>
                <li>Dados técnicos: endereço IP, tipo de navegador, sistema operacional.</li>
            </ul>
        </section>

        <section aria-labelledby="finalidade-titulo">
            <h2 id="finalidade-titulo" class="text-xl font-bold text-white mb-3">3. Para Que Usamos Seus Dados</h2>
            <ul class="list-disc pl-5 space-y-2">
                <li>Criação e gestão da sua conta de aluno.</li>
                <li>Emissão de certificados de conclusão.</li>
                <li>Envio de informações sobre novos cursos e atualizações (com opção de descadastro).</li>
                <li>Cumprimento de obrigações legais e fiscais.</li>
                <li>Melhoria contínua da plataforma.</li>
            </ul>
        </section>

        <section aria-labelledby="base-titulo">
            <h2 id="base-titulo" class="text-xl font-bold text-white mb-3">4. Base Legal (LGPD)</h2>
            <p>Tratamos seus dados com base nas seguintes hipóteses legais previstas na Lei nº 13.709/2018 (LGPD):</p>
            <ul class="list-disc pl-5 space-y-2">
                <li><strong>Execução de contrato</strong> — para prestação dos serviços de ensino contratados.</li>
                <li><strong>Consentimento</strong> — para comunicações de marketing (revogável a qualquer momento).</li>
                <li><strong>Obrigação legal</strong> — para cumprimento de exigências fiscais e regulatórias.</li>
                <li><strong>Legítimo interesse</strong> — para segurança da plataforma e prevenção de fraudes.</li>
            </ul>
        </section>

        <section aria-labelledby="compartilhamento-titulo">
            <h2 id="compartilhamento-titulo" class="text-xl font-bold text-white mb-3">5. Compartilhamento de Dados</h2>
            <p>Não vendemos seus dados. Podemos compartilhá-los com:</p>
            <ul class="list-disc pl-5 space-y-2">
                <li>Processadores de pagamento (ex: Stripe, PagSeguro) — para cobrança.</li>
                <li>Prestadores de serviço de e-mail — para comunicações transacionais.</li>
                <li>Autoridades públicas — quando exigido por lei.</li>
            </ul>
        </section>

        <section aria-labelledby="direitos-titulo">
            <h2 id="direitos-titulo" class="text-xl font-bold text-white mb-3">6. Seus Direitos</h2>
            <p>Conforme a LGPD, você tem direito a:</p>
            <ul class="list-disc pl-5 space-y-2">
                <li>Confirmar a existência de tratamento dos seus dados.</li>
                <li>Acessar, corrigir ou solicitar a exclusão dos seus dados.</li>
                <li>Revogar o consentimento para comunicações de marketing.</li>
                <li>Portabilidade dos dados a outro fornecedor.</li>
                <li>Reclamar perante a ANPD (Autoridade Nacional de Proteção de Dados).</li>
            </ul>
            <p class="mt-4">Para exercer seus direitos, envie e-mail para:
                <a href="mailto:[SEU_EMAIL]" class="text-brand hover:underline">[SEU_EMAIL]</a>
            </p>
        </section>

        <section aria-labelledby="cookies-titulo">
            <h2 id="cookies-titulo" class="text-xl font-bold text-white mb-3">7. Cookies e Analytics</h2>
            <p>
                Utilizamos cookies essenciais para o funcionamento da plataforma e, com seu consentimento, cookies
                analíticos (Google Analytics 4) para entender como os usuários utilizam o site. Você pode desativar
                cookies não essenciais nas configurações do seu navegador.
            </p>
        </section>

        <section aria-labelledby="contato-titulo">
            <h2 id="contato-titulo" class="text-xl font-bold text-white mb-3">8. Contato</h2>
            <p>
                Dúvidas sobre esta Política? Entre em contato pelo e-mail
                <a href="mailto:[SEU_EMAIL]" class="text-brand hover:underline">[SEU_EMAIL]</a>.
            </p>
            <p class="mt-2 text-gray-500 text-sm">
                ⚠️ Este documento é um modelo base. Recomendamos revisão por advogado especializado em LGPD antes
                da publicação.
            </p>
        </section>

    </div>
</article>
@endsection
