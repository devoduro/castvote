<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class NomineeFactory extends Factory
{
    private static array $ghanaianArtistes = [
        ['name' => 'Sarkodie', 'bio' => 'Multiple VGMA Artiste of the Year winner and Ghana\'s most decorated rapper.'],
        ['name' => 'Stonebwoy', 'bio' => 'Reggae/Dancehall icon and VGMA Artiste of the Year winner, known for his powerful live performances.'],
        ['name' => 'Shatta Wale', 'bio' => 'Self-proclaimed dancehall king of Africa with a massive fan base across the continent.'],
        ['name' => 'King Promise', 'bio' => 'Afrobeats sensation known for smooth melodies and crossover hits across Africa and Europe.'],
        ['name' => 'Black Sherif', 'bio' => 'Rising Afrobeats star whose "Second Sermon" became a Pan-African viral anthem.'],
        ['name' => 'Gyakie', 'bio' => 'KNUST graduate turned Afropop star, known for her hit "Forever" remixed with Omah Lay.'],
        ['name' => 'Camidoh', 'bio' => 'Afropop singer-songwriter known for the smash hit "Sugarcane" featuring King Promise.'],
        ['name' => 'KiDi', 'bio' => 'Lynx Entertainment artiste and highlife-afrobeats fusion star, VGMA Artiste of the Year 2022.'],
        ['name' => 'Kuami Eugene', 'bio' => 'Highlife revival pioneer and VGMA artiste winner, known for his rich vocal runs.'],
        ['name' => 'Efya', 'bio' => 'Ghana\'s leading female R&B/Soul vocalist, collaborator to major acts across Africa.'],
        ['name' => 'Cina Soul', 'bio' => 'Soulful songstress known for her unique sound blending Afrobeats with blues and folk.'],
        ['name' => 'Diana Hamilton', 'bio' => 'Gospel superstar and VGMA Artiste of the Year 2021, known for "Adom".'],
        ['name' => 'Joe Mettle', 'bio' => 'Award-winning contemporary gospel artiste and worship leader.'],
        ['name' => 'Obrafour', 'bio' => 'Veteran Ghanaian rapper known as the "Rap Sofo" with decades of lyrical excellence.'],
        ['name' => 'Tic Tac', 'bio' => 'Pioneer of Ghanaian hiplife, one of the founding fathers of the genre.'],
        ['name' => 'Fameye', 'bio' => 'Highlife-afrobeats artiste known for his emotional storytelling and hit "Nothing I Get".'],
        ['name' => 'Medikal', 'bio' => 'AMG Business rapper known for fast-paced delivery and witty punchlines.'],
        ['name' => 'Kofi Kinaata', 'bio' => 'Western Region\'s finest, known for his storytelling highlife and "Thy Grace".'],
        ['name' => 'Mr Drew', 'bio' => 'Highlife-afrobeats dancer-turned-artiste known for "Feelings" and "Eat".'],
        ['name' => 'Kidi', 'bio' => 'Highlife sensation from Lynx Entertainment known for romantic ballads.'],
    ];

    private static int $nomineeIndex = 0;

    public function definition(): array
    {
        $artiste = self::$ghanaianArtistes[self::$nomineeIndex % count(self::$ghanaianArtistes)];
        self::$nomineeIndex++;

        return [
            'category_id'   => Category::factory(),
            'name'          => $artiste['name'],
            'code'          => str_pad(self::$nomineeIndex, 2, '0', STR_PAD_LEFT),
            'photo_path'    => null,
            'bio'           => $artiste['bio'],
            'display_order' => self::$nomineeIndex,
        ];
    }
}
