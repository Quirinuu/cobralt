<?php
/** Dados e restauração automática da Diretoria CoBraLT. */

declare(strict_types=1);

/** @return array<int,array{nome:string,cargo:string,especialidade:?string,foto:?string,grupo:string,bio:?string,ativo:int,ordem:int}> */
function directors_default_data(): array {
    $advisorBio = json_encode([
        'Graduado em Medicina pela UFMG',
        'Residência em Cirurgia do Trauma – Hospital João XXIII',
        'Cirurgião do Trauma do HBDF / SES-DF',
        'RTD de Cirurgia do Trauma do Distrito Federal',
        'Instrutor: ATLS, PHTLS, ETC e DSTC',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return [
        ['nome' => 'Dr. Wellington Santos', 'cargo' => 'Orientador', 'especialidade' => 'Cirurgião do Trauma', 'foto' => 'assets/img/wellington-santos.jpg', 'grupo' => 'Orientador', 'bio' => $advisorBio ?: null, 'ativo' => 1, 'ordem' => 1],
        ['nome' => 'Janio Júnior Dias Sousa', 'cargo' => 'Presidente', 'especialidade' => null, 'foto' => 'assets/img/janio-junior.jpg', 'grupo' => 'Diretoria Deliberativa', 'bio' => null, 'ativo' => 1, 'ordem' => 1],
        ['nome' => 'Thalles Wilgner Neder', 'cargo' => 'Vice-Presidente', 'especialidade' => null, 'foto' => 'assets/img/thalles-wilgner.jpg', 'grupo' => 'Diretoria Deliberativa', 'bio' => null, 'ativo' => 1, 'ordem' => 2],
        ['nome' => 'Monique Maurício Costa', 'cargo' => '1ª Secretária', 'especialidade' => null, 'foto' => 'assets/img/monique-mauricio.jpg', 'grupo' => 'Diretoria Deliberativa', 'bio' => null, 'ativo' => 1, 'ordem' => 3],
        ['nome' => 'Vitor Soares Rocha', 'cargo' => '2º Secretário', 'especialidade' => null, 'foto' => 'assets/img/vitor-soares.jpg', 'grupo' => 'Diretoria Deliberativa', 'bio' => null, 'ativo' => 1, 'ordem' => 4],
        ['nome' => 'Guilherme Alves Pereira Barrozo', 'cargo' => 'Diretor Financeiro', 'especialidade' => null, 'foto' => 'assets/img/guilherme-alves.jpg', 'grupo' => 'Diretoria Deliberativa', 'bio' => null, 'ativo' => 1, 'ordem' => 5],
        ['nome' => 'Paonne Bueno', 'cargo' => 'Diretor Geral', 'especialidade' => null, 'foto' => 'assets/img/paonne-bueno.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 1],
        ['nome' => 'Kelvia Aysla', 'cargo' => 'Região Norte', 'especialidade' => null, 'foto' => 'assets/img/kelvia-aysla.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 2],
        ['nome' => 'Carolina Leopoldino', 'cargo' => 'Região Nordeste', 'especialidade' => null, 'foto' => 'assets/img/carolina-leopoldino.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 3],
        ['nome' => 'Thaís Lopes', 'cargo' => 'Região Nordeste', 'especialidade' => null, 'foto' => 'assets/img/thais-lopes.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 4],
        ['nome' => 'Gemeli Balbinot', 'cargo' => 'Região Centro-Oeste', 'especialidade' => null, 'foto' => 'assets/img/gemeli-balbinot.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 5],
        ['nome' => 'Paula Queiroz', 'cargo' => 'Região Sudeste – São Paulo', 'especialidade' => null, 'foto' => 'assets/img/paula-queiroz.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 6],
        ['nome' => 'Frederico Costa', 'cargo' => 'Região Sudeste – Minas Gerais', 'especialidade' => null, 'foto' => 'assets/img/frederico-costa.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 7],
        ['nome' => 'Laís Destri', 'cargo' => 'Região Sul', 'especialidade' => null, 'foto' => 'assets/img/lais-destri.jpg', 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 8],
        ['nome' => 'Em definição', 'cargo' => 'Região Sudeste – RJ/ES', 'especialidade' => null, 'foto' => null, 'grupo' => 'Diretoria Executiva', 'bio' => null, 'ativo' => 1, 'ordem' => 9],
        ['nome' => 'Paulo Dornelles', 'cargo' => 'Diretoria de Prevenção', 'especialidade' => null, 'foto' => 'assets/img/paulo-dornelles.jpg', 'grupo' => 'Diretoria de Prevenção e Extensão', 'bio' => null, 'ativo' => 1, 'ordem' => 1],
        ['nome' => 'João Guilherme Roos', 'cargo' => 'Diretoria de Extensão', 'especialidade' => null, 'foto' => 'assets/img/joao-guilherme.jpg', 'grupo' => 'Diretoria de Prevenção e Extensão', 'bio' => null, 'ativo' => 1, 'ordem' => 2],
        ['nome' => 'Gabriel Pereira', 'cargo' => 'Diretor Interdisciplinar', 'especialidade' => null, 'foto' => 'assets/img/gabriel-pereira.jpg', 'grupo' => 'Diretoria Interdisciplinar', 'bio' => null, 'ativo' => 1, 'ordem' => 1],
        ['nome' => 'Gabriela Mohr', 'cargo' => 'Diretoria de Marketing', 'especialidade' => null, 'foto' => 'assets/img/gabriela-mohr.jpg', 'grupo' => 'Diretoria de Marketing', 'bio' => null, 'ativo' => 1, 'ordem' => 1],
        ['nome' => 'Júlia Manzke', 'cargo' => 'Diretoria de Marketing', 'especialidade' => null, 'foto' => 'assets/img/julia-simon.jpg', 'grupo' => 'Diretoria de Marketing', 'bio' => null, 'ativo' => 1, 'ordem' => 2],
        ['nome' => 'Lívia Pio', 'cargo' => 'Diretoria de Marketing', 'especialidade' => null, 'foto' => 'assets/img/livia-pio.jpg', 'grupo' => 'Diretoria de Marketing', 'bio' => null, 'ativo' => 1, 'ordem' => 3],
        ['nome' => 'Gabriel Quirino', 'cargo' => 'Site Dev · Diretoria de Marketing', 'especialidade' => null, 'foto' => 'assets/img/gabriel-quirino.jpg', 'grupo' => 'Diretoria de Marketing', 'bio' => null, 'ativo' => 1, 'ordem' => 4],
    ];
}

function directors_seed_if_empty(PDO $db): bool {
    if ((int)$db->query('SELECT COUNT(*) FROM diretoria')->fetchColumn() > 0) {
        return false;
    }

    $insert = $db->prepare(
        'INSERT INTO diretoria (nome, cargo, especialidade, foto, grupo, bio, ativo, ordem)
         VALUES (:nome, :cargo, :especialidade, :foto, :grupo, :bio, :ativo, :ordem)'
    );
    foreach (directors_default_data() as $director) {
        $insert->execute($director);
    }
    return true;
}
