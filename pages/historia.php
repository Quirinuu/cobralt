<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('História', 'História do CoBraLT — Desde 1999 conectando as Ligas do Trauma do Brasil.');
layout_header('historia');
?>

<main id="main-content">

<!-- HERO -->
<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>História</span>
    </nav>
    <div class="page-hero-label">Desde 1999</div>
    <h1 class="page-hero-title">A História do CoBraLT</h1>
    <p class="page-hero-sub">De um congresso idealizado por estudantes ao maior comitê de ligas acadêmicas de trauma do Brasil — uma trajetória de mais de 25 anos em prol da vida.</p>
    <div class="region-stats">
      <div class="region-stat">
        <div class="region-stat-value">1999</div>
        <div class="region-stat-label">primeiro CoLT</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">180+</div>
        <div class="region-stat-label">instituições filiadas</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">2mil+</div>
        <div class="region-stat-label">participantes no CoLT</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">5</div>
        <div class="region-stat-label">regiões do Brasil</div>
      </div>
    </div>
  </div>
</div>

<!-- QUEM SOMOS — 5 pilares -->
<section class="section" style="background:var(--off-white);border-bottom:1px solid var(--slate-200);">
  <div class="section-inner">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Quem somos</span>
      <h2 class="section-title">O CoBraLT em 5 pilares</h2>
      <p class="section-subtitle" style="margin:0 auto;">O Comitê Brasileiro das Ligas do Trauma é a associação nacional que conecta as Ligas do Trauma de todo o Brasil, promovendo integração acadêmica, excelência no ensino e impacto direto na formação médica e na assistência ao paciente traumatizado.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;margin-top:2rem;">

      <div class="contact-form" data-animate style="padding:1.5rem;display:flex;flex-direction:column;gap:.75rem;">
        <div style="font-size:1.6rem;">🧠</div>
        <h3 style="font-family:var(--font-display);color:var(--navy);font-size:1rem;font-weight:800;">Quem Somos</h3>
        <p style="font-size:.82rem;color:var(--slate-600);line-height:1.7;">O CoBraLT é o órgão nacional que conecta as Ligas do Trauma de todo o Brasil, promovendo integração acadêmica, excelência no ensino e impacto direto na formação médica e na assistência ao paciente traumatizado.</p>
      </div>

      <div class="contact-form" data-animate data-animate-delay="1" style="padding:1.5rem;display:flex;flex-direction:column;gap:.75rem;">
        <div style="font-size:1.6rem;">🌎</div>
        <h3 style="font-family:var(--font-display);color:var(--navy);font-size:1rem;font-weight:800;">Rede Nacional de Ligas</h3>
        <p style="font-size:.82rem;color:var(--slate-600);line-height:1.7;">Uma das maiores redes acadêmicas da saúde no país, com ligas distribuídas em todas as regiões, promovendo colaboração entre universidades, serviços de saúde e profissionais da área do trauma.</p>
      </div>

      <div class="contact-form" data-animate data-animate-delay="2" style="padding:1.5rem;display:flex;flex-direction:column;gap:.75rem;">
        <div style="font-size:1.6rem;">🎓</div>
        <h3 style="font-family:var(--font-display);color:var(--navy);font-size:1rem;font-weight:800;">Formação e Excelência Acadêmica</h3>
        <p style="font-size:.82rem;color:var(--slate-600);line-height:1.7;">Atuação direta na capacitação de estudantes e profissionais por meio de ensino, pesquisa e extensão, fortalecendo a formação em urgência, emergência e cirurgia do trauma.</p>
      </div>

      <div class="contact-form" data-animate data-animate-delay="3" style="padding:1.5rem;display:flex;flex-direction:column;gap:.75rem;">
        <div style="font-size:1.6rem;">🚑</div>
        <h3 style="font-family:var(--font-display);color:var(--navy);font-size:1rem;font-weight:800;">Impacto em Saúde Pública</h3>
        <p style="font-size:.82rem;color:var(--slate-600);line-height:1.7;">Desenvolvimento e apoio a projetos nacionais de prevenção ao trauma, educação comunitária e redução da mortalidade por causas externas, em parceria com diversas instituições.</p>
      </div>

      <div class="contact-form" data-animate data-animate-delay="4" style="padding:1.5rem;display:flex;flex-direction:column;gap:.75rem;">
        <div style="font-size:1.6rem;">🏛️</div>
        <h3 style="font-family:var(--font-display);color:var(--navy);font-size:1rem;font-weight:800;">Organização e Representatividade</h3>
        <p style="font-size:.82rem;color:var(--slate-600);line-height:1.7;">Responsável pela articulação nacional das ligas e pela organização anualmente do Congresso Brasileiro das Ligas do Trauma (CoLT), o maior evento acadêmico de trauma do país.</p>
      </div>

    </div>
  </div>
</section>

