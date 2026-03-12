<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LivrosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('livros')->upsert(
            array (
  0 => 
  array (
    'id' => 1,
    'isbn' => '9786500000005',
    'nome' => 'As Intermitencias da Morte',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Saramago.',
    'imagem_capa' => 'https://img.bertrand.pt/images/as-intermitencias-da-morte-jose-saramago/NDV8MTU3MjM4ODR8Mjc4MDkxNTR8MTc3MTIzNDYxNzAwMA==/500x',
    'preco' => 37.62,
    'created_at' => '2026-03-11 11:18:12',
    'updated_at' => '2026-03-11 11:18:12',
  ),
  1 => 
  array (
    'id' => 2,
    'isbn' => '9786500000032',
    'nome' => 'Amor de Perdição',
    'editora_id' => 4,
    'bibliografia' => 'Obra de Camilo Castelo Branco.',
    'imagem_capa' => 'https://img.bertrand.pt/images/amor-de-perdicao-camilo-castelo-branco/NDV8MTY2NjY1ODR8MTIyNzk1NTJ8MTc3Mjc5MjExOTAwMA==/500x',
    'preco' => 36.39,
    'created_at' => '2026-03-11 11:18:47',
    'updated_at' => '2026-03-11 11:18:47',
  ),
  2 => 
  array (
    'id' => 3,
    'isbn' => '9786500000036',
    'nome' => 'Fanny Owen',
    'editora_id' => 6,
    'bibliografia' => 'Obra de Agustina Bessa-Luis.',
    'imagem_capa' => 'https://www.relogiodagua.pt/wp-content/uploads/2023/11/Screen-Shot-2017-09-21-at-3.25.08-PM.png',
    'preco' => 20.78,
    'created_at' => '2026-03-11 11:18:52',
    'updated_at' => '2026-03-11 11:18:52',
  ),
  3 => 
  array (
    'id' => 4,
    'isbn' => '9786500000010',
    'nome' => 'Poemas de Álvaro de Campos',
    'editora_id' => 2,
    'bibliografia' => 'Obra de Fernando Pessoa.',
    'imagem_capa' => 'https://http2.mlstatic.com/D_NQ_NP_878289-MLA97261116288_112025-O.webp',
    'preco' => 26.66,
    'created_at' => '2026-03-11 11:18:19',
    'updated_at' => '2026-03-11 11:18:19',
  ),
  4 => 
  array (
    'id' => 5,
    'isbn' => '9786500000028',
    'nome' => 'Capitães da Areia',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Jorge Amado.',
    'imagem_capa' => 'https://img.wook.pt/images/capitaes-da-areia-jorge-amado/MXwxNDU3ODY0fDEzODE3NjZ8MTc3MjcwNTU2NjAwMA==/500x',
    'preco' => 25.23,
    'created_at' => '2026-03-11 11:18:42',
    'updated_at' => '2026-03-11 11:18:42',
  ),
  5 => 
  array (
    'id' => 6,
    'isbn' => '9786500000004',
    'nome' => 'A Jangada de Pedra',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Saramago.',
    'imagem_capa' => 'https://img.portoeditora.pt/images/a-jangada-de-pedra-jose-saramago/NHwxNjM5Mzk0MHwyOTIyMDYyMXwxNzcxMjM5NDgzMDAwfHdlYnA=/300x',
    'preco' => 10.26,
    'created_at' => '2026-03-11 11:18:11',
    'updated_at' => '2026-03-11 11:18:11',
  ),
  6 => 
  array (
    'id' => 7,
    'isbn' => '9786500000002',
    'nome' => 'Memorial do Convento',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Saramago.',
    'imagem_capa' => 'https://img.wook.pt/images/memorial-do-convento-jose-saramago/MXwxNTc0NzQ0NHwyODkwNDAzMnwxNzcxMjM0NzYwMDAw/500x',
    'preco' => 15.71,
    'created_at' => '2026-03-11 11:18:08',
    'updated_at' => '2026-03-11 11:18:08',
  ),
  7 => 
  array (
    'id' => 8,
    'isbn' => '9786500000008',
    'nome' => 'Mensagem',
    'editora_id' => 2,
    'bibliografia' => 'Obra de Fernando Pessoa.',
    'imagem_capa' => 'https://books.google.com/books/content?id=p5TgU69L4roC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 16.42,
    'created_at' => '2026-03-11 11:18:16',
    'updated_at' => '2026-03-11 11:18:16',
  ),
  8 => 
  array (
    'id' => 9,
    'isbn' => '9786500000037',
    'nome' => 'A Ronda da Noite',
    'editora_id' => 6,
    'bibliografia' => 'Obra de Agustina Bessa-Luis.',
    'imagem_capa' => 'https://www.relogiodagua.pt/wp-content/uploads/2023/11/Screen-Shot-2018-01-29-at-4.06.35-PM.png',
    'preco' => 23.08,
    'created_at' => '2026-03-11 11:18:53',
    'updated_at' => '2026-03-11 11:18:53',
  ),
  9 => 
  array (
    'id' => 10,
    'isbn' => '9786500000029',
    'nome' => 'Gabriela, Cravo e Canela',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Jorge Amado.',
    'imagem_capa' => 'https://www.worten.pt/i/1156105498_zoom',
    'preco' => 38.83,
    'created_at' => '2026-03-11 11:18:43',
    'updated_at' => '2026-03-11 11:18:43',
  ),
  10 => 
  array (
    'id' => 11,
    'isbn' => '9786500000048',
    'nome' => 'Harry Potter and the Philosophers Stone',
    'editora_id' => 11,
    'bibliografia' => 'Obra de J. K. Rowling.',
    'imagem_capa' => 'https://res.cloudinary.com/bloomsbury-atlas/image/upload/w_360,c_scale,dpr_1.5/jackets/9781408855652.jpg',
    'preco' => 24.18,
    'created_at' => '2026-03-11 11:19:08',
    'updated_at' => '2026-03-11 11:19:08',
  ),
  11 => 
  array (
    'id' => 12,
    'isbn' => '9786500000018',
    'nome' => 'Jesusalém',
    'editora_id' => 7,
    'bibliografia' => 'Obra de Mia Couto.',
    'imagem_capa' => 'https://www.continente.pt/dw/image/v2/BDVS_PRD/on/demandware.static/-/Sites-col-master-catalog/default/dwe176e695/images/col/500/5007003-topshot.jpg?sw=2000&sh=2000',
    'preco' => 35.51,
    'created_at' => '2026-03-11 11:18:29',
    'updated_at' => '2026-03-11 11:18:29',
  ),
  12 => 
  array (
    'id' => 13,
    'isbn' => '9786500000025',
    'nome' => 'Memórias Postumas de Brás Cubas',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Machado de Assis.',
    'imagem_capa' => 'https://books.google.com/books/content?id=xUNrAAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api',
    'preco' => 27.32,
    'created_at' => '2026-03-11 11:18:38',
    'updated_at' => '2026-03-11 11:18:38',
  ),
  13 => 
  array (
    'id' => 14,
    'isbn' => '9786500000035',
    'nome' => 'A Sibila',
    'editora_id' => 6,
    'bibliografia' => 'Obra de Agustina Bessa-Luis.',
    'imagem_capa' => 'https://www.relogiodagua.pt/wp-content/uploads/2023/11/Screen-Shot-2017-07-06-at-5.14.52-PM.png',
    'preco' => 37.54,
    'created_at' => '2026-03-11 11:18:51',
    'updated_at' => '2026-03-11 11:18:51',
  ),
  14 => 
  array (
    'id' => 15,
    'isbn' => '9786500000027',
    'nome' => 'Esaue Jaco',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Machado de Assis.',
    'imagem_capa' => 'https://img.travessa.pt/livro/GR/b9/b9777efc-1aea-49d5-9d06-35cf1962d3ca.jpg',
    'preco' => 36.7,
    'created_at' => '2026-03-11 11:18:40',
    'updated_at' => '2026-03-11 11:18:40',
  ),
  15 => 
  array (
    'id' => 16,
    'isbn' => '9786500000026',
    'nome' => 'Quincas Borba',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Machado de Assis.',
    'imagem_capa' => 'https://cdn.kobo.com/book-images/a55cfbe6-f9c3-454a-9a46-02f93bda078c/353/569/90/False/quincas-borba-39.jpg',
    'preco' => 32.66,
    'created_at' => '2026-03-11 11:18:39',
    'updated_at' => '2026-03-11 11:18:39',
  ),
  16 => 
  array (
    'id' => 17,
    'isbn' => '9786500000006',
    'nome' => 'O Evangelho Segundo Jesus Cristo',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Saramago.',
    'imagem_capa' => 'https://img.wook.pt/images/o-evangelho-segundo-jesus-cristo-jose-saramago/MXwxNTgyNTQ4NXwyNTk0NTYwNnwxNzcxMjM0NjUxMDAw/500x',
    'preco' => 32.23,
    'created_at' => '2026-03-11 11:18:13',
    'updated_at' => '2026-03-11 11:18:13',
  ),
  17 => 
  array (
    'id' => 18,
    'isbn' => '9786500000044',
    'nome' => 'A Boneca de Kokoschka',
    'editora_id' => 8,
    'bibliografia' => 'Obra de Afonso Cruz.',
    'imagem_capa' => 'https://books.google.com/books/content?id=NdIiEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 27.82,
    'created_at' => '2026-03-11 11:19:02',
    'updated_at' => '2026-03-11 11:19:02',
  ),
  18 => 
  array (
    'id' => 19,
    'isbn' => '9786500000042',
    'nome' => 'A Fórmula de Deus',
    'editora_id' => 4,
    'bibliografia' => 'Obra de Jose Rodrigues dos Santos.',
    'imagem_capa' => 'https://img.bertrand.pt/images/a-formula-de-deus-jose-rodrigues-dos-santos/NDV8MTg2NDQ1fDIzNjEyMXwxNjk5NTI2MzM2MDAw/500x',
    'preco' => 38.75,
    'created_at' => '2026-03-11 11:19:00',
    'updated_at' => '2026-03-11 11:19:00',
  ),
  19 => 
  array (
    'id' => 20,
    'isbn' => '9786500000017',
    'nome' => 'O Outro Pé da Sereia',
    'editora_id' => 7,
    'bibliografia' => 'Obra de Mia Couto.',
    'imagem_capa' => 'https://books.google.com/books/content?id=qU0sx2ysu70C&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 16.15,
    'created_at' => '2026-03-11 11:18:28',
    'updated_at' => '2026-03-11 11:18:28',
  ),
  20 => 
  array (
    'id' => 21,
    'isbn' => '9786500000039',
    'nome' => 'A Máquina de Fazer Espanhóis',
    'editora_id' => 5,
    'bibliografia' => 'Obra de Valter Hugo Mae.',
    'imagem_capa' => 'https://leituria.com/Files/Images/2caf1cf9-0ffb-4c33-9845-a563d59618aa.jpeg',
    'preco' => 12.7,
    'created_at' => '2026-03-11 11:18:56',
    'updated_at' => '2026-03-11 11:18:56',
  ),
  21 => 
  array (
    'id' => 22,
    'isbn' => '9786500000034',
    'nome' => 'A Brasileira de Prazins',
    'editora_id' => 4,
    'bibliografia' => 'Obra de Camilo Castelo Branco.',
    'imagem_capa' => 'https://img.bertrand.pt/images/a-brasileira-de-prazins-camilo-castelo-branco/NDV8MTc1NzIzfDIyMzgyMHwxMzgzNTc3MDQ0MDAw/500x',
    'preco' => 37.09,
    'created_at' => '2026-03-11 11:18:49',
    'updated_at' => '2026-03-11 11:18:49',
  ),
  22 => 
  array (
    'id' => 23,
    'isbn' => '9786500000012',
    'nome' => 'Os Maias',
    'editora_id' => 3,
    'bibliografia' => 'Obra de Eca de Queiros.',
    'imagem_capa' => 'https://upload.wikimedia.org/wikipedia/commons/9/9b/Os_Maias_Book_Cover.jpg',
    'preco' => 38.09,
    'created_at' => '2026-03-11 11:18:21',
    'updated_at' => '2026-03-11 11:18:21',
  ),
  23 => 
  array (
    'id' => 24,
    'isbn' => '9786500000024',
    'nome' => 'Dom Casmurro',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Machado de Assis.',
    'imagem_capa' => 'https://books.google.com/books/content?id=6Ne7EQAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 30.31,
    'created_at' => '2026-03-11 11:18:37',
    'updated_at' => '2026-03-11 11:18:37',
  ),
  24 => 
  array (
    'id' => 25,
    'isbn' => '9786500000023',
    'nome' => 'Laço de Família',
    'editora_id' => 8,
    'bibliografia' => 'Obra de Clarice Lispector.',
    'imagem_capa' => 'https://penguinlivros.pt/wp-content/uploads/2026/03/lacos-de-familia-CL91344-600x939.jpg',
    'preco' => 16.11,
    'created_at' => '2026-03-11 11:18:35',
    'updated_at' => '2026-03-11 11:18:35',
  ),
  25 => 
  array (
    'id' => 26,
    'isbn' => '9786500000038',
    'nome' => 'O Remorso de Baltazar Serapião',
    'editora_id' => 5,
    'bibliografia' => 'Obra de Valter Hugo Mae.',
    'imagem_capa' => 'https://static.fnac-static.com/multimedia/PT/images_produits/PT/ZoomPE/3/0/8/9789896720803/tsp20130910200525/O-Remorso-de-Baltazar-Serapiao.jpg',
    'preco' => 32.6,
    'created_at' => '2026-03-11 11:18:55',
    'updated_at' => '2026-03-11 11:18:55',
  ),
  26 => 
  array (
    'id' => 27,
    'isbn' => '9786500000015',
    'nome' => 'A Cidade e as Serras',
    'editora_id' => 3,
    'bibliografia' => 'Obra de Eca de Queiros.',
    'imagem_capa' => 'https://m.media-amazon.com/images/I/61ZA28+OiQL._SY466_.jpg',
    'preco' => 29.25,
    'created_at' => '2026-03-11 11:18:25',
    'updated_at' => '2026-03-11 11:18:25',
  ),
  27 => 
  array (
    'id' => 28,
    'isbn' => '9786500000007',
    'nome' => 'Livro do Desassossego',
    'editora_id' => 2,
    'bibliografia' => 'Obra de Fernando Pessoa.',
    'imagem_capa' => 'https://books.google.com/books/content?id=stlxDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 23.46,
    'created_at' => '2026-03-11 11:18:15',
    'updated_at' => '2026-03-11 11:18:15',
  ),
  28 => 
  array (
    'id' => 29,
    'isbn' => '9786500000021',
    'nome' => 'Perto do Coração Selvagem',
    'editora_id' => 8,
    'bibliografia' => 'Obra de Clarice Lispector.',
    'imagem_capa' => 'https://books.google.com/books/content?id=2lE3EQAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 12.65,
    'created_at' => '2026-03-11 11:18:33',
    'updated_at' => '2026-03-11 11:18:33',
  ),
  29 => 
  array (
    'id' => 30,
    'isbn' => '9786500000047',
    'nome' => 'O Cavaleiro da Dinamarca',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Sophia de Mello Breyner.',
    'imagem_capa' => 'https://img.portoeditora.pt/images/o-cavaleiro-da-dinamarca-sophia-de-mello-breyner-andresen/NHwxNTI3MDkwNHwyODg2OTIxM3wxNzcxMjM1NzM0MDAwfHdlYnA=/300x',
    'preco' => 24.41,
    'created_at' => '2026-03-11 11:19:06',
    'updated_at' => '2026-03-11 11:19:06',
  ),
  30 => 
  array (
    'id' => 31,
    'isbn' => '9786500000046',
    'nome' => 'A Menina do Mar',
    'editora_id' => 2,
    'bibliografia' => 'Obra de Sophia de Mello Breyner.',
    'imagem_capa' => 'https://static.fnac-static.com/multimedia/Images/PT/NR/0e/ca/55/5622286/1540-1.jpg',
    'preco' => 12.99,
    'created_at' => '2026-03-11 11:19:05',
    'updated_at' => '2026-03-11 11:19:05',
  ),
  31 => 
  array (
    'id' => 32,
    'isbn' => '9786500000003',
    'nome' => 'O Homem Duplicado',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Saramago.',
    'imagem_capa' => 'https://img.portoeditora.pt/images/o-homem-duplicado-jose-saramago/NHwxNTQ5Nzk1NnwyODY2NzAxNnwxNzcxMjQwMDM3MDAwfHdlYnA=/300x',
    'preco' => 18.33,
    'created_at' => '2026-03-11 11:18:10',
    'updated_at' => '2026-03-11 11:18:10',
  ),
  32 => 
  array (
    'id' => 33,
    'isbn' => '9786500000001',
    'nome' => 'Ensaio sobre a Cegueira',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Saramago.',
    'imagem_capa' => 'https://img.portoeditora.pt/images/ensaio-sobre-a-cegueira-jose-saramago/NHwxNTgyNTQ4NnwyOTI3NDk3M3wxNzcwOTkwMDU2MDAwfHdlYnA=/300x',
    'preco' => 19.3,
    'created_at' => '2026-03-11 11:18:07',
    'updated_at' => '2026-03-11 11:18:07',
  ),
  33 => 
  array (
    'id' => 34,
    'isbn' => '9786500000022',
    'nome' => 'A Paixão Segundo G.H.',
    'editora_id' => 8,
    'bibliografia' => 'Obra de Clarice Lispector.',
    'imagem_capa' => 'https://books.google.com/books/content?id=1lE3EQAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 32.98,
    'created_at' => '2026-03-11 11:18:34',
    'updated_at' => '2026-03-11 11:18:34',
  ),
  34 => 
  array (
    'id' => 35,
    'isbn' => '9786500000045',
    'nome' => 'Jesus Cristo Bebia Cerveja',
    'editora_id' => 8,
    'bibliografia' => 'Obra de Afonso Cruz.',
    'imagem_capa' => 'https://books.google.com/books/content?id=INjVEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 26.24,
    'created_at' => '2026-03-11 11:19:04',
    'updated_at' => '2026-03-11 11:19:04',
  ),
  35 => 
  array (
    'id' => 36,
    'isbn' => '9786500000050',
    'nome' => '1984',
    'editora_id' => 9,
    'bibliografia' => 'Obra de George Orwell.',
    'imagem_capa' => 'https://cdn.penguin.co.uk/dam-assets/books/9780141036144/9780141036144-jacket-large.jpg',
    'preco' => 38.84,
    'created_at' => '2026-03-11 11:19:10',
    'updated_at' => '2026-03-11 11:19:10',
  ),
  36 => 
  array (
    'id' => 37,
    'isbn' => '9786500000030',
    'nome' => 'Dona Flor e Seus Dois Maridos',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Jorge Amado.',
    'imagem_capa' => 'https://www.continente.pt/dw/image/v2/BDVS_PRD/on/demandware.static/-/Sites-col-master-catalog/default/dw8680a8f5/images/col/652/6524736-topshot.jpg?sw=2000&sh=2000',
    'preco' => 25.82,
    'created_at' => '2026-03-11 11:18:44',
    'updated_at' => '2026-03-11 11:18:44',
  ),
  37 => 
  array (
    'id' => 38,
    'isbn' => '9786500000013',
    'nome' => 'O Crime do Padre Amaro',
    'editora_id' => 3,
    'bibliografia' => 'Obra de Eca de Queiros.',
    'imagem_capa' => 'https://www.presenca.pt/cdn/shop/products/image-1_efbd3ffc-a364-443b-a4ee-6d12a33b01ff_grande.jpg?v=1604970762',
    'preco' => 34.37,
    'created_at' => '2026-03-11 11:18:23',
    'updated_at' => '2026-03-11 11:18:23',
  ),
  38 => 
  array (
    'id' => 39,
    'isbn' => '9786500000043',
    'nome' => 'O Sinal de Vida',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Jose Rodrigues dos Santos.',
    'imagem_capa' => 'https://static.fnac-static.com/multimedia/Images/PT/NR/46/5d/86/8805702/1540-1.jpg',
    'preco' => 31.09,
    'created_at' => '2026-03-11 11:19:01',
    'updated_at' => '2026-03-11 11:19:01',
  ),
  39 => 
  array (
    'id' => 40,
    'isbn' => '9786500000020',
    'nome' => 'A Hora da Estrela',
    'editora_id' => 8,
    'bibliografia' => 'Obra de Clarice Lispector.',
    'imagem_capa' => 'https://books.google.com/books/content?id=82UHEAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api',
    'preco' => 32.61,
    'created_at' => '2026-03-11 11:18:31',
    'updated_at' => '2026-03-11 11:18:31',
  ),
  40 => 
  array (
    'id' => 41,
    'isbn' => '9786500000019',
    'nome' => 'A Confissão da Leoa',
    'editora_id' => 7,
    'bibliografia' => 'Obra de Mia Couto.',
    'imagem_capa' => 'https://books.google.com/books/content?id=Ur1k4yOp6p4C&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api',
    'preco' => 20.35,
    'created_at' => '2026-03-11 11:18:30',
    'updated_at' => '2026-03-11 11:18:30',
  ),
  41 => 
  array (
    'id' => 42,
    'isbn' => '9786500000014',
    'nome' => 'A Relíquia',
    'editora_id' => 1,
    'bibliografia' => 'Obra de Eca de Queiros.',
    'imagem_capa' => 'https://img.wook.pt/images/a-reliquia-eca-de-queiros/MXwyMTQ1MTU1fDE0MjYyMDM2fDE3NzE4NDY1NDMwMDA=/500x',
    'preco' => 10.17,
    'created_at' => '2026-03-11 11:18:24',
    'updated_at' => '2026-03-11 11:18:24',
  ),
  42 => 
  array (
    'id' => 43,
    'isbn' => '9786500000011',
    'nome' => 'O Banqueiro Anarquista',
    'editora_id' => 2,
    'bibliografia' => 'Obra de Fernando Pessoa.',
    'imagem_capa' => 'https://penguinlivros.pt/wp-content/uploads/2026/02/o-banqueiro-anarquista-PG39018-scaled.jpg',
    'preco' => 38.44,
    'created_at' => '2026-03-11 11:18:20',
    'updated_at' => '2026-03-11 11:18:20',
  ),
  43 => 
  array (
    'id' => 44,
    'isbn' => '9786500000009',
    'nome' => 'Poemas de Alberto Caeiro',
    'editora_id' => 2,
    'bibliografia' => 'Obra de Fernando Pessoa.',
    'imagem_capa' => 'https://www.continente.pt/dw/image/v2/BDVS_PRD/on/demandware.static/-/Sites-col-master-catalog/default/dw7c93e32e/images/col/486/4865058-topshot.jpg?sw=2000&sh=2000',
    'preco' => 20.02,
    'created_at' => '2026-03-11 11:18:17',
    'updated_at' => '2026-03-11 11:18:17',
  ),
  44 => 
  array (
    'id' => 45,
    'isbn' => '9786500000033',
    'nome' => 'A Queda de um Anjo',
    'editora_id' => 4,
    'bibliografia' => 'Obra de Camilo Castelo Branco.',
    'imagem_capa' => 'https://img.bertrand.pt/images/a-queda-de-um-anjo-camilo-castelo-branco/NDV8MTU5Njg0ODJ8MTE0OTgzMTJ8MTQxMDg4NjQ2MDAwMA==/500x',
    'preco' => 25.83,
    'created_at' => '2026-03-11 11:18:48',
    'updated_at' => '2026-03-11 11:18:48',
  ),
  45 => 
  array (
    'id' => 46,
    'isbn' => '9786500000040',
    'nome' => 'O Filho de Mil Homens',
    'editora_id' => 5,
    'bibliografia' => 'Obra de Valter Hugo Mae.',
    'imagem_capa' => 'https://static.fnac-static.com/multimedia/PT/images_produits/PT/ZoomPE/7/7/0/9789896721077/tsp20130910200525/O-Filho-de-Mil-Homens.jpg',
    'preco' => 21.67,
    'created_at' => '2026-03-11 11:18:58',
    'updated_at' => '2026-03-11 11:18:58',
  ),
  46 => 
  array (
    'id' => 47,
    'isbn' => '9786500000041',
    'nome' => 'Codex 632',
    'editora_id' => 13,
    'bibliografia' => 'Obra de Jose Rodrigues dos Santos.',
    'imagem_capa' => 'https://static.fnac-static.com/multimedia/Images/PT/NR/2a/39/b4/11811114/1540-1.jpg',
    'preco' => 23.73,
    'created_at' => '2026-03-11 11:18:59',
    'updated_at' => '2026-03-11 11:18:59',
  ),
  47 => 
  array (
    'id' => 48,
    'isbn' => '9786500000031',
    'nome' => 'Tieta do Agreste',
    'editora_id' => 12,
    'bibliografia' => 'Obra de Jorge Amado.',
    'imagem_capa' => 'https://static.fnac-static.com/multimedia/PT/images_produits/PT/ZoomPE/1/4/9/9789722051941/tsp20130325200941/Tieta-do-Agreste.jpg',
    'preco' => 28.2,
    'created_at' => '2026-03-11 11:18:46',
    'updated_at' => '2026-03-11 11:18:46',
  ),
  48 => 
  array (
    'id' => 49,
    'isbn' => '9786500000016',
    'nome' => 'Terra Sonâmbula',
    'editora_id' => 7,
    'bibliografia' => 'Obra de Mia Couto.',
    'imagem_capa' => 'https://books.google.com/books/content?id=9kXUbz0QLg0C&printsec=frontcover&img=1&zoom=1&source=gbs_api',
    'preco' => 25.1,
    'created_at' => '2026-03-11 11:18:27',
    'updated_at' => '2026-03-11 11:18:27',
  ),
  49 => 
  array (
    'id' => 50,
    'isbn' => '9786500000049',
    'nome' => 'The Hobbit',
    'editora_id' => 10,
    'bibliografia' => 'Obra de J. R. R. Tolkien.',
    'imagem_capa' => 'https://harpercollins.co.uk/cdn/shop/files/x9780007322602_500x.jpg?v=1743593663',
    'preco' => 19.07,
    'created_at' => '2026-03-11 11:19:09',
    'updated_at' => '2026-03-11 11:19:09',
  ),
),
            ['id'],
            ['isbn', 'nome', 'editora_id', 'bibliografia', 'imagem_capa', 'preco', 'created_at', 'updated_at']
        );

        DB::table('autor_livro')->upsert(
            array (
  0 => 
  array (
    'autores_id' => 1,
    'livros_id' => 1,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  1 => 
  array (
    'autores_id' => 8,
    'livros_id' => 2,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  2 => 
  array (
    'autores_id' => 9,
    'livros_id' => 3,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  3 => 
  array (
    'autores_id' => 2,
    'livros_id' => 4,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  4 => 
  array (
    'autores_id' => 7,
    'livros_id' => 5,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  5 => 
  array (
    'autores_id' => 1,
    'livros_id' => 6,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  6 => 
  array (
    'autores_id' => 1,
    'livros_id' => 7,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  7 => 
  array (
    'autores_id' => 2,
    'livros_id' => 8,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  8 => 
  array (
    'autores_id' => 9,
    'livros_id' => 9,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  9 => 
  array (
    'autores_id' => 7,
    'livros_id' => 10,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  10 => 
  array (
    'autores_id' => 14,
    'livros_id' => 11,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  11 => 
  array (
    'autores_id' => 4,
    'livros_id' => 12,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  12 => 
  array (
    'autores_id' => 6,
    'livros_id' => 13,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  13 => 
  array (
    'autores_id' => 9,
    'livros_id' => 14,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  14 => 
  array (
    'autores_id' => 6,
    'livros_id' => 15,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  15 => 
  array (
    'autores_id' => 6,
    'livros_id' => 16,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  16 => 
  array (
    'autores_id' => 1,
    'livros_id' => 17,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  17 => 
  array (
    'autores_id' => 12,
    'livros_id' => 18,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  18 => 
  array (
    'autores_id' => 11,
    'livros_id' => 19,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  19 => 
  array (
    'autores_id' => 4,
    'livros_id' => 20,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  20 => 
  array (
    'autores_id' => 10,
    'livros_id' => 21,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  21 => 
  array (
    'autores_id' => 8,
    'livros_id' => 22,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  22 => 
  array (
    'autores_id' => 3,
    'livros_id' => 23,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  23 => 
  array (
    'autores_id' => 6,
    'livros_id' => 24,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  24 => 
  array (
    'autores_id' => 5,
    'livros_id' => 25,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  25 => 
  array (
    'autores_id' => 10,
    'livros_id' => 26,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  26 => 
  array (
    'autores_id' => 3,
    'livros_id' => 27,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  27 => 
  array (
    'autores_id' => 2,
    'livros_id' => 28,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  28 => 
  array (
    'autores_id' => 5,
    'livros_id' => 29,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  29 => 
  array (
    'autores_id' => 13,
    'livros_id' => 30,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  30 => 
  array (
    'autores_id' => 13,
    'livros_id' => 31,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  31 => 
  array (
    'autores_id' => 1,
    'livros_id' => 32,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  32 => 
  array (
    'autores_id' => 1,
    'livros_id' => 33,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  33 => 
  array (
    'autores_id' => 5,
    'livros_id' => 34,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  34 => 
  array (
    'autores_id' => 12,
    'livros_id' => 35,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  35 => 
  array (
    'autores_id' => 16,
    'livros_id' => 36,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  36 => 
  array (
    'autores_id' => 7,
    'livros_id' => 37,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  37 => 
  array (
    'autores_id' => 3,
    'livros_id' => 38,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  38 => 
  array (
    'autores_id' => 11,
    'livros_id' => 39,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  39 => 
  array (
    'autores_id' => 5,
    'livros_id' => 40,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  40 => 
  array (
    'autores_id' => 4,
    'livros_id' => 41,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  41 => 
  array (
    'autores_id' => 3,
    'livros_id' => 42,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  42 => 
  array (
    'autores_id' => 2,
    'livros_id' => 43,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  43 => 
  array (
    'autores_id' => 2,
    'livros_id' => 44,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  44 => 
  array (
    'autores_id' => 8,
    'livros_id' => 45,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  45 => 
  array (
    'autores_id' => 10,
    'livros_id' => 46,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  46 => 
  array (
    'autores_id' => 11,
    'livros_id' => 47,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  47 => 
  array (
    'autores_id' => 7,
    'livros_id' => 48,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  48 => 
  array (
    'autores_id' => 4,
    'livros_id' => 49,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  49 => 
  array (
    'autores_id' => 15,
    'livros_id' => 50,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
),
            ['id'],
            ['autores_id', 'livros_id', 'created_at', 'updated_at']
        );
    }
}
