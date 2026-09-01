<?php
/** Atualizações de conteúdo que precisam rodar mesmo com migrações desativadas. */

declare(strict_types=1);

function apply_content_update_20260901(PDO $db): bool {
    $db->exec(
        'CREATE TABLE IF NOT EXISTS content_updates (
            update_key VARCHAR(100) PRIMARY KEY,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $key = '20260901_leagues_and_supporters';
    $check = $db->prepare('SELECT update_key FROM content_updates WHERE update_key = ?');
    $check->execute([$key]);
    if ($check->fetchColumn()) {
        return false;
    }

    $startedTransaction = !$db->inTransaction();
    if ($startedTransaction) {
        $db->beginTransaction();
    }

    try {
        $mark = $db->prepare('INSERT INTO content_updates (update_key) VALUES (?)');
        $mark->execute([$key]);

        $leagueLinks = [
            'Sudeste — MG' => 'pages/regiao-sudeste-mg',
            'Sudeste — SP' => 'pages/regiao-sudeste-sp',
            'Sudeste — RJ e ES' => 'pages/regiao-sudeste-rjes',
            'Nordeste' => 'pages/regiao-nordeste',
            'Norte' => 'pages/regiao-norte',
            'Centro-Oeste' => 'pages/regiao-centro-oeste',
            'Sul' => 'pages/regiao-sul',
        ];
        $leagueUpdate = $db->prepare('UPDATE ligas_regioes SET link = :link WHERE nome = :nome');
        foreach ($leagueLinks as $name => $link) {
            $leagueUpdate->execute(['link' => $link, 'nome' => $name]);
        }

        $newSupporters = [
            ['nome' => 'Karina Cuzio', 'instituicao' => 'Liga do Trauma da Unicamp', 'imagem' => 'assets/img/apoiadores/karina-cuzio.jpeg'],
            ['nome' => 'Ileana Lulic', 'instituicao' => 'Hospital Merkur, Zagreb - Croácia', 'imagem' => 'assets/img/apoiadores/ileana-lulic.jpeg'],
        ];
        $find = $db->prepare('SELECT id FROM apoiadores WHERE nome = :nome LIMIT 1');
        $insert = $db->prepare(
            'INSERT INTO apoiadores (nome, instituicao, imagem, ativo, ordem)
             VALUES (:nome, :instituicao, :imagem, 1, :ordem)'
        );
        $update = $db->prepare(
            'UPDATE apoiadores SET instituicao = :instituicao, imagem = :imagem, ativo = 1 WHERE id = :id'
        );

        foreach ($newSupporters as $supporter) {
            $find->execute(['nome' => $supporter['nome']]);
            $id = $find->fetchColumn();
            if ($id) {
                $update->execute([
                    'instituicao' => $supporter['instituicao'],
                    'imagem' => $supporter['imagem'],
                    'id' => (int)$id,
                ]);
                continue;
            }
            $supporter['ordem'] = (int)$db->query('SELECT COALESCE(MAX(ordem), 0) + 10 FROM apoiadores')->fetchColumn();
            $insert->execute($supporter);
        }

        if ($startedTransaction && $db->inTransaction()) {
            $db->commit();
        }
        return true;
    } catch (PDOException $e) {
        if ($startedTransaction && $db->inTransaction()) {
            $db->rollBack();
        }
        if ($e->getCode() === '23000') {
            return false;
        }
        throw $e;
    }
}
