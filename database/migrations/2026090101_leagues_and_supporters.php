<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/leagues.php';
require_once dirname(__DIR__, 2) . '/includes/supporters.php';

return [
    'id' => '2026090101_leagues_and_supporters',
    'description' => 'Corrige links das ligas e adiciona Karina Cuzio e Ileana Lulic.',
    'up' => static function (PDO $db): void {
        leagues_seed_if_empty($db);

        $leagueUpdate = $db->prepare('UPDATE ligas_regioes SET link = :link WHERE nome = :nome');
        foreach (leagues_default_data() as $league) {
            $leagueUpdate->execute(['link' => $league['link'], 'nome' => $league['nome']]);
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
    },
];
