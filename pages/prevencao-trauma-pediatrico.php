<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/prevention_document_page.php';

render_prevention_document([
    'title' => 'Prevenção de Trauma Pediátrico',
    'breadcrumb' => 'Trauma Pediátrico',
    'description' => 'Material educativo do CoBraLT sobre prevenção de trauma em crianças e adolescentes.',
    'subtitle' => 'Informação para proteger crianças e adolescentes, fortalecer ambientes seguros e ampliar a educação em saúde junto às famílias e comunidades.',
    'pages' => 46,
    'pdf' => 'prevencao-trauma-pediatrico-2026.pdf',
    'download_name' => 'Prevencao de Trauma Pediatrico - CoBraLT 2026.pdf',
    'summary_title' => 'Proteger hoje, garantir o amanhã',
    'summary' => [
        'A publicação aborda a epidemiologia e as particularidades do trauma pediátrico, relacionando idade, desenvolvimento, ambiente e supervisão aos principais mecanismos de lesão em crianças e adolescentes.',
        'O conteúdo reúne estratégias para prevenção no trânsito, em casa, na escola e em atividades recreativas, além de discutir primeiros socorros, trabalho multiprofissional, participação familiar e o papel das ligas acadêmicas na educação em saúde.',
    ],
    'highlights' => [
        'Quedas, trânsito, queimaduras, afogamentos, intoxicações, violência e trauma esportivo.',
        'Particularidades anatômicas e fisiológicas da população pediátrica.',
        'Segurança no ambiente doméstico, escolar, esportivo e comunitário.',
        'Educação de famílias, primeiros socorros e ações extensionistas das ligas acadêmicas.',
    ],
]);
