<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Termos de Uso', 'Termos de Uso do site do CoBraLT — Comitê Brasileiro das Ligas do Trauma.');
layout_header('');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Termos de Uso</span>
    </nav>
    <div class="page-hero-label">Legal</div>
    <h1 class="page-hero-title">Termos de Uso</h1>
    <p class="page-hero-sub">Última atualização: abril de 2026</p>
  </div>
</div>

<section class="section">
  <div class="section-inner" style="max-width:800px;">
    <div class="post-content" style="font-size:1rem;line-height:1.85;color:var(--slate-700);">

      <h2>1. Aceitação dos termos</h2>
      <p>Ao acessar e utilizar o site do <strong>CoBraLT — Comitê Brasileiro das Ligas do Trauma</strong>, você concorda com os presentes Termos de Uso. Caso não concorde com qualquer disposição, pedimos que não utilize o site.</p>

      <h2>2. Sobre o CoBraLT</h2>
      <p>O CoBraLT é um comitê acadêmico sem fins lucrativos que reúne e representa as Ligas do Trauma de instituições de ensino superior de todo o Brasil. Este site tem caráter informativo e serve como canal de comunicação, divulgação de eventos e cadastro de ligas filiadas.</p>

      <h2>3. Uso permitido</h2>
      <p>O acesso ao site é gratuito e aberto ao público. Você pode:</p>
      <ul>
        <li>Navegar pelas páginas e ler o conteúdo publicado;</li>
        <li>Preencher formulários de inscrição em eventos e solicitação de filiação;</li>
        <li>Compartilhar links do site, desde que sem fins comerciais e com atribuição ao CoBraLT.</li>
      </ul>

      <h2>4. Uso proibido</h2>
      <p>É expressamente proibido:</p>
      <ul>
        <li>Tentar acessar o painel administrativo sem autorização;</li>
        <li>Utilizar bots, scrapers ou ferramentas automatizadas para extrair conteúdo do site em larga escala;</li>
        <li>Publicar ou transmitir conteúdo falso, difamatório ou que viole direitos de terceiros por meio de qualquer interação com o site;</li>
        <li>Reproduzir ou redistribuir o conteúdo do site para fins comerciais sem autorização expressa do CoBraLT;</li>
        <li>Realizar ataques de qualquer natureza contra a infraestrutura do site.</li>
      </ul>

      <h2>5. Conteúdo do site</h2>
      <p>Todo o conteúdo publicado neste site — textos, imagens, logotipos e dados — é de propriedade do CoBraLT ou de seus parceiros, e está protegido pelas leis brasileiras de direito autoral. A reprodução parcial ou total sem autorização é proibida.</p>
      <p>As opiniões expressas em publicações e notícias refletem a posição institucional do CoBraLT e não constituem aconselhamento médico ou clínico.</p>

      <h2>6. Inscrições e filiações</h2>
      <p>Ao preencher formulários de inscrição em eventos ou solicitação de filiação, você declara que as informações fornecidas são verdadeiras e completas. O CoBraLT reserva-se o direito de recusar ou cancelar inscrições e filiações em caso de informações falsas ou conduta inadequada.</p>
      <p>A aprovação de solicitações de filiação de ligas está sujeita à análise da diretoria do CoBraLT, conforme critérios internos, e não é automática.</p>

      <h2>7. Links externos</h2>
      <p>Este site pode conter links para plataformas e sites de terceiros (como plataformas de inscrição, instituições parceiras e redes sociais). O CoBraLT não se responsabiliza pelo conteúdo, disponibilidade ou práticas desses sites.</p>

      <h2>8. Disponibilidade do site</h2>
      <p>O CoBraLT envidar esforços razoáveis para manter o site disponível, mas não garante disponibilidade ininterrupta. O site pode ficar indisponível por manutenção, atualizações ou fatores fora do controle do CoBraLT.</p>

      <h2>9. Limitação de responsabilidade</h2>
      <p>O CoBraLT não se responsabiliza por danos diretos, indiretos, incidentais ou consequenciais resultantes do uso ou da impossibilidade de uso deste site, nem por decisões tomadas com base nas informações aqui publicadas.</p>

      <h2>10. Modificações dos termos</h2>
      <p>Estes Termos de Uso podem ser alterados a qualquer momento. A versão mais recente estará sempre disponível nesta página, com a data de última atualização indicada no topo. O uso continuado do site após alterações implica aceitação dos novos termos.</p>

      <h2>11. Lei aplicável e foro</h2>
      <p>Estes Termos de Uso são regidos pela legislação brasileira. Para resolução de quaisquer controvérsias, fica eleito o foro da comarca de Brasília — DF, com renúncia a qualquer outro, por mais privilegiado que seja.</p>

    </div>

    <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--slate-200);display:flex;gap:1rem;flex-wrap:wrap;">
      <a href="privacidade.php" class="btn btn-navy" style="display:inline-flex;align-items:center;gap:6px;">
        Política de Privacidade
      </a>
      <a href="../index.php" class="btn" style="display:inline-flex;align-items:center;gap:6px;background:var(--off-white);color:var(--navy);border:1px solid var(--slate-200);">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Voltar ao início
      </a>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