<!-- HISTÓRIA COMPLETA -->
<section class="section">
  <div class="section-inner" style="max-width:860px;">

    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Linha do tempo</span>
      <h2 class="section-title">Nossa Trajetória</h2>
      <p class="section-subtitle">Cada etapa, um novo capítulo na história do atendimento ao trauma no Brasil.</p>
    </div>

    <div class="timeline-full">

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">Anos 80–90</div>
        <h3>🧠 Origem do Movimento das Ligas do Trauma</h3>
        <p>A história do CoBraLT está diretamente ligada ao crescimento do ensino do trauma no Brasil. Desde as décadas de 1980 e 1990, com a consolidação da cirurgia do trauma como área essencial na formação médica e a criação da Sociedade Brasileira de Atendimento Integrado ao Traumatizado (SBAIT), em 1982, surgiu a necessidade de ampliar o acesso ao conhecimento na área.</p>
        <p style="margin-top:.75rem;">Nesse contexto, as Ligas Acadêmicas do Trauma emergiram como um importante instrumento de ensino, pesquisa e extensão, permitindo que estudantes de medicina tivessem contato prático e teórico com a abordagem ao paciente traumatizado.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">1999</div>
        <h3>🔥 O Marco Inicial: Surgimento do CoLT</h3>
        <p>O grande ponto de virada ocorreu em 1999, com a realização do primeiro Congresso Brasileiro das Ligas do Trauma (CoLT), idealizado por estudantes e coordenado por importantes nomes da cirurgia do trauma no Brasil, como os professores Mario Mantovani, Dario Birolini, Evandro Freire e Gustavo P. Fraga.</p>
        <p style="margin-top:.75rem;">O evento marcou o início de uma mobilização nacional, reunindo ligas de diferentes instituições e promovendo integração entre acadêmicos, profissionais de saúde e equipes multidisciplinares envolvidas no atendimento ao trauma.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">2003</div>
        <h3>🏛️ Fundação do CoBraLT</h3>
        <p>Em 2003, foi oficialmente fundado o Comitê Brasileiro das Ligas do Trauma (CoBraLT), com o objetivo de centralizar, organizar e representar as ligas do trauma em todo o território nacional.</p>
        <p style="margin-top:.75rem;">Desde sua criação, o CoBraLT assumiu um papel estratégico, atuando como órgão de articulação entre as ligas, promovendo padronização de ações, fortalecimento institucional e ampliação da atuação acadêmica no país.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">2013</div>
        <h3>🌎 Expansão Nacional e Consolidação</h3>
        <p>A partir de 2013, o CoBraLT iniciou um processo de expansão nacional estruturada, com a criação de representações regionais e fortalecimento das ligas em todas as regiões do Brasil.</p>
        <p style="margin-top:.75rem;">Esse crescimento resultou em uma rede ampla e integrada, com dezenas de instituições filiadas e atuação ativa em ensino, pesquisa e extensão.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">2016</div>
        <h3>📋 Padronização e Qualidade Assistencial</h3>
        <p>Em 2016, foram implementados protocolos clínicos padronizados em diversas ligas filiadas, com o objetivo de melhorar a qualidade do atendimento ao paciente traumatizado e reduzir índices de morbimortalidade.</p>
        <p style="margin-top:.75rem;">Essa iniciativa representou um avanço significativo na integração entre o meio acadêmico e a prática assistencial, aproximando o estudante da realidade do sistema de saúde.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">2020</div>
        <h3>💻 Transformação Digital</h3>
        <p>Durante a pandemia de COVID-19, em 2020, o CoBraLT passou por um processo de transformação digital, adotando novas tecnologias para manter suas atividades, promover integração entre as ligas e garantir a continuidade dos projetos educacionais e científicos.</p>
        <p style="margin-top:.75rem;">A digitalização permitiu ampliar o alcance das ações, fortalecer a comunicação nacional e modernizar a gestão da rede.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">2026</div>
        <h3>🤝 Reposicionamento Institucional</h3>
        <p>A gestão do CoBraLT, após difíceis negociações com a Diretoria Executiva da SBAIT de 2024/2025, teve que tomar a difícil decisão de se desvincular da sociedade, que ainda não tem o devido reconhecimento pela Associação Médica Brasileira (AMB).</p>
        <p style="margin-top:.75rem;">Acreditamos que o fortalecimento da residência médica e a busca da especialidade são assuntos prioritários, que estão diretamente relacionados com a existência do CoBraLT e com o futuro da Cirurgia do Trauma no Brasil.</p>
      </div>

      <div class="timeline-full-item" data-animate>
        <div class="timeline-full-dot"></div>
        <div class="timeline-full-year">Hoje</div>
        <h3>🚀 CoBraLT na Atualidade</h3>
        <p>Atualmente, o CoBraLT é reconhecido como a principal organização de ligas acadêmicas de trauma no Brasil, reunindo dezenas de instituições e milhares de estudantes e profissionais da saúde.</p>
        <p style="margin-top:.75rem;">Sua atuação abrange desde a formação acadêmica até o impacto direto na sociedade, por meio de projetos de prevenção, eventos científicos e integração nacional.</p>
      </div>

    </div>

    <!-- Missão -->
    <div class="colt-banner" style="border-radius:var(--radius-xl);padding:2.5rem;margin-top:3rem;" data-animate>
      <span class="section-label" style="color:var(--sky-light);">🏁 Missão Contínua</span>
      <p style="color:rgba(255,255,255,.85);font-size:1rem;line-height:1.8;margin-top:.75rem;">Mais do que uma organização, o CoBraLT representa um <strong style="color:#fff;">movimento nacional em prol da vida</strong>, da educação médica de qualidade e da redução dos impactos do trauma na sociedade brasileira.</p>
    </div>

  </div>
</section>

<!-- CTA -->
<section class="colt-banner" style="padding:3.5rem 2rem;">
  <div style="max-width:1180px;margin:0 auto;text-align:center;">
    <span class="section-label" style="color:var(--sky-light);">Faça parte</span>
    <h2 class="section-title" style="color:#fff;margin-bottom:1rem;">Escreva a próxima página dessa história</h2>
    <p style="color:rgba(255,255,255,.6);font-size:.9rem;max-width:480px;margin:0 auto 2rem;">Afilie sua liga ao CoBraLT e contribua para a excelência no atendimento ao trauma no Brasil.</p>
    <a href="../index.php#filiacao" class="btn btn-primary">Filiar minha liga</a>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
