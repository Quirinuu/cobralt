<?php
/** Dados e helpers compartilhados das ligas regionais. */

declare(strict_types=1);

/** @return array<int,array{emoji:string,nome:string,descricao:string,contagem:int,link:string,ativo:int,ordem:int}> */
function leagues_default_data(): array {
    return [
        ['emoji' => '🏥', 'nome' => 'Sudeste — MG', 'descricao' => 'Minas Gerais — 32 ligas acadêmicas filiadas cobrindo as principais universidades do estado.', 'contagem' => 32, 'link' => 'pages/regiao-sudeste-mg', 'ativo' => 1, 'ordem' => 1],
        ['emoji' => '🏙️', 'nome' => 'Sudeste — SP', 'descricao' => 'São Paulo — o maior polo de trauma do Brasil, com ligas nas principais universidades do estado.', 'contagem' => 45, 'link' => 'pages/regiao-sudeste-sp', 'ativo' => 1, 'ordem' => 2],
        ['emoji' => '🌊', 'nome' => 'Sudeste — RJ e ES', 'descricao' => 'Rio de Janeiro e Espírito Santo — 17 ligas cobrindo as principais instituições da sub-região.', 'contagem' => 17, 'link' => 'pages/regiao-sudeste-rjes', 'ativo' => 1, 'ordem' => 3],
        ['emoji' => '🌾', 'nome' => 'Nordeste', 'descricao' => '9 estados nordestinos — 24 ligas com protocolos adaptados à realidade regional.', 'contagem' => 24, 'link' => 'pages/regiao-nordeste', 'ativo' => 1, 'ordem' => 4],
        ['emoji' => '🌳', 'nome' => 'Norte', 'descricao' => '7 estados — 23 ligas expandindo a excelência em trauma para a Amazônia.', 'contagem' => 23, 'link' => 'pages/regiao-norte', 'ativo' => 1, 'ordem' => 5],
        ['emoji' => '🌐', 'nome' => 'Centro-Oeste', 'descricao' => 'DF, GO, MS e MT — 27 ligas atendendo o Planalto Central e região.', 'contagem' => 27, 'link' => 'pages/regiao-centro-oeste', 'ativo' => 1, 'ordem' => 6],
        ['emoji' => '🏔️', 'nome' => 'Sul', 'descricao' => 'Paraná, Rio Grande do Sul e Santa Catarina — 20 ligas em rede.', 'contagem' => 20, 'link' => 'pages/regiao-sul', 'ativo' => 1, 'ordem' => 7],
    ];
}

function leagues_seed_if_empty(PDO $db): bool {
    if ((int)$db->query('SELECT COUNT(*) FROM ligas_regioes')->fetchColumn() > 0) {
        return false;
    }

    $insert = $db->prepare(
        'INSERT INTO ligas_regioes (emoji, nome, descricao, contagem, link, ativo, ordem)
         VALUES (:emoji, :nome, :descricao, :contagem, :link, :ativo, :ordem)'
    );
    foreach (leagues_default_data() as $league) {
        $insert->execute($league);
    }
    return true;
}

function league_clean_link(string $link): string {
    return (string)preg_replace('/\.(?:html?|php)(?=([?#].*)?$)/i', '', trim($link));
}

function league_count_label(mixed $count): string {
    $value = trim((string)$count);
    return preg_match('/^\d+$/', $value) ? $value . ' ligas' : $value;
}
