<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/prevention_document_page.php';

render_prevention_document([
    'title' => 'Prevenção de Trauma no Idoso',
    'breadcrumb' => 'Trauma no Idoso',
    'description' => 'Material educativo do CoBraLT sobre prevenção de trauma na pessoa idosa.',
    'subtitle' => 'Estratégias e orientações para reduzir riscos, preservar a autonomia e promover mais segurança e qualidade de vida para a pessoa idosa.',
    'pages' => 53,
    'pdf' => 'prevencao-trauma-idoso-2026.pdf',
    'download_name' => 'Prevencao de Trauma no Idoso - CoBraLT 2026.pdf',
    'summary_title' => 'Segurança, autonomia e cuidado contínuo',
    'summary' => [
        'O material apresenta as particularidades do trauma geriátrico e mostra como fragilidade, comorbidades, medicamentos, capacidade funcional e alterações fisiológicas influenciam o risco, a avaliação e a recuperação da pessoa idosa.',
        'A publicação reúne orientações de prevenção para quedas, acidentes de trânsito, violência e maus-tratos, além de destacar o cuidado multiprofissional, a recuperação funcional e possibilidades de atuação educativa das ligas acadêmicas.',
    ],
    'highlights' => [
        'Envelhecimento, fragilidade e impactos nos principais sistemas do organismo.',
        'Quedas, trauma no trânsito, violência e maus-tratos como mecanismos relevantes.',
        'Atendimento integrado, reabilitação e preservação da independência.',
        'Ambiente seguro, exercício, revisão de medicamentos, saúde óssea e ações comunitárias de prevenção.',
    ],
]);
