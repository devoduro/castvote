<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AwardEventSeeder extends Seeder
{
    private array $categories = [
        ['name' => 'Artiste of the Year',             'code' => '01'],
        ['name' => 'Song of the Year',                'code' => '02'],
        ['name' => 'Album of the Year',               'code' => '03'],
        ['name' => 'New Artiste of the Year',         'code' => '04'],
        ['name' => 'Afrobeats/Afropop Song of Year',  'code' => '05'],
        ['name' => 'Highlife Song of the Year',       'code' => '06'],
        ['name' => 'Gospel Song of the Year',         'code' => '07'],
        ['name' => 'Hip Hop Song of the Year',        'code' => '08'],
    ];

    private array $nominees = [
        '01' => [ // Artiste of the Year
            ['name' => 'Sarkodie',    'code' => '01', 'bio' => 'Multiple VGMA Artiste of the Year winner and Ghana\'s most decorated rapper. Known for his witty wordplay and unmatched pen game.'],
            ['name' => 'Stonebwoy',   'code' => '02', 'bio' => 'Reggae/Dancehall icon and VGMA Artiste of the Year, known for his powerful live performances and anthems like "Epistles of Mama".'],
            ['name' => 'King Promise','code' => '03', 'bio' => 'Afrobeats sensation with smooth melodies and crossover hits across Africa and Europe including "Slow Down" and "Terminator".'],
            ['name' => 'Black Sherif','code' => '04', 'bio' => 'Rising Afrobeats star whose "Second Sermon" became a Pan-African viral anthem reaching millions worldwide.'],
            ['name' => 'KiDi',        'code' => '05', 'bio' => 'Lynx Entertainment artiste and highlife-afrobeats fusion star, VGMA Artiste of the Year 2022 known for "Touch It".'],
        ],
        '02' => [ // Song of the Year
            ['name' => 'Black Sherif – Second Sermon',  'code' => '01', 'bio' => 'The Konongo Zongo-born artist\'s breakout anthem that swept across Africa in 2021-2022.'],
            ['name' => 'Sarkodie – Non Living Thing',   'code' => '02', 'bio' => 'A groundbreaking freestyle over a Burna Boy instrumental that showcased Sarkodie\'s global appeal.'],
            ['name' => 'Camidoh – Sugarcane',           'code' => '03', 'bio' => 'Sweet afrobeats tune featuring King Promise that dominated Ghana\'s airwaves for months.'],
            ['name' => 'KiDi – Touch It',               'code' => '04', 'bio' => 'The TikTok sensation that went viral globally and introduced KiDi to millions of new fans.'],
            ['name' => 'Stonebwoy – Activate',          'code' => '05', 'bio' => 'High-energy dancehall banger that became a crowd favourite at every Ghanaian party and event.'],
        ],
        '03' => [ // Album of the Year
            ['name' => 'Sarkodie – No Pressure',       'code' => '01', 'bio' => 'A genre-defying 20-track project showcasing Sarkodie\'s versatility and lyrical mastery.'],
            ['name' => 'Stonebwoy – Anloga Junction',  'code' => '02', 'bio' => 'A critically acclaimed Afropop/Reggae fusion album that peaked on multiple African charts.'],
            ['name' => 'King Promise – As Promised',   'code' => '03', 'bio' => 'A smooth afrobeats album delivering on every promise of King Promise\'s immense talent.'],
            ['name' => 'KiDi – The Golden Boy',        'code' => '04', 'bio' => 'A polished project cementing KiDi\'s status as Ghana\'s golden boy of afrobeats and highlife.'],
        ],
        '04' => [ // New Artiste of the Year
            ['name' => 'Camidoh',     'code' => '01', 'bio' => 'Singer-songwriter from Tema whose "Sugarcane" made him an overnight household name across Ghana.'],
            ['name' => 'Gyakie',      'code' => '02', 'bio' => 'KNUST graduate turned Afropop star, known for "Forever" remixed internationally with Omah Lay.'],
            ['name' => 'Cina Soul',   'code' => '03', 'bio' => 'Soulful songstress with a unique sound blending Afrobeats with blues and folk traditions.'],
            ['name' => 'Fameye',      'code' => '04', 'bio' => 'Highlife-afrobeats artiste known for emotional storytelling and his breakout hit "Nothing I Get".'],
        ],
        '05' => [ // Afrobeats/Afropop
            ['name' => 'King Promise – Terminator', 'code' => '01', 'bio' => 'An infectious afrobeats anthem with a catchy hook that dominated radio across West Africa.'],
            ['name' => 'Camidoh – Sugarcane',       'code' => '02', 'bio' => 'The year\'s biggest crossover afropop hit, earning millions of streams on Spotify and Apple Music.'],
            ['name' => 'Mr Drew – Eat',             'code' => '03', 'bio' => 'An energetic afrobeats jam accompanied by a viral dance challenge that swept social media.'],
            ['name' => 'Medikal – Omo Ada',         'code' => '04', 'bio' => 'An afrobeats street anthem with a mix of rap and singing that resonated with Ghanaian youth.'],
        ],
        '06' => [ // Highlife
            ['name' => 'Kuami Eugene – Open Gate',      'code' => '01', 'bio' => 'A rich highlife song with contemporary production showcasing Kuami\'s powerful vocal runs.'],
            ['name' => 'Kofi Kinaata – Thy Grace',      'code' => '02', 'bio' => 'A heartfelt highlife ballad giving thanks that became an anthem of gratitude in Ghana.'],
            ['name' => 'KiDi – Say Cheese',             'code' => '03', 'bio' => 'A fun highlife-infused afrobeats song with an unforgettable visual and positive energy.'],
            ['name' => 'Fameye – Nothing I Get',        'code' => '04', 'bio' => 'An emotionally-charged highlife song about overcoming poverty that resonated with millions.'],
        ],
        '07' => [ // Gospel
            ['name' => 'Diana Hamilton – Adom',   'code' => '01', 'bio' => 'An anointed worship song about grace that swept the Gospel Music Awards and topped charts for weeks.'],
            ['name' => 'Joe Mettle – Bo Noo Ni',  'code' => '02', 'bio' => 'A contemporary gospel anthem that became a staple in Ghanaian churches and worship concerts.'],
            ['name' => 'Cwesi Oteng – Imela',     'code' => '03', 'bio' => 'A cross-border gospel collaboration celebrating God\'s goodness in both Twi and Igbo.'],
        ],
        '08' => [ // Hip Hop
            ['name' => 'Sarkodie – Oouu (Response)', 'code' => '01', 'bio' => 'A lyrical demolition track that reminded everyone why Sarkodie is Ghana\'s greatest rapper.'],
            ['name' => 'Medikal – Omo Ada',          'code' => '02', 'bio' => 'Street banger blending trap beats with Ghanaian slang, making Medikal undeniable in 2024.'],
            ['name' => 'Obibini – 730',              'code' => '03', 'bio' => 'Underground lyrical masterpiece that earned Obibini recognition as one of Ghana\'s top MCs.'],
            ['name' => 'Yaw Tog – Sore',             'code' => '04', 'bio' => 'The teen sensation\'s breakout drill track that went viral and earned a remix with Stormzy.'],
        ],
    ];

    public function run(): void
    {
        $org = Organization::where('name', 'Ghana Music Awards Ltd')->first();

        $event = Event::create([
            'organization_id' => $org->id,
            'name'            => 'Vodafone Ghana Music Awards 2025',
            'slug'            => 'vgma-2025',
            'event_type'      => 'award',
            'voting_rules'    => [
                'pay_per_vote'              => true,
                'price_per_vote_pesewas'    => 100,
                'max_votes_per_voter'       => null,
                'requires_eligibility_list' => false,
                'anonymous_tally'           => false,
            ],
            'ussd_shortcode' => '*920*134#',
            'ussd_short_id'  => '240',
            'starts_at'      => now()->subDays(3),
            'ends_at'        => now()->addDays(28),
            'status'         => 'live',
        ]);

        foreach ($this->categories as $order => $catData) {
            $category = Category::create([
                'event_id'      => $event->id,
                'name'          => $catData['name'],
                'code'          => $catData['code'],
                'display_order' => $order + 1,
            ]);

            $nomineeList = $this->nominees[$catData['code']] ?? [];
            foreach ($nomineeList as $nomOrder => $nomData) {
                Nominee::create([
                    'category_id'   => $category->id,
                    'name'          => $nomData['name'],
                    'code'          => $nomData['code'],
                    'bio'           => $nomData['bio'],
                    'photo_path'    => null,
                    'display_order' => $nomOrder + 1,
                ]);
            }
        }

        // Seed some sample votes and payments for the results dashboard
        $this->seedSampleVotes($event);
    }

    private function seedSampleVotes(Event $event): void
    {
        $ghanaNetworks = [
            'mtn'        => ['024', '054', '055', '059'],
            'vodafone'   => ['020', '050'],
            'airteltigo' => ['026', '056', '027', '057'],
        ];

        $categories = $event->categories()->with('nominees')->get();

        foreach ($categories as $category) {
            foreach ($category->nominees as $nominee) {
                // Give each nominee a random vote count so the dashboard has data
                $voteCount = rand(50, 5000);
                $qty       = rand(1, 5);
                $batches   = (int) ceil($voteCount / $qty);

                for ($i = 0; $i < min($batches, 20); $i++) {
                    $network = array_rand($ghanaNetworks);
                    $prefix  = $ghanaNetworks[$network][array_rand($ghanaNetworks[$network])];
                    $phone   = $prefix . str_pad(rand(1000000, 9999999), 7, '0');

                    $payment = Payment::create([
                        'event_id'           => $event->id,
                        'provider'           => 'paystack',
                        'provider_reference' => 'cv_seed_' . Str::random(16),
                        'amount_pesewas'     => $qty * 100,
                        'currency'           => 'GHS',
                        'phone_number'       => $phone,
                        'momo_network'       => $network,
                        'status'             => 'success',
                        'metadata'           => [
                            'nominee_id'  => $nominee->id,
                            'category_id' => $category->id,
                            'quantity'    => $qty,
                        ],
                        'verified_at' => now()->subMinutes(rand(1, 2000)),
                    ]);

                    Vote::create([
                        'event_id'    => $event->id,
                        'category_id' => $category->id,
                        'nominee_id'  => $nominee->id,
                        'quantity'    => $qty,
                        'channel'     => rand(0, 1) ? 'ussd' : 'web',
                        'voter_phone' => $phone,
                        'payment_id'  => $payment->id,
                    ]);
                }
            }
        }
    }
}
