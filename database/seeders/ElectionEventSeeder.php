<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EligibleVoter;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class ElectionEventSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::where('name', 'University of Ghana SRC')->first();

        $event = Event::create([
            'organization_id' => $org->id,
            'name'            => 'UG SRC Presidential Election 2025',
            'slug'            => 'ug-src-election-2025',
            'event_type'      => 'election',
            'voting_rules'    => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => true,
            ],
            'ussd_shortcode' => '*920*134*310#',
            'ussd_short_id'  => '310',
            'starts_at'      => now()->addDays(7),
            'ends_at'        => now()->addDays(8),
            'status'         => 'draft',
        ]);

        // SRC President category
        $president = Category::create([
            'event_id'      => $event->id,
            'name'          => 'SRC President',
            'code'          => '01',
            'display_order' => 1,
        ]);

        $candidates = [
            ['name' => 'Nana Yaw Boateng',   'code' => '01', 'bio' => 'Level 300 BSc Computer Science. Running on a platform of digital transformation and improved campus Wi-Fi.'],
            ['name' => 'Abena Amoah',         'code' => '02', 'bio' => 'Level 400 LLB Law. Advocating for affordable campus accommodation and gender equity policies.'],
            ['name' => 'Kwesi Appiah-Kubi',   'code' => '03', 'bio' => 'Level 300 BSc Economics. Focused on student welfare funds and academic resource expansion.'],
        ];

        foreach ($candidates as $order => $c) {
            Nominee::create([
                'category_id'   => $president->id,
                'name'          => $c['name'],
                'code'          => $c['code'],
                'bio'           => $c['bio'],
                'display_order' => $order + 1,
            ]);
        }

        // VP category
        $vp = Category::create([
            'event_id'      => $event->id,
            'name'          => 'SRC Vice President',
            'code'          => '02',
            'display_order' => 2,
        ]);

        $vpCandidates = [
            ['name' => 'Esi Mensah',       'code' => '01', 'bio' => 'Level 200 BA Political Science. Passionate about student union democracy and transparent governance.'],
            ['name' => 'Kofi Acheampong',  'code' => '02', 'bio' => 'Level 300 BSc Agriculture. Championing rural student scholarships and inclusive campus culture.'],
        ];

        foreach ($vpCandidates as $order => $c) {
            Nominee::create([
                'category_id'   => $vp->id,
                'name'          => $c['name'],
                'code'          => $c['code'],
                'bio'           => $c['bio'],
                'display_order' => $order + 1,
            ]);
        }

        // Seed eligible voters (student index numbers)
        $indexNumbers = [];
        for ($i = 1; $i <= 50; $i++) {
            $indexNumbers[] = [
                'event_id'   => $event->id,
                'identifier' => '10' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'eligible'   => true,
                'voted_at'   => null,
            ];
        }

        foreach ($indexNumbers as $voter) {
            EligibleVoter::create($voter);
        }
    }
}
