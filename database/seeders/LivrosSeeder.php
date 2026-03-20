<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LivrosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('livros')->upsert(
            [
                [
                    'id' => 1,
                    'isbn' => '9781471136726',
                    'nome' => 'Ugly Love',
                    'editora_id' => 1,
                    'bibliografia' => '"Ugly Love", de Colleen Hoover, é um romance contemporâneo de 2014, publicado em Portugal pela TopSeller. A obra narra o relacionamento intenso e relutante entre Tate Collins e Miles Archer, com regras rígidas: não perguntar sobre o passado e não esperar futuro, abordando temas de trauma e superação.',
                    'imagem_capa' => 'capas/vzNybbBGDNKYStJIW5pLqKaEiKNCnXVyRzArMUsH.jpg',
                    'preco' => 12.99,
                    'created_at' => '2026-03-17 10:30:03',
                    'updated_at' => '2026-03-17 11:01:50',
                ],
                [
                    'id' => 2,
                    'isbn' => '9781408734254',
                    'nome' => 'The Love Hypothesis',
                    'editora_id' => 2,
                    'bibliografia' => '"The Love Hypothesis", de Ali Hazelwood é um romance contemporâneo de 2021, conhecido por ser baseado em fanfiction de Star Wars, com personagens como Olive Smith e Adam Carlsen. A obra foca-se num namoro falso entre uma doutoranda e um professor, destacando-se no género STEMinist romance.',
                    'imagem_capa' => 'capas/93mN9LsVsfPFr9t77YSYT5HAAlin470D6g3AL6YX.jpg',
                    'preco' => 13.65,
                    'created_at' => '2026-03-17 10:40:42',
                    'updated_at' => '2026-03-17 10:40:42',
                ],
                [
                    'id' => 3,
                    'isbn' => '9781471156267',
                    'nome' => 'It Ends with Us',
                    'editora_id' => 5,
                    'bibliografia' => '"It Ends with Us", é um romance de 2016 escrito por Colleen Hoover. A obra aborda a violência doméstica e relacionamentos tóxicos, focando na personagem Lily Bloom. O livro gerou uma sequela, It Starts with Us (2022), e uma adaptação cinematográfica lançada em 2024.',
                    'imagem_capa' => 'capas/OimUMzHIkp4OnF11CWFLE2VPLTue4jqevskLiTCQ.jpg',
                    'preco' => 17.67,
                    'created_at' => '2026-03-17 10:48:39',
                    'updated_at' => '2026-03-17 10:48:39',
                ],
                [
                    'id' => 4,
                    'isbn' => '9780241610350',
                    'nome' => 'One of Us Is Lying',
                    'editora_id' => 4,
                    'bibliografia' => '"One of Us Is Lying", é o romance de estreia da autora norte-americana Karen M. McManus, uma renomada escritora de thrillers Young Adult (jovens adultos). A obra tornou-se um best-seller internacional, com a autora escrevendo também as sequências "Um de Nós é o Próximo" e "Um de Nós Está de Volta".',
                    'imagem_capa' => 'capas/P1bU04xt9eZPIWM0OiwSxoQdruCSZWEWZt1aEGve.jpg',
                    'preco' => 16.88,
                    'created_at' => '2026-03-17 10:59:49',
                    'updated_at' => '2026-03-17 11:00:03',
                ],
                [
                    'id' => 5,
                    'isbn' => '9789722545471',
                    'nome' => 'Icebreaker',
                    'editora_id' => 6,
                    'bibliografia' => '"Icebreaker", de Hannah Grace, é um romance contemporâneo de 2022, publicado em Portugal pela Bertrand Editora. A obra narra a convivência forçada entre Anastasia Allen, uma patinadora artística focada nos seus objetivos, e Nate Hawkins, o capitão da equipa de hóquei no gelo, que se veem obrigados a partilhar o mesmo rinque após um incidente, abordando temas de rivalidade, disciplina desportiva e romance universitário.',
                    'imagem_capa' => 'capas/lJe5wGO5nV5H5ZcFaBb3vgdi4bLCsn0VrIVEPWtH.jpg',
                    'preco' => 18.80,
                    'created_at' => '2026-03-17 11:10:52',
                    'updated_at' => '2026-03-17 11:11:48',
                ],
                [
                    'id' => 6,
                    'isbn' => '9789895701124',
                    'nome' => 'The Housemaid',
                    'editora_id' => 7,
                    'bibliografia' => '"The Housemaid", de Freida McFadden, é um thriller psicológico de 2022, publicado em Portugal pela Alma dos Livros em 2023. A obra acompanha Millie, uma mulher com um passado conturbado que aceita trabalhar como empregada doméstica para a abastada família Winchester; no entanto, rapidamente descobre que a dinâmica da casa esconde segredos perigosos e que o seu quarto apenas tranca pelo lado de fora, abordando temas de manipulação, suspense e reviravoltas inesperadas.',
                    'imagem_capa' => 'capas/NdXKqbgS9UkiX35aHosWzVuYOGdVbzafevdPk4KT.webp',
                    'preco' => 19.45,
                    'created_at' => '2026-03-17 11:14:48',
                    'updated_at' => '2026-03-17 11:14:59',
                ],
                [
                    'id' => 7,
                    'isbn' => '9789897843051',
                    'nome' => 'All the Bright Places',
                    'editora_id' => 8,
                    'bibliografia' => '"All the Bright Places", de Jennifer Niven, é um romance contemporâneo Young Adult de 2015, publicado em Portugal pela Nuvem de Tinta. A obra narra o encontro inesperado entre Violet Markey e Theodore Finch no cimo da torre sineira da escola, levando-os a percorrer o Indiana num projeto escolar enquanto lidam com o luto, a doença mental e a descoberta da beleza nos momentos efémeros, abordando temas de perda, sobrevivência e esperança.',
                    'imagem_capa' => 'capas/EZkVhkngl1fkN53GKOKN13rpUAvCPW1iXyOHtWVq.jpg',
                    'preco' => 17.45,
                    'created_at' => '2026-03-17 11:17:40',
                    'updated_at' => '2026-03-17 11:17:40',
                ],
                [
                    'id' => 8,
                    'isbn' => '9789722372794',
                    'nome' => 'The Do-Over',
                    'editora_id' => 3,
                    'bibliografia' => '"The Do-Over", de Lynn Painter, é um romance contemporâneo Young Adult de 2022, publicado em Portugal pela Editorial Presença. A obra narra o pior Dia dos Namorados da vida de Emilie Hornby, que termina num acidente de carro apenas para ela acordar e perceber que está presa num ciclo temporal, sendo obrigada a reviver o mesmo dia desastroso repetidamente ao lado do seu irritante parceiro de laboratório, Nick, enquanto explora temas de segundas oportunidades, autenticidade e amor inesperado.',
                    'imagem_capa' => 'capas/icrBNMTodZkuKns6p6rRDTDi5l8AQ6eT3RK3EGqI.jpg',
                    'preco' => 17.90,
                    'created_at' => '2026-03-17 11:19:56',
                    'updated_at' => '2026-03-17 11:19:56',
                ],
                [
                    'id' => 9,
                    'isbn' => '9789722363570',
                    'nome' => 'Five Feet Apart',
                    'editora_id' => 3,
                    'bibliografia' => '"Five Feet Apart", de Rachael Lippincott com Mikki Daughtry e Tobias Iaconis, é um romance contemporâneo Young Adult de 2018, publicado em Portugal pela Editorial Presença. A obra narra a história de Stella Grant e Will Newman, dois adolescentes com fibrose quística que se apaixonam no hospital, mas que estão estritamente proibidos de se aproximarem a menos de seis pés (cerca de dois metros) para evitarem infeções cruzadas letais, abordando temas de sacrifício, a fragilidade da vida e a força do toque humano.',
                    'imagem_capa' => 'capas/jYllQTZ3tXXmTCqrU23nATUvthwpZEvkm3y5QZiF.jpg',
                    'preco' => 16.90,
                    'created_at' => '2026-03-17 11:22:15',
                    'updated_at' => '2026-03-17 11:22:15',
                ],
                [
                    'id' => 10,
                    'isbn' => '9789892357423',
                    'nome' => 'Archer\'s Voice',
                    'editora_id' => 9,
                    'bibliografia' => '"Archer\'s Voice", de Mia Sheridan, é um romance contemporâneo de 2014, publicado em Portugal pelas Edições Asa em 2023. A obra narra a história de Bree Prescott, que se muda para uma pequena cidade no Maine para fugir de um passado traumático, e Archer Hale, um homem isolado e mudo que carrega as cicatrizes de uma tragédia familiar, explorando temas de superação, comunicação não-verbal e o poder curativo do amor.',
                    'imagem_capa' => 'capas/pzmT3cW0RucZDoBsUoxL7luPdbGxbseZWo84WeTn.jpg',
                    'preco' => 18.90,
                    'created_at' => '2026-03-17 11:24:08',
                    'updated_at' => '2026-03-17 11:25:10',
                ],
            ],
            ['id'],
            ['isbn', 'nome', 'editora_id', 'bibliografia', 'imagem_capa', 'preco', 'created_at', 'updated_at']
        );
    }
}