-- CoBraLT — sincronização do módulo de Notícias
-- Execute este arquivo uma única vez no phpMyAdmin da Hostinger.
-- Ele preserva a tabela e os usuários, mas remove todas as publicações antigas
-- da tabela `posts` e deixa somente as duas notícias oficiais abaixo.

SET NAMES utf8mb4;
SET @schema_name = DATABASE();

-- Compatibilidade com versões anteriores do banco.
SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE posts ADD COLUMN tipo VARCHAR(30) NOT NULL DEFAULT ''noticias'' AFTER slug',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name AND TABLE_NAME = 'posts' AND COLUMN_NAME = 'tipo'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE posts ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name AND TABLE_NAME = 'posts' AND COLUMN_NAME = 'updated_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

START TRANSACTION;

DELETE FROM posts;

-- A instalação da Hostinger exige que toda notícia pertença a um usuário
-- existente. Seleciona automaticamente o primeiro administrador cadastrado.
SET @news_author_id = (SELECT MIN(id) FROM admin_users);

INSERT INTO posts
  (author_id, title, slug, tipo, excerpt, content, cover_image, category, status, published_at)
VALUES
  (
    @news_author_id,
    'Gestão 2026 toma posse',
    'gestao-2026-toma-posse',
    'noticias',
    'A nova gestão do CoBraLT inicia seu ciclo com o compromisso de aproximar as ligas, fortalecer os projetos nacionais e ampliar o impacto da rede acadêmica de trauma.',
    '<p>O Comitê Brasileiro das Ligas do Trauma inicia um novo capítulo com a posse da Gestão 2026. O momento marca a continuidade de uma trajetória construída coletivamente e renova o compromisso do CoBraLT com estudantes, orientadores, profissionais e ligas acadêmicas de todas as regiões do país.</p><h2>Um novo ciclo de trabalho</h2><p>A nova gestão assume com a missão de fortalecer a integração da rede, apoiar o desenvolvimento das ligas filiadas e dar ainda mais visibilidade às iniciativas de ensino, pesquisa, prevenção e extensão em trauma.</p><p>Entre as prioridades estão a proximidade com as representações regionais, a organização dos projetos nacionais, a comunicação transparente e a criação de oportunidades que conectem diferentes instituições em torno de objetivos comuns.</p><blockquote>Uma nova gestão começa valorizando a história já construída e transformando colaboração em novos caminhos para toda a rede.</blockquote><h2>Compromisso com as ligas</h2><p>O trabalho da Gestão 2026 será guiado pela escuta e pela participação coletiva. Cada liga filiada faz parte dessa construção e contribui para que o CoBraLT continue sendo um espaço de formação, cooperação e transformação.</p><p>Com responsabilidade e entusiasmo, a nova diretoria inicia suas atividades preparada para conduzir projetos, apoiar lideranças e fortalecer a presença do CoBraLT no cenário acadêmico nacional.</p>',
    'assets/img/posts/gestao-2026.svg',
    'Institucional',
    'published',
    '2026-02-24 12:00:00'
  ),
  (
    @news_author_id,
    'Dr. Wellington José dos Santos é homenageado como paraninfo da Turma IV',
    'dr-wellington-paraninfo-turma-iv',
    'noticias',
    'Orientador do Comitê CoBraLT, Dr. Wellington representou o corpo docente em uma cerimônia marcada por reconhecimento, confiança e inspiração para uma nova geração de profissionais.',
    '<p>A formação de novos profissionais também é construída por exemplos. Em uma cerimônia repleta de significado, o Prof. Me. Wellington José dos Santos foi homenageado como paraninfo da Turma IV e falou em nome do corpo docente, traduzindo em palavras a responsabilidade, a confiança e o vínculo que acompanham uma trajetória acadêmica.</p><h2>Uma homenagem construída por confiança</h2><p>Ser escolhido como paraninfo representa muito mais do que ocupar um lugar de destaque em uma solenidade. A homenagem reconhece a presença de um educador que participa da formação humana e profissional de seus alunos, compartilha conhecimento e ajuda cada estudante a transformar desafios em amadurecimento.</p><p>Ao representar o corpo docente, Dr. Wellington deu voz a professores que acompanham de perto o desenvolvimento da turma. É um momento que celebra a conclusão de uma etapa e reforça valores que seguem com cada formando: ética, responsabilidade, dedicação e cuidado com o próximo.</p><blockquote>Uma turma escolhe como referência quem, ao longo do caminho, soube ensinar também pelo exemplo.</blockquote><h2>Referência para o Comitê CoBraLT</h2><p>Para o Comitê Brasileiro das Ligas do Trauma, a homenagem tem um significado especial. Dr. Wellington é orientador do Comitê e contribui para que projetos, lideranças e novas gerações encontrem direção, segurança e propósito em suas iniciativas.</p><p>Sua atuação mostra como a orientação qualificada fortalece não apenas a formação acadêmica, mas também o compromisso coletivo com a educação, a ciência e o atendimento ao trauma.</p><h2>O valor de quem orienta caminhos</h2><p>O CoBraLT parabeniza o Dr. Wellington José dos Santos pela homenagem e pela confiança recebida da Turma IV. Que esse reconhecimento registre a importância de sua contribuição e inspire a continuidade de um trabalho que aproxima pessoas, fortalece a educação e ajuda a construir caminhos para o futuro.</p>',
    'assets/img/posts/wellington-paraninfo-turma-iv.jpeg',
    'Institucional',
    'published',
    '2026-07-25 14:00:00'
  );

COMMIT;

-- Conferência: o resultado deve retornar exatamente duas linhas.
SELECT id, title, slug, tipo, status, cover_image, published_at
FROM posts
ORDER BY published_at DESC;
