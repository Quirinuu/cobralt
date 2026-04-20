<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';

try {
    $db = getPublicDB();
    $ligas = $db->query(
        "SELECT emoji, nome, descricao, contagem, link
         FROM ligas_regioes WHERE ativo = 1 ORDER BY ordem ASC"
    )->fetchAll();
    $total = array_sum(array_map(fn($l) => (int)$l['contagem'], $ligas));
} catch (PDOException $e) {
    $ligas = [];
    $total = 0;
}

layout_head('Ligas Filiadas', 'Rede nacional de ligas acadêmicas de trauma filiadas ao CoBraLT.');
layout_header('ligas');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Ligas Filiadas</span>
    </nav>
    <div class="page-hero-label">Rede Nacional</div>
    <h1 class="page-hero-title">Ligas Filiadas</h1>
    <p class="page-hero-sub">180+ instituições organizadas em <?= count($ligas) ?> ligas regionais, cobrindo todo o território nacional.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">180+</div><div class="region-stat-label">instituições filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value"><?= count($ligas) ?></div><div class="region-stat-label">ligas regionais</div></div>
      <div class="region-stat"><div class="region-stat-value">5</div><div class="region-stat-label">regiões do Brasil</div></div>
      <div class="region-stat"><div class="region-stat-value">2003</div><div class="region-stat-label">fundação do CoBraLT</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <div class="leagues-grid">
      <?php if (empty($ligas)): ?>
        <p style="color:var(--slate-400);grid-column:1/-1;text-align:center;">Nenhuma liga cadastrada.</p>
      <?php else: foreach ($ligas as $i => $liga): ?>
      <div class="league-card" data-animate data-animate-delay="<?= $i ?>">
        <div class="league-icon"><?= h($liga['emoji']) ?></div>
        <h3><?= h($liga['nome']) ?></h3>
        <p><?= h($liga['descricao']) ?></p>
        <span class="league-count"><?= h($liga['contagem']) ?></span>
        <a href="<?= h(str_replace('pages/', '', $liga['link'])) ?>" class="news-link" style="margin-top:.75rem;display:inline-flex;">Ver ligas →</a>
      </div>
      <?php endforeach; endif; ?>
    </div>


  </div>
</section>

<!-- BENEFÍCIOS -->
<section class="section" id="beneficios" style="background:linear-gradient(180deg,var(--off-white) 0%,#fff 100%);" aria-labelledby="beneficios-title">
  <div class="section-inner" style="max-width:960px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Vantagens exclusivas</span>
      <h2 class="section-title" id="beneficios-title">Benefícios da Filiação</h2>
      <p class="section-subtitle">Ao filiar sua liga ao CoBraLT, você conecta sua instituição a uma rede nacional de excelência em trauma.</p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin-top:2rem;">
      <div class="benefit-card" data-animate><div class="benefit-icon">🏥</div><div class="benefit-content"><h4>Programas Clínicos</h4><p>Participação no <strong>Programa Salvando Vidas</strong>, <strong>P.A.R.T.Y</strong> e participação e sediamento no <strong>Conecta Trauma</strong>.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="1"><div class="benefit-icon">📚</div><div class="benefit-content"><h4>Discussão de Casos</h4><p>Participação na <strong>Discussão Mensal de Casos Clínicos</strong> com alunos e orientadores de todo o Brasil.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="2"><div class="benefit-icon">🎓</div><div class="benefit-content"><h4>Congressos com Desconto</h4><p>Desconto nos <strong>CoLTs e congressos parceiros</strong>. Inscritos no CoLT 2026 têm acesso liberado aos <strong>Pré-CoLTs</strong>.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="3"><div class="benefit-icon">🔬</div><div class="benefit-content"><h4>Pesquisa Científica</h4><p>Apoio em <strong>produção e divulgação científica</strong>, participação em <strong>projetos a nível nacional</strong> e apoio institucional em eventos.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="4"><div class="benefit-icon">📋</div><div class="benefit-content"><h4>Consultoria Institucional</h4><p>Consultoria para <strong>abertura e manutenção de ligas acadêmicas</strong> e <strong>cartilhas de prevenção</strong> revisadas pelo comitê.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="5"><div class="benefit-icon">🌐</div><div class="benefit-content"><h4>Rede Nacional</h4><p>Integração com ligas de todas as regiões, projetos interestadais e oportunidade de <strong>intercâmbio nacional e internacional</strong>.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="6"><div class="benefit-icon">🏆</div><div class="benefit-content"><h4>Premiações & Reconhecimento</h4><p>Premiações anuais, possibilidade de sediar o <strong>CoLT 2027</strong> e acesso facilitado a <strong>órgãos de regulação de trauma</strong>.</p></div></div>
      <div class="benefit-card" data-animate data-animate-delay="7"><div class="benefit-icon">👨‍🏫</div><div class="benefit-content"><h4>Acesso a Especialistas</h4><p>Acesso direto aos <strong>orientadores do comitê</strong> para palestras, cursos e escritas científicas.</p></div></div>
    </div>
    <div style="text-align:center;margin-top:2.5rem;" data-animate>
      <a href="../index.php#filiacao" class="btn btn-affiliate">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Filiar minha liga agora
      </a>
    </div>
  </div>
</section>

<style>
.benefit-card { background:var(--white); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:1.4rem 1.5rem; display:flex; gap:1rem; align-items:flex-start; box-shadow:var(--shadow-sm); transition:all var(--transition); position:relative; overflow:hidden; }
.benefit-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--sky),var(--navy)); transform:scaleX(0); transition:transform var(--transition); }
.benefit-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); }
.benefit-card:hover::before { transform:scaleX(1); }
.benefit-icon { width:40px; height:40px; background:linear-gradient(135deg,rgba(14,165,233,0.12),rgba(1,54,132,0.1)); border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.benefit-content h4 { font-family:var(--font-display); color:var(--navy); font-size:0.9rem; font-weight:700; margin-bottom:0.35rem; }
.benefit-content p { color:var(--slate-600); font-size:0.8rem; line-height:1.65; margin:0; }
.benefit-content strong { color:var(--navy); }
@media (max-width:600px) { .benefit-card { flex-direction:column; gap:0.75rem; } }
</style>

</main>

<?php layout_footer('../'); ?>
