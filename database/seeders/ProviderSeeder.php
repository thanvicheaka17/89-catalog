<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Provider;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Pragmatic', 
                'logo' => 'pragmatic.webp', 
                'slug' => 'pragmatic', 
                'description' => 'Pragmatic Play is a leading game developer in the iGaming industry.', 
                'rtp_promax_name' => 'RTP PRAGMATIC PLAY',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => '82f16dca7450e03e39de843d2004f4ca5a1d082a.png', 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'PGSoft', 
                'logo' => 'pgsoft.webp', 
                'slug' => 'pgsoft', 
                'description' => 'PGSoft is a premium gaming provider specializing in slot games.', 
                'rtp_promax_name' => 'RTP PGSOFT',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => '780a559582ecd6c72759c73d3435bed5571cfca6.png', 'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'Joker', 
                'logo' => 'joker.webp', 
                'slug' => 'joker', 
                'description' => 'Joker Gaming is known for its innovative and engaging slot games.',
                'rtp_promax_name' => 'RTP JOKER SLOT',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => '82bfa6015436fd95ffd78f5fbbbb7b1132fcd1ca.png', 
                'rtp_promax_plus_logo' => '0426e402d7638c8dc25a45c5aa5bd300bdfd895e.png',
                'rtp_promax_plus_name' => 'JOKER',
            ],
            [
                'name' => 'Microgaming', 
                'logo' => 'microgaming.webp', 
                'slug' => 'microgaming', 
                'description' => 'Microgaming is a pioneer in the online gaming industry with award-winning games.',
                'rtp_promax_name' => 'RTP MICROGAMING',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => '630a8e687936da934aa1e78c214e53e05a66518b.png', 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'AdvantPlay', 
                'logo' => 'advantplay.webp', 
                'slug' => 'advantplay', 
                'description' => 'AdvantPlay specializes in premium gaming content and technology.',
                'rtp_promax_plus_name' => 'ADVANTPLAY',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => 'b124452e8bb989db8a50ee0a6a6485d7313d6b61.jpg',
            ],
            [
                'name' => 'AmbSlot', 
                'logo' => 'ambslot.webp', 
                'slug' => 'amb-slot', 
                'description' => 'AmbSlot delivers innovative slot gaming experiences.'
            ],
            [
                'name' => 'BigPot', 
                'logo' => 'bigpot.webp', 
                'slug' => 'bigpot', 
                'description' => 'BigPot Gaming specializes in high-quality slot games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'BigTime', 
                'logo' => 'bigtime.webp', 
                'slug' => 'big-time-gaming', 
                'description' => 'BigTime Gaming is known for its Megaways mechanics.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => 'ec0d06cc6a37108e0e514c3593bf0ac7ab0f0d58.png',
                'rtp_promax_plus_name' => 'BTG',
            ],
            [
                'name' => 'CQ9', 
                'logo' => 'cq9.webp', 
                'slug' => 'cq9', 
                'description' => 'CQ9 Gaming delivers premium gaming content.',
                'rtp_promax_name' => 'RTP CQ9',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => '7f07c335d455c0fad801cf80b9dc83fb992f7b42.png', 
                'rtp_promax_plus_logo' => 'c57ba10a8aec6ecc71ff7e267a90d1d32916fb7d.png',
                'rtp_promax_plus_name' => 'CQ9',
            ],
            [
                'name' => 'DragoonSoft', 
                'logo' => 'dragoonsoft.webp', 
                'slug' => 'dragoonsoft', 
                'description' => 'DragoonSoft specializes in innovative gaming technology.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => '166a48b24221bcae396b14f0876e71a8994fc5dc.png',
                'rtp_promax_plus_name' => 'DRAGOONSOFT',
            ],
            [
                'name' => 'Drasil', 
                'logo' => 'drasil.webp', 
                'slug' => 'yggdrasil', 
                'description' => 'Drasil Gaming creates engaging and entertaining games.',
                'rtp_promax_name' => 'RTP YGGDRASIL SLOT',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => 'babcacaeee028a6448b6497cae3c6234c7f5b0f8.png', 
                'rtp_promax_plus_logo' => '4654d009f69c724bedd74a9a7ced8c7d5b7375b1.png',
                'rtp_promax_plus_name' => 'YGG',
            ],
            [
                'name' => 'FaChai', 'logo' => 'fachai.webp', 'slug' => 'fachai', 'description' => 'FaChai Gaming offers unique gaming experiences.',
                'rtp_promax_name' => 'RTP FACHAI',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => '7144b140140f5b214a5997d8d007b2fe6d2e2214.png', 
                'rtp_promax_plus_logo' => '533ba4e23c7fc113bd0424851cabecbc53a94f3e.png',
                'rtp_promax_plus_name' => 'FACHAI',
            ],
            ['name' => 'FastSpin', 'logo' => 'fastspin.webp', 'slug' => 'fast-spin', 'description' => 'FastSpin Gaming provides fast and entertaining games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => 'f8c4cc300126485f89d6c670f3a44c77da9ae2ca.jpg',
                'rtp_promax_plus_name' => 'FASTSPIN',
            ],
            [
                'name' => 'FatPanda', 
                'logo' => 'fatpanda.webp', 
                'slug' => 'fat-panda', 
                'description' => 'FatPanda Gaming specializes in Asian-themed games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_name' => 'FATPANDA',
                'rtp_promax_plus_logo' => '9f5c12ad69677a05d735b9c20c61a86a72053857.png'
            ],
            [
                'name' => 'FunGaming', 'logo' => 'fungaming.webp', 'slug' => 'fun-gaming', 'description' => 'FunGaming creates fun and engaging gaming content.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [   
                'name' => 'FunkyGames', 'logo' => 'funkygames.webp', 'slug' => 'funky-games', 'description' => 'FunkyGames offers creative and innovative games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'Habanero', 
                'logo' => 'habanero.webp', 
                'slug' => 'habanero', 
                'description' => 'Habanero Gaming is known for its high-quality slot games.',
                'rtp_promax_name' => 'RTP HABANERO',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => '52996d67a9e07d50dcd506d10894d48d42d7a7b8.png', 
                'rtp_promax_plus_logo' => '9a7b9fc8806e7ff27a24127f00b0b790c5c1d975.jpg',
                'rtp_promax_plus_name' => 'HABANERO',
            ],
            [
                'name' => 'Hackshaw', 'logo' => 'hackshaw.webp', 'slug' => 'hacksaw', 'description' => 'Hackshaw Gaming delivers unique gaming experiences.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'IonSlot', 'logo' => 'ionslot.webp', 'slug' => 'ion-slot', 'description' => 'IonSlot Gaming specializes in modern slot games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'JDB', 'logo' => 'jdb.webp', 'slug' => 'jdb', 'description' => 'JDB Gaming offers comprehensive gaming solutions.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'Jili', 
                'logo' => 'jili.webp', 
                'slug' => 'jili', 'description' => 'Jili Gaming provides exciting gaming experiences.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => '75abde2513478dafda04473862bc97df73eb5342.jpg',
                'rtp_promax_plus_name' => 'JILI',
            ],
            [
                'name' => 'KingMidas', 'logo' => 'kingmidas.webp', 'slug' => 'kingmidas', 'description' => 'KingMidas Gaming specializes in slot games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'Live22', 'logo' => 'live22.webp', 'slug' => 'live22', 'description' => 'Live22 provides live gaming solutions.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => 'd3c463c2109cc3f48c78c96b9b03ad74db56282f.png',
                'rtp_promax_plus_name' => 'LIVE22',
            ],
            [
                'name' => 'MarioClub', 'logo' => 'marioclub.webp', 'slug' => 'mario-club', 'description' => 'MarioClub Gaming provides entertaining games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'Naga', 'logo' => 'naga.webp', 'slug' => 'naga-games', 'description' => 'Naga Gaming delivers high-quality gaming content.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => '3eb2ec9ee0701df078c52f5f84c36d2adf725a63.png',
                'rtp_promax_plus_name' => 'NAGAGAMES',
            ],
            [
                'name' => 'NetEnt', 'logo' => 'netent.webp', 'slug' => 'netent', 'description' => 'NetEnt is a leading provider of premium gaming solutions.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => 'a4daa2553024b88c85e7d30c4587ccdfec75fded.png',
                'rtp_promax_plus_name' => 'NETENT',
            ],
            [
                'name' => 'NoLimit', 
                'logo' => 'nolimit.webp', 
                'slug' => 'no-limit-city', 
                'description' => 'NoLimit City creates award-winning slot games.',
                'rtp_promax_name' => 'RTP NOLIMIT CITY',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => 'd8e69fd4b2c9736170f4eee1714637b4329bcfca.png', 
                'rtp_promax_plus_logo' => 'bcc3535b93b15a18b4b27c5a79e59114a788f5f1.png',
                'rtp_promax_plus_name' => 'NOLIMITCITY',
            ],
            [
                'name' => 'OctoPlay', 'logo' => 'octoplay.webp', 'slug' => 'octoplay', 'description' => 'OctoPlay provides creative gaming solutions.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'OnlyPlay', 'logo' => 'onlyplay.webp', 'slug' => 'only-play', 'description' => 'OnlyPlay Gaming specializes in premium content.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'PlaynGO', 'logo' => 'playngo.webp', 'slug' => 'playngo', 'description' => 'PlaynGO is known for its high-quality slot games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => '4965de39fdcaa6c2fe5e52f0933a886ed709d050.jpg',
                'rtp_promax_plus_name' => 'PLAYNGO',
            ],
            [
                'name' => 'PlayStar', 'logo' => 'playstar.webp', 'slug' => 'playstar', 'description' => 'PlayStar Gaming offers engaging gaming experiences.',
                'rtp_promax_name' => 'RTP PLAYSTAR',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => '40db56491500952647fd3d897789cdf4f7b0df1d.png', 
                'rtp_promax_plus_logo' => 'd66ec4654300a92711916d1bd7dbdae40b498114.png',
                'rtp_promax_plus_name' => 'PLAYSTAR',
            ],
            [
                'name' => 'Playtech', 'logo' => 'playtech.webp', 'slug' => 'playtech', 'description' => 'Playtech is a global leader in gaming technology.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'RedTiger', 'logo' => 'redtiger.webp', 'slug' => 'red-tiger', 'description' => 'RedTiger Gaming creates exciting slot games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'SkyWind', 'logo' => 'skywind.webp', 'slug' => 'skywind', 'description' => 'SkyWind Gaming delivers innovative games.'],
            [
                'name' => 'Slot88', 'logo' => 'slot88.webp', 'slug' => 'slot88', 'description' => 'Slot88 provides exciting gaming experiences.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'SmartSoft', 'logo' => 'smartsoft.webp', 'slug' => 'smartsoft', 'description' => 'SmartSoft Gaming specializes in smart gaming solutions.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'SpadeGaming', 
                'logo' => 'spadegaming.webp', 
                'slug' => 'spade-gaming', 
                'description' => 'SpadeGaming offers premium Asian gaming content.',
                'rtp_promax_name' => 'RTP SPADEGAMING',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => true, 
                'rtp_promax_logo' => '32a9afad9e538f1fa047fe031a112ab791ead11e.png', 
                'rtp_promax_plus_logo' => '6913bd8e2260dba71824014baaba2566db4e4cee.png',
                'rtp_promax_plus_name' => 'SPADEGAMING',
            ],
            [
                'name' => 'Spinix', 'logo' => 'spinix.webp', 'slug' => 'spinix', 'description' => 'Spinix Gaming provides creative gaming experiences.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'TopTrend', 
                'logo' => 'toptrend.webp', 
                'slug' => 'toptrend-gaming', 
                'description' => 'TopTrend Gaming delivers top-quality games.',
                'rtp_promax_name' => 'RTP TOPTREND',
                'is_rtp_promax' => true, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => '9823ae325e6e6fefb13e899522358ecb173f88d0.png', 
                'rtp_promax_plus_logo' => null
            ],
            [
                'name' => 'Mancala', 'logo' => 'mancala.webp', 'slug' => 'mancala-gaming', 'description' => 'Mancala Gaming delivers top-quality games.',
                'is_rtp_promax' => false, 
                'is_rtp_promax_plus' => false, 
                'rtp_promax_logo' => null, 
                'rtp_promax_plus_logo' => null
            ],

            /* no image */
            ['name' => 'Boominggame', 'logo' => 'booming-game.webp', 'slug' => 'booming-game', 'description' => 'Boominggame provides exciting gaming experiences.'],
            ['name' => 'Ace333', 'logo' => 'ace333.webp', 'slug' => 'ace333', 'description' => 'Ace333 delivers innovative gaming solutions.'],
            ['name' => 'Apollo777', 'logo' => 'apollo777.webp', 'slug' => 'apollo777', 'description' => 'Apollo777 provides premium gaming content.'],
            ['name' => 'Besoft', 'logo' => 'besoft.webp', 'slug' => 'besoft', 'description' => 'Besoft Gaming specializes in high-quality games.'],
            ['name' => 'Bgaming', 'logo' => 'bgaming.webp', 'slug' => 'bgaming', 'description' => 'Bgaming creates engaging and entertaining games.'],
            ['name' => 'Bng', 'logo' => 'bng.webp', 'slug' => 'bng', 'description' => 'Bng Gaming delivers unique gaming experiences.'],
            ['name' => 'Eagaming', 'logo' => 'eagaming.webp', 'slug' => 'eagaming', 'description' => 'Eagaming provides exciting gaming content.'],
            ['name' => 'Evoplay', 'logo' => 'evoplay.webp', 'slug' => 'evoplay', 'description' => 'Evoplay specializes in innovative gaming technology.'],
            ['name' => 'Fiveggame', 'logo' => '5gaming.webp', 'slug' => '5gaming', 'description' => 'Fiveggame offers creative gaming solutions.'],
            ['name' => 'Gmw', 'logo' => 'gmw.webp', 'slug' => 'gmw', 'description' => 'Gmw Gaming provides entertaining games.'],
            ['name' => 'I8game', 'logo' => 'i8.webp', 'slug' => 'i8', 'description' => 'I8game delivers premium gaming experiences.'],
            ['name' => 'Kagaming', 'logo' => 'ka-gaming.webp', 'slug' => 'ka-gaming', 'description' => 'Kagaming specializes in slot games.'],
            ['name' => 'Nextspin', 'logo' => 'nextspin.webp', 'slug' => 'nextspin', 'description' => 'Nextspin provides fast and entertaining games.'],
            ['name' => 'Onegame', 'logo' => 'onegame.webp', 'slug' => 'onegame', 'description' => 'Onegame creates engaging gaming experiences.'],
            ['name' => 'Pegasus', 'logo' => 'pegasus.webp', 'slug' => 'pegasus', 'description' => 'Pegasus Gaming delivers high-quality content.'],
            ['name' => 'Relaxgaming', 'logo' => 'relax-gaming.webp', 'slug' => 'relax-gaming', 'description' => 'Relaxgaming specializes in premium games.'],
            ['name' => 'Sbobet', 'logo' => 'sbobet.webp', 'slug' => 'sbobet', 'description' => 'Sbobet offers comprehensive gaming solutions.'],
            ['name' => 'Uu', 'logo' => 'uu.webp', 'slug' => 'uu', 'description' => 'Uu Gaming provides exciting gaming experiences.'],


        ];

        foreach ($providers as $provider) {
            // Helper function to build logo URL
            $buildLogoUrl = function($logo, $basePath) {
                if (empty($logo)) {
                    return null;
                }
                // If already a full path (starts with /), use it directly; otherwise prepend the base path
                if (substr($logo, 0, 1) === '/') {
                    return $logo;
                }
                return $basePath . $logo;
            };

            Provider::create([
                'name' => $provider['name'],
                'slug' => $provider['slug'],
                'description' => $provider['description'],
                'logo' => 'images/providers/' . $provider['logo'],
                'rtp_promax_name' => $provider['rtp_promax_name'] ?? null,
                'is_rtp_promax' => $provider['is_rtp_promax'] ?? false,
                'is_rtp_promax_plus' => $provider['is_rtp_promax_plus'] ?? false,
                'rtp_promax_logo' => $buildLogoUrl($provider['rtp_promax_logo'] ?? null, 'images/providers/rtp-promax/'),
                'rtp_promax_plus_logo' => $buildLogoUrl($provider['rtp_promax_plus_logo'] ?? null, 'images/providers/rtp-promax-plus/'),
                'rtp_promax_plus_name' => $provider['rtp_promax_plus_name'] ?? null,
            ]);
        }
    }
}
