<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estoque>
 */
class EstoqueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Barbearia' => [
                'Óleo Pré-Barba'=>['Proraso', 'Don Alcides', 'Barber Neez'],
                'Creme Esfoliante'=>['Neutrogena', 'Nivea', 'Granado', 'L\'Occitane au Brésil', 'Good Embaixador'],
                'Creme de Barbear'=>['Proraso', 'Nivea', 'Barber Neez', 'Gillette'],
                'Sabão de Barbear'=>['Barba Brava', 'Truefitt & Hill', 'Cella', 'Taylor of Old Bond Street'],
                'Loção Pós-Barba'=>['Nivea', 'Proraso', 'Barber Neez', 'L\'Oréal', 'QOD Barber Shop', 'Baboon', 'King C. Gillette'],
                'Shampoo para Barba'=>['Reuzel', 'Professor Fuzzworthy', 'Scotch Porter', 'Viking Revolution'],
                'Condicionador para Barba'=>['Scotch Porter', 'Viking Revolution', 'Professor Fuzzworthy'],
                'Balm para Barba'=>['Honest Amish', 'Scotch Porter', 'Viking Revolution', 'Professor Fuzzworthy'],
                'Cera para Bigode'=>['Honest Amish', 'Captain Fawcett', 'Clubman Pinaud'],
                'Pomada para Cabelo'=>['Suavecito', 'Layrite', 'Uppercut Deluxe', 'Reuzel'],
                'Tônico Capilar'=>['Vichy Dercos', 'Kérastase Specifique', 'L\'Oréal Professionnel'],
                'Coloração para Barba'=>['Just For Men', 'RefectoCil', 'Godefroy'],
                
            ],
            'Salão de Beleza' => [
                'Shampoo'=>['Pantene', 'Head & Shoulders', 'L\'Oréal', 'TRESemmé', 'Dove'],
                'Condicionador'=>['Pantene', 'L\'Oréal', 'TRESemmé', 'Dove', 'Aussie'],
                'Máscara de Hidratação'=>['Novex', 'Salon Line', 'L\'Oréal Professionnel', 'Kérastase'],
                'Leave-in'=>['Avon Advance Techniques', 'L\'Oréal Elseve', 'Salon Line'],
                'Óleo Capilar'=>['Moroccanoil', 'L\'Oréal Mythic Oil', 'Boticário Nativa SPA'],
                'Spray de Sal'=>['L\'Oréal Studio Line', 'TIGI Bed Head', 'Not Your Mother\'s Beach Babe'],
                'Protetor Térmico'=>['Tresemmé Thermal Creations', 'L\'Oréal Professionnel Serie Expert'],
                'Gel Modelador'=>['Bio Extratus', 'Salon Line', 'Skala'],
                'Mousse Modelador'=>['L\'Oréal Studio Line', 'TIGI Bed Head'],
                'Spray Fixador'=>['L\'Oréal Elnett Satin', 'Schwarzkopf Got2b'],
            ],
            'Petshop' => [
                'Shampoo para Cães'=>['Pet Society', 'Bioextratus Pets', 'Pet Clean'],
                'Condicionador para Cães'=>['Pet Society', 'Bioextratus Pets', 'Pet Clean'],
                'Spray Desembaraçador'=>['Pet Society', 'Bioextratus Pets'],
                'Perfume para Cães'=>['Pet Society', 'Pet Clean'],
                'Toalhas Umedecidas'=>['Pet Society', 'Pet Clean'],
                'Corta-Unhas para Cães'=>['Beter', 'Wahl', 'Furminator'],
                'Escova de Dentes para Cães'=>['Virbac', 'Sentry', 'Petsmile'],
                'Brinquedos para Cães'=>['KONG', 'Nylabone', 'Chuckit!'],
                'Ração Seca para Cães'=>['Pedigree', 'Royal Canin', 'Hill\'s Science Diet'],
                'Ração Úmida para Cães'=>['Pedigree', 'Royal Canin', 'Hill\'s Science Diet'],
                'Transporter para Cães'=>['Petmate', 'MidWest', 'Sherpa'],
                'Areia Higiênica para Gatos'=>['Cat\'s Best', 'Golden Cat', 'Sanicat'],
                'Brinquedos para Gatos'=>['Yeowww!', 'KONG', 'Cat Dancer'],
                'Arranhadores para Gatos'=>['Friskies', 'Petmaker', 'Toca do Coelho']          
            ],
            'Clínica Estética' => [
                'Creme Anti-idade'=>['Neutrogena Rapid Wrinkle Repair', 'Olay Regenerist', 'L\'Oréal Revitalift'],
                'Sérum de Vitamina C'=>['La Roche-Posay Pure Vitamin C10', 'Vichy Liftactiv Vitamin C Serum', 'The Ordinary Vitamin C Suspension'],
                'Protetor Solar Facial'=>['La Roche-Posay Anthelios', 'Neutrogena Ultra Sheer', 'Vichy Capital Soleil'],
                'Hidratante Facial'=>['CeraVe Moisturizing Cream', 'Neutrogena Hydro Boost', 'La Roche-Posay Toleriane'],
                'Tônico Facial'=>['Thayers Witch Hazel Toner', 'Pixi Glow Tonic', 'La Roche-Posay Effaclar'],
                'Máscara Facial'=>['Aztec Secret Indian Healing Clay', 'GlamGlow Supermud', 'The Body Shop Tea Tree Mask'],
            ],
            'Clínica Veterinária' => [
                'Antipulgas para Cães'=>['Frontline', 'Advantix', 'NexGard'],
                'Antipulgas para Gatos'=>['Frontline', 'Advocate', 'Revolution'],
                'Vermífugo para Cães'=>['Drontal', 'Panacur', 'Milbemax'],
                'Vermífugo para Gatos'=>['Drontal', 'Panacur', 'Milbemax'],
                'Suplementos Nutricionais'=>['Cosequin', 'Dasuquin', 'Nutramax'],
            ]
        ];


        // Detecta o tipo do tenant ativo (Stancl Tenancy)
        $tenantType = function_exists('tenant') && tenant('type') ? tenant('type') : 'Barbearia';
        if (!array_key_exists($tenantType, $products)) {
            // fallback para o primeiro tipo se não existir
            $tenantType = array_key_first($products);
        }
        $categories = array_keys($products[$tenantType]);
        $category = $this->faker->randomElement($categories);
        $productName = $this->faker->randomElement($products[$tenantType][$category]);

        return [
            'branch_id' => $this->faker->numberBetween(1, 3), // ajuste conforme sua estrutura
            'produto_nome' => $productName,
            'categoria' => $category,
            'quantidade_atual' => $this->faker->numberBetween(1, 100),
            'quantidade_minima' => $this->faker->numberBetween(1, 20),
            'preco_unitario' => $this->faker->randomFloat(2, 10, 500),
            'percentual_produtos' => $this->faker->numberBetween(5, 30),
            'fornecedor' => $this->faker->company,
            'data_validade' => $this->faker->dateTimeBetween('now', '+2 years'),
            'created_at' => now(),
            'updated_at' => now(),
            // outros campos necessários...
        ];
    }
}
