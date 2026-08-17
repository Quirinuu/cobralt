<?php
/**
 * Dados e helpers compartilhados dos apoiadores.
 */

declare(strict_types=1);

/** @return array<int,array{nome:string,instituicao:string,imagem:string,ativo:int,ordem:int}> */
function supporters_default_data(): array {
    $items = [
        ['Amauri Clemente da Rocha', '', 'amauri-clemente.png'],
        ['André Canesso Pierro', '', 'andre-pierro.png'],
        ['Andrea de Melo Alexandre Fraga', '', 'andrea-fraga.png'],
        ['Antonio Toshimitsu Onimaru', '', 'antonio-toshimitsu.png'],
        ['Dr. Caio Duarte', '', 'caio-duarte.png'],
        ['Cesar Vanderlei Carmona', '', 'cesar-vanderlei.png'],
        ['Cláudio Diunky Okawa', '', 'claudio-diunky.png'],
        ['Cristhian Jaillita Meneses', '', 'cristhian-jaillita.png'],
        ['Daniel de Souza Lima', '', 'daniel-de-souza-lima.jpeg'],
        ['Dóris M. Lazzarotto Swarowsky', 'Liga Acadêmica do Trauma da Universidade de Santa Cruz do Sul', 'doris-lazzarotto-swarowsky.jpeg'],
        ['Fernando Antonio C. Spencer Netto', '', 'fernando-antonio.png'],
        ['Fernando López Mozos', '', 'fernando-lopes.png'],
        ['Filipe Barcelos', '', 'filipe-barcelos.png'],
        ['Francisco Eduardo Silva', '', 'francisco-eduardo.png'],
        ['Frederico Michelino', '', 'frederico-michelino.png'],
        ['Guilherme Biazotto', '', 'guilherme-biazotto.png'],
        ['Gustavo de Mendonça Borges', '', 'gustavo-mendonca.png'],
        ['Gustavo P. Fraga', '', 'gustavo-fraga.png'],
        ['Henrique José V. Silveira', '', 'henrique-jose.png'],
        ['Henrique Valério de Mesquita', '', 'henrique-valerio.png'],
        ['Jackson Vinícius de Lima Bertuol', '', 'jackson-vinicius.png'],
        ['José Alberto Fernandes da Silva Filho', '', 'jose-alberto.png'],
        ['José Aurélio Ramalho', '', 'jose-aurelio.png'],
        ['José Benedito Bortoto', '', 'jose-benedito-bortoto.png'],
        ['Larissa Berbert', '', 'larissa-berbert.png'],
        ['Lucas Fileni', '', 'lucas-fileni.png'],
        ['Lucas Xavier', '', 'lucas-xavier.jpeg'],
        ['Luis Teodoro da Luz', '', 'luis-teodoro.png'],
        ['Nara Gelle de Oliveira', '', 'nara-gelle.png'],
        ['Phillipe Abreu', '', 'phillipe-abreu.png'],
        ['Dr. Renato Diniz Lins', '', 'renato-diniz.png'],
        ['Renato Nunes Justino', '', 'renato-nunes-justino.jpeg'],
        ['Rodrigo Barros de Carvalho', '', 'rodrigo-barros.png'],
        ['Rodrigo Caselli Belém', '', 'rodrigo-caselli.png'],
        ['Romeo L. Simões', '', 'romeo-simoes.png'],
        ['Prof. Dr. Santiago Servin', '', 'santiago-servin.png'],
        ['Saulo Ferreira', '', 'saulo-ferreira.png'],
        ['Thiago R. Calderan', '', 'thiago-calderan.png'],
        ['Tiago Leal Ghezzi', '', 'tiago-leal.png'],
        ['Vinicius Sampaio', '', 'vinicius-sampaio.png'],
        ['Vitor F. Kruger', '', 'vitor-kruger.png'],
        ['Waldemar Prandi Filho', '', 'waldemar-prandi.png'],
        ['Wellington José dos Santos', '', 'wellington-santos.png'],
        ['Wesley Costa', '', 'wesley-costa.jpeg'],
        ['Willian G. Hashimoto H. de Sousa', '', 'willian-hashimoto.png'],
    ];

    return array_map(
        static fn(array $item, int $index): array => [
            'nome' => $item[0],
            'instituicao' => $item[1],
            'imagem' => 'assets/img/apoiadores/' . $item[2],
            'ativo' => 1,
            'ordem' => ($index + 1) * 10,
        ],
        $items,
        array_keys($items)
    );
}

/**
 * Preenche o cadastro inicial quando a tabela existe, mas ainda está vazia.
 * Isso também cobre hospedagens em que a tabela é criada antes da primeira
 * execução das migrações.
 */
function supporters_seed_if_empty(PDO $db): bool {
    if ((int)$db->query('SELECT COUNT(*) FROM apoiadores')->fetchColumn() > 0) {
        return false;
    }

    $insert = $db->prepare(
        'INSERT INTO apoiadores (nome, instituicao, imagem, ativo, ordem)
         VALUES (:nome, :instituicao, :imagem, :ativo, :ordem)'
    );
    foreach (supporters_default_data() as $supporter) {
        $insert->execute($supporter);
    }
    return true;
}

/** @return array<int,array<string,mixed>> */
function supporters_get_active(): array {
    try {
        if (!function_exists('getPublicDB')) {
            require_once __DIR__ . '/db.php';
        }
        $db = getPublicDB();
        supporters_seed_if_empty($db);
        $rows = $db->query(
            'SELECT id, nome, instituicao, imagem, ativo, ordem
             FROM apoiadores
             WHERE ativo = 1
             ORDER BY ordem ASC, nome ASC'
        )->fetchAll();
        return is_array($rows) ? $rows : [];
    } catch (Throwable $e) {
        error_log('[CoBraLT] Falha ao carregar apoiadores: ' . $e->getMessage());
        return supporters_default_data();
    }
}

function supporter_image_src(string $image, string $prefix = ''): string {
    $image = trim($image);
    if ($image === '') {
        return '';
    }
    if (preg_match('#^(?:https?:)?//#i', $image) || str_starts_with($image, 'data:')) {
        return $image;
    }
    return $prefix . ltrim($image, '/');
}

function supporter_initials(string $name): string {
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_substr($part, 0, 1, 'UTF-8');
    }
    return mb_strtoupper($initials ?: 'AP', 'UTF-8');
}
