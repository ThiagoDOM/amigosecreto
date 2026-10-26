<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Participant;
use App\Models\ParticipantExclusion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic portfolio showcase data.
     */
    public function run(): void
    {
        // 1. Showcase User (Organizador Showcase)
        $user = User::firstOrCreate(
            ['email' => 'admin@giftmatch.online'],
            [
                'name' => 'Organizador Showcase',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Showcase Event 1: Amigo Secreto da Família (Sorteado com Restrições de Casais)
        $familyEvent = Event::updateOrCreate(
            ['code' => 'FAMILIA24'],
            [
                'user_id' => $user->id,
                'title' => 'Amigo Secreto da Família 🎄',
                'description' => 'Confraternização de final de ano da família com ceia e troca de presentes!',
                'budget_limit' => 150.00,
                'draw_date' => '2026-12-20',
                'event_date' => '2026-12-24',
                'location' => 'Casa dos Pais',
                'status' => 'drawn',
                'drawn_at' => now(),
            ]
        );

        // Clear existing participants/exclusions for this event to keep it idempotent
        $familyEvent->exclusions()->delete();
        $familyEvent->participants()->delete();

        // Create Participants
        $lucas = Participant::create([
            'event_id' => $familyEvent->id,
            'name' => 'Lucas Silva',
            'phone' => '(11) 98765-4321',
            'email' => 'lucas.silva@exemplo.com',
            'token' => 'token_lucas_unrevealed_fam24',
            'clothing_size' => 'M',
            'shoe_size' => '41',
            'likes' => 'Café especial, livros de finanças e camisas casuais',
            'dislikes' => 'Cheiros muito doces, lactose',
            'wishlist' => json_encode([
                [
                    'title' => 'Livro Hábitos Atômicos',
                    'notes' => 'Edição capa comum ou especial',
                    'url' => 'https://www.amazon.com.br/dp/8550807567',
                ],
                [
                    'title' => 'Caneca Térmica Inox 500ml',
                    'notes' => 'Cor preta ou inox fosco',
                    'url' => 'https://shopee.com.br/search?keyword=caneca+termica+inox',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $mariana = Participant::create([
            'event_id' => $familyEvent->id,
            'name' => 'Mariana Costa',
            'phone' => '(11) 98765-4322',
            'email' => 'mariana.costa@exemplo.com',
            'token' => 'token_mariana_revealed_fam24',
            'clothing_size' => 'P',
            'shoe_size' => '36',
            'likes' => 'Decoração aconchegante, plantas e velas aromáticas',
            'dislikes' => 'Cores muito escuras',
            'revealed_at' => now(),
            'wishlist' => json_encode([
                [
                    'title' => 'Vela Aromática Artesanal Lavanda',
                    'notes' => 'Pote de vidro',
                    'url' => 'https://shopee.com.br/search?keyword=vela+aromatica+lavanda',
                ],
                [
                    'title' => 'Vaso de Cerâmica Minimalista',
                    'notes' => 'Tamanho médio para suculentas',
                    'url' => 'https://lista.mercadolivre.com.br/vaso-ceramica-minimalista',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $carlos = Participant::create([
            'event_id' => $familyEvent->id,
            'name' => 'Carlos Eduardo',
            'phone' => '(11) 98765-4323',
            'email' => 'carlos.eduardo@exemplo.com',
            'token' => 'token_carlos_secret_fam24',
            'clothing_size' => 'G',
            'shoe_size' => '42',
            'likes' => 'Tecnologia, periféricos e jogos eletrônicos',
            'dislikes' => 'Roupas formais',
            'revealed_at' => now(),
            'wishlist' => json_encode([
                [
                    'title' => 'Fone de Ouvido Bluetooth JBL',
                    'notes' => 'Modelo intra-auricular com estojo de recarga',
                    'url' => 'https://lista.mercadolivre.com.br/fone-jbl-bluetooth',
                ],
                [
                    'title' => 'Suporte Articulado para Notebook',
                    'notes' => 'Material alumínio',
                    'url' => 'https://shopee.com.br/search?keyword=suporte+articulado+notebook',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $beatriz = Participant::create([
            'event_id' => $familyEvent->id,
            'name' => 'Beatriz Santos',
            'phone' => '(11) 98765-4324',
            'email' => 'beatriz.santos@exemplo.com',
            'token' => 'token_beatriz_secret_fam24',
            'clothing_size' => 'M',
            'shoe_size' => '37',
            'likes' => 'Gastronomia, vinhos e culinária italiana',
            'dislikes' => 'Bebidas muito amargas',
            'wishlist' => json_encode([
                [
                    'title' => 'Livro de Receitas Italianas',
                    'notes' => 'Com receitas tradicionais de massas',
                    'url' => '',
                ],
                [
                    'title' => 'Kit de Azeites e Pimentas Artesanais',
                    'notes' => 'Temperos finos',
                    'url' => '',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $rafael = Participant::create([
            'event_id' => $familyEvent->id,
            'name' => 'Rafael Oliveira',
            'phone' => '(11) 98765-4325',
            'email' => 'rafael.oliveira@exemplo.com',
            'token' => 'token_rafael_secret_fam24',
            'clothing_size' => 'GG',
            'shoe_size' => '43',
            'likes' => 'Corrida de rua, academia e esportes ao ar livre',
            'dislikes' => 'Coisas frágeis de vidro',
            'wishlist' => json_encode([
                [
                    'title' => 'Camiseta Dry Fit para Corrida',
                    'notes' => 'Tamanho GG, cor preta ou azul marinho',
                    'url' => 'https://lista.mercadolivre.com.br/camiseta-dry-fit-corrida',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $juliana = Participant::create([
            'event_id' => $familyEvent->id,
            'name' => 'Juliana Mendes',
            'phone' => '(11) 98765-4326',
            'email' => 'juliana.mendes@exemplo.com',
            'token' => 'token_juliana_secret_fam24',
            'clothing_size' => 'P',
            'shoe_size' => '35',
            'likes' => 'Skincare, autocuidado e maquiagem',
            'dislikes' => 'Produtos testados em animais',
            'wishlist' => json_encode([
                [
                    'title' => 'Sérum Facial Vitamina C',
                    'notes' => 'Para uso diurno',
                    'url' => 'https://shopee.com.br/search?keyword=serum+vitamina+c',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        // Configure Couple Restrictions (Lucas <-> Mariana)
        ParticipantExclusion::create([
            'event_id' => $familyEvent->id,
            'participant_id' => $lucas->id,
            'excluded_participant_id' => $mariana->id,
        ]);
        ParticipantExclusion::create([
            'event_id' => $familyEvent->id,
            'participant_id' => $mariana->id,
            'excluded_participant_id' => $lucas->id,
        ]);

        // Assign Secret Friends (Derangement respecting couple exclusion)
        // Lucas -> Carlos
        $lucas->update(['drawn_participant_id' => $carlos->id]);
        // Mariana -> Beatriz
        $mariana->update(['drawn_participant_id' => $beatriz->id]);
        // Carlos -> Juliana
        $carlos->update(['drawn_participant_id' => $juliana->id]);
        // Beatriz -> Rafael
        $beatriz->update(['drawn_participant_id' => $rafael->id]);
        // Rafael -> Lucas
        $rafael->update(['drawn_participant_id' => $lucas->id]);
        // Juliana -> Mariana
        $juliana->update(['drawn_participant_id' => $mariana->id]);

        // 3. Showcase Event 2: Amigo Secreto da Firma (Em Aberto / Inscrições)
        $companyEvent = Event::updateOrCreate(
            ['code' => 'FIRMA2026'],
            [
                'user_id' => $user->id,
                'title' => 'Amigo Secreto da Firma 2026 🏢',
                'description' => 'Confraternização de fim de ano da empresa com chopp e happy hour no rooftop!',
                'budget_limit' => 100.00,
                'draw_date' => '2026-12-18',
                'event_date' => '2026-12-22',
                'location' => 'Rooftop do Escritório - Av. Paulista',
                'status' => 'open',
            ]
        );

        $companyEvent->participants()->delete();
        Participant::create([
            'event_id' => $companyEvent->id,
            'name' => 'Eduardo Ramos (Organizador)',
            'phone' => '(11) 99999-8888',
            'token' => 'token_organizador_company_1',
            'clothing_size' => 'M',
            'shoe_size' => '41',
            'likes' => 'Café gourmet, gadgets e livros técnicos',
        ]);
        Participant::create([
            'event_id' => $companyEvent->id,
            'name' => 'Fernanda Lima',
            'phone' => '(11) 98888-7777',
            'token' => 'token_fernanda_company_2',
            'clothing_size' => 'M',
            'shoe_size' => '38',
            'likes' => 'Chocolates artesanais e papelaria fofa',
        ]);
        Participant::create([
            'event_id' => $companyEvent->id,
            'name' => 'Rodrigo Souza',
            'phone' => '(11) 97777-6666',
            'token' => 'token_rodrigo_company_3',
            'clothing_size' => 'G',
            'shoe_size' => '42',
            'likes' => 'Boardgames e canecas geek',
        ]);
    }
}
