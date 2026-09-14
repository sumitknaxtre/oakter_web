<?php

namespace Database\Seeders;

use App\Models\Dealer;
use Illuminate\Database\Seeder;

class DealerSeeder extends Seeder
{
    /**
     * Replace dealers with the Studio AC retail outlet list from the client handoff.
     * Re-runnable on local and server: deletes existing rows, then inserts fresh data.
     */
    public function run(): void
    {
        Dealer::query()->delete();

        $now = now();
        $rows = [];

        foreach ($this->dealers() as $index => $dealer) {
            $rows[] = [
                ...$dealer,
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Dealer::query()->insert($rows);
    }

    /**
     * @return list<array{name: string, address: string, phone: ?string, map_url: ?string, state: string, district: string}>
     */
    private function dealers(): array
    {
        return [
            [
                'name' => 'SUNIL ENTERPRISES',
                'address' => 'SUNIL ENTERPRISES, SP JAIN COLLEGE ROAD Nearby SP JAIN COLLEGE, Mahdiganj, Sasaram, Rohtas. State: Bihar PIN Code: 821115',
                'phone' => '9470040140',
                'map_url' => null,
                'state' => 'Bihar',
                'district' => 'Rohtas',
            ],
            [
                'name' => 'ELECTRONICS MAHAL',
                'address' => 'Electronics Mahal/Jewar Mahal. Mohalla Rahamganj, Naka 6, near Ram Janki Mandir, Darbhanga, Bihar. PIN Code: 846004',
                'phone' => '9835602737',
                'map_url' => null,
                'state' => 'Bihar',
                'district' => 'Darbhanga',
            ],
            [
                'name' => 'MOBILE CARE',
                'address' => 'Mobile Care, Under Indian Bank, Rambabu Chowk, Samastipur, Bihar. PIN Code: 848101',
                'phone' => '7209104510',
                'map_url' => null,
                'state' => 'Bihar',
                'district' => 'Samastipur',
            ],
            [
                'name' => 'AJAY WATCH AND RADIO',
                'address' => 'Vitthal Baba Sthan Chaugai Dumraon, Buxar, Bihar 802115',
                'phone' => '99340 77947',
                'map_url' => null,
                'state' => 'Bihar',
                'district' => 'Buxar',
            ],
            [
                'name' => 'Amazin Deals',
                'address' => 'NH21, Chandigarh Kharar Road, adjoining Palki Palace-II, Sector 119, Balongi, Sahibzada Ajit Singh Nagar, Punjab 160055',
                'phone' => '7009876931',
                'map_url' => null,
                'state' => 'Chandigarh (Tricity)',
                'district' => 'SAS Nagar',
            ],
            [
                'name' => 'Amazin Deals',
                'address' => 'Aarkay Warehouse, Godown Road, near Modi Kunj Society, Bhabat, Zirakpur, Punjab 140603',
                'phone' => '7009876931',
                'map_url' => null,
                'state' => 'Chandigarh (Tricity)',
                'district' => 'SAS Nagar',
            ],
            [
                'name' => 'TECNIQA',
                'address' => 'Laxmi Nivas, Near Vinayak City, Bhatagaon, Raipur, Chhattisgarh 492001',
                'phone' => '6264168200',
                'map_url' => null,
                'state' => 'Chhattisgarh',
                'district' => 'Raipur',
            ],
            [
                'name' => 'Aaliya Mobile Care',
                'address' => 'Property No. 181, Block C, Shop No. 4, Kaushal Cinema Marg, Sohail Telecom, Jahangirpuri, Delhi 110033',
                'phone' => '75039 02652',
                'map_url' => 'https://share.google/Ks0PBG6iItywmxaIs',
                'state' => 'Delhi/NCR',
                'district' => 'North West',
            ],
            [
                'name' => 'ANKUR ELECTRICALS',
                'address' => 'B-1/32, Sector 18, Noida, Gautam Buddha Nagar, Uttar Pradesh 201301',
                'phone' => '99717 33337',
                'map_url' => null,
                'state' => 'Delhi/NCR',
                'district' => 'Gautam Buddha Nagar',
            ],
            [
                'name' => 'Divine Enterprise',
                'address' => 'NAV Kalpataru CHS, Shop No. 5, Plot No. C4, Sector 09, Airoli, Navi Mumbai 400708',
                'phone' => '93222 78926',
                'map_url' => null,
                'state' => 'Maharashtra',
                'district' => 'Thane',
            ],
            [
                'name' => 'MULTIDEAL CORPORATION',
                'address' => 'Plot No. 19, Adjacent to Anjani Eye Hospital, Central Bazar Road, Ramdaspeth, Nagpur 10',
                'phone' => '9822224557',
                'map_url' => null,
                'state' => 'Maharashtra',
                'district' => 'Nagpur',
            ],
            [
                'name' => 'BHU Brahhma Solutions',
                'address' => 'GST No. 27BLKPP2702E1Z9, 236, Trimurti Nagar, Lokseva Nagar 440022',
                'phone' => '8999513353',
                'map_url' => null,
                'state' => 'Maharashtra',
                'district' => 'Nagpur',
            ],
            [
                'name' => 'SHREE VARENYAM CONSOLIDATE',
                'address' => 'NH16, Service Road, Post Konisi, Berhampur, Ganjam, Odisha 761008',
                'phone' => '94370 64032',
                'map_url' => null,
                'state' => 'Odisha',
                'district' => 'Ganjam',
            ],
            [
                'name' => 'Sumanta Electricals',
                'address' => 'Plot No. 854/3368, Old Jagannath Road, Madhupatana, Cuttack, Odisha 753010',
                'phone' => '7205675070',
                'map_url' => null,
                'state' => 'Odisha',
                'district' => 'Cuttack',
            ],
            [
                'name' => 'NARAYAN SALES',
                'address' => 'NC College, Jajpur Town, Jajpur, Odisha 755001',
                'phone' => '89840 16537',
                'map_url' => null,
                'state' => 'Odisha',
                'district' => 'Jajapur',
            ],
            [
                'name' => 'TECNIQA',
                'address' => 'Shop No. 02, Khata No. 278, Plot No. 925, Behind Shakti Continental, 6th Lane, Amalapada, Angul, Odisha 759122',
                'phone' => '9937430129',
                'map_url' => null,
                'state' => 'Odisha',
                'district' => 'Anugul',
            ],
            [
                'name' => 'Malhotra Electronics Mall',
                'address' => 'Ground Floor, Shimla Pahari Chownk, Hoshiarpur, Punjab 146001',
                'phone' => '9914100400',
                'map_url' => null,
                'state' => 'Punjab',
                'district' => 'Hoshiarpur',
            ],
            [
                'name' => 'Madan Lal and Sons',
                'address' => '345, Shopping Centre, Kota, Rajasthan 324007',
                'phone' => '9983377833',
                'map_url' => null,
                'state' => 'Rajasthan',
                'district' => 'Kota',
            ],
            [
                'name' => 'SHREEJI ENGINEERING',
                'address' => '301, Keshavpura Sector 7, Kota, Rajasthan 324009',
                'phone' => '7024048662',
                'map_url' => null,
                'state' => 'Rajasthan',
                'district' => 'Kota',
            ],
            [
                'name' => 'Bhomi Solar',
                'address' => 'Gokul Vihar Colony, Ward No. 22, Near Dhod Choraha, Sikar, Rajasthan 332001',
                'phone' => '9870494625',
                'map_url' => null,
                'state' => 'Rajasthan',
                'district' => 'Sikar',
            ],
            [
                'name' => 'SATV Technologies',
                'address' => 'Plot No. 20, KH No. 17, Chhota Bharwara, Chinhat, Lucknow, Uttar Pradesh 226010',
                'phone' => '8896542098',
                'map_url' => null,
                'state' => 'Uttar Pradesh',
                'district' => 'Lucknow',
            ],
            [
                'name' => 'JAIN ELECTRIC CORNER',
                'address' => '82/1, Railway Road, Meerut, Uttar Pradesh 250002',
                'phone' => null,
                'map_url' => null,
                'state' => 'Uttar Pradesh',
                'district' => 'Meerut',
            ],
            [
                'name' => 'NAVEEN SOLAR SOLUTION',
                'address' => 'Sandeela Road near Bus Stop, Bangarmau, Unnao, Uttar Pradesh 209868. 09FRDPS9793H1ZS',
                'phone' => '9897186701',
                'map_url' => null,
                'state' => 'Uttar Pradesh',
                'district' => 'Unnao',
            ],
            [
                'name' => 'BHARDWAJ SUPER STORE',
                'address' => 'Building No./Flat No. 0, Tamkuhi Road, Sewarhi, Kushinagar, Uttar Pradesh 274406, 09DDWPM1817D2Z8',
                'phone' => '9559844378',
                'map_url' => null,
                'state' => 'Uttar Pradesh',
                'district' => 'Kushinagar',
            ],
            [
                'name' => 'ELECTRO VISION',
                'address' => 'B17/12S Kalyani, Kalyani, Nadia, West Bengal 741235',
                'phone' => '9831649873',
                'map_url' => null,
                'state' => 'West Bengal',
                'district' => 'Nadia',
            ],
        ];
    }
}
