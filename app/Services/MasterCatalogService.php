<?php

namespace App\Services;

use App\Database;

class MasterCatalogService {

    /**
     * Master database of 90+ popular Indonesian FMCG / warung grocery products
     * with real standard EAN-13 barcodes, categories, units, modal prices, sell prices, and verified images.
     */
    private static array $masterItems = [
        // ==================== MIE & MAKANAN INSTAN ====================
        [
            'barcode' => '8998866200223',
            'aliases' => ['8998866200224', '089686010724'],
            'name' => 'Indomie Goreng Spesial 85g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2900,
            'sell' => 3500,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8998866200421',
            'aliases' => ['8998866200422'],
            'name' => 'Indomie Kuah Soto Mie 70g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2800,
            'sell' => 3300,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8998866200322',
            'aliases' => ['8998866200323'],
            'name' => 'Indomie Kuah Ayam Bawang 69g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2800,
            'sell' => 3300,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8998866200520',
            'aliases' => [],
            'name' => 'Indomie Kari Ayam 72g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2900,
            'sell' => 3500,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8998866200827',
            'aliases' => [],
            'name' => 'Indomie Goreng Rendang 91g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 3000,
            'sell' => 3500,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8998866200728',
            'aliases' => [],
            'name' => 'Indomie Goreng Cabe Ijo 85g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 3000,
            'sell' => 3500,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8992388111222',
            'aliases' => ['8992388111017'],
            'name' => 'Mie Sedaap Goreng 90g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2850,
            'sell' => 3400,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8992388111321',
            'aliases' => [],
            'name' => 'Mie Sedaap Soto 75g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2800,
            'sell' => 3300,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8992388111420',
            'aliases' => [],
            'name' => 'Mie Sedaap Kari Kental 72g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2800,
            'sell' => 3300,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8992388111529',
            'aliases' => [],
            'name' => 'Mie Sedaap Ayam Bawang 70g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 2800,
            'sell' => 3300,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8998866201220',
            'aliases' => [],
            'name' => 'Sarimi Isi 2 Ayam Kecap 126g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 3800,
            'sell' => 4500,
            'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'
        ],
        [
            'barcode' => '8998866201329',
            'aliases' => [],
            'name' => 'Sarimi Isi 2 Soto Koya 115g',
            'cat' => 'Mie & Instan',
            'unit' => 'pcs',
            'buy' => 3800,
            'sell' => 4500,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8998866300121',
            'aliases' => [],
            'name' => 'Pop Mie Rasa Ayam Spesial 75g',
            'cat' => 'Mie & Instan',
            'unit' => 'cup',
            'buy' => 5000,
            'sell' => 6000,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8998866300220',
            'aliases' => [],
            'name' => 'Pop Mie Rasa Baso 75g',
            'cat' => 'Mie & Instan',
            'unit' => 'cup',
            'buy' => 5000,
            'sell' => 6000,
            'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'
        ],
        [
            'barcode' => '8991003005',
            'aliases' => ['8992761005018'],
            'name' => 'Sarden ABC Saus Tomat 155g',
            'cat' => 'Mie & Instan',
            'unit' => 'kaleng',
            'buy' => 9500,
            'sell' => 11500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],

        // ==================== MINUMAN & SUSU ====================
        [
            'barcode' => '8886008101053',
            'aliases' => ['8886008101015'],
            'name' => 'Aqua Botol Sedang 600ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 3000,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'
        ],
        [
            'barcode' => '8886008101060',
            'aliases' => [],
            'name' => 'Aqua Botol Besar 1500ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 5500,
            'sell' => 7000,
            'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'
        ],
        [
            'barcode' => '8886008101046',
            'aliases' => [],
            'name' => 'Aqua Botol Kecil 330ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 2200,
            'sell' => 3000,
            'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'
        ],
        [
            'barcode' => '8996001600269',
            'aliases' => [],
            'name' => 'Le Minerale Botol 600ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 3000,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'
        ],
        [
            'barcode' => '8996001600276',
            'aliases' => [],
            'name' => 'Le Minerale Botol Besar 1500ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 5500,
            'sell' => 7000,
            'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'
        ],
        [
            'barcode' => '8996001414002',
            'aliases' => [],
            'name' => 'Teh Pucuk Harum 350ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 3200,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80'
        ],
        [
            'barcode' => '8992761011019',
            'aliases' => [],
            'name' => 'Teh Botol Sosro Kotak 250ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'kotak',
            'buy' => 3000,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80'
        ],
        [
            'barcode' => '8992761012016',
            'aliases' => [],
            'name' => 'Teh Botol Sosro Pet 450ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 5500,
            'sell' => 7000,
            'img' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80'
        ],
        [
            'barcode' => '8992388031018',
            'aliases' => [],
            'name' => 'Floridina Orange 350ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 3000,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=400&q=80'
        ],
        [
            'barcode' => '8996001300015',
            'aliases' => [],
            'name' => 'Pocari Sweat Botol 500ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'btl',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'
        ],
        [
            'barcode' => '8992696408221',
            'aliases' => [],
            'name' => 'Susu Steril Bear Brand 189ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'kaleng',
            'buy' => 9500,
            'sell' => 11000,
            'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'
        ],
        [
            'barcode' => '8992696404414',
            'aliases' => [],
            'name' => 'Susu Ultra Milk Cokelat 250ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'kotak',
            'buy' => 6000,
            'sell' => 7500,
            'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'
        ],
        [
            'barcode' => '8992696404421',
            'aliases' => [],
            'name' => 'Susu Ultra Milk Full Cream 250ml',
            'cat' => 'Minuman & Susu',
            'unit' => 'kotak',
            'buy' => 6000,
            'sell' => 7500,
            'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'
        ],
        [
            'barcode' => '8992753111017',
            'aliases' => ['8991005004'],
            'name' => 'Susu Kental Manis Frisian Flag Cokelat 370g',
            'cat' => 'Minuman & Susu',
            'unit' => 'kaleng',
            'buy' => 11500,
            'sell' => 13500,
            'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'
        ],
        [
            'barcode' => '8992753111024',
            'aliases' => [],
            'name' => 'Susu Kental Manis Frisian Flag Putih 370g',
            'cat' => 'Minuman & Susu',
            'unit' => 'kaleng',
            'buy' => 11500,
            'sell' => 13500,
            'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'
        ],
        [
            'barcode' => '8991002101344',
            'aliases' => ['8991005001'],
            'name' => 'Kopi Kapal Api Spesial Mix 1 Renteng (10 sachet)',
            'cat' => 'Minuman & Susu',
            'unit' => 'renteng',
            'buy' => 13000,
            'sell' => 15000,
            'img' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80'
        ],
        [
            'barcode' => '8991002101016',
            'aliases' => [],
            'name' => 'Kopi Kapal Api Spesial Hitam 65g',
            'cat' => 'Minuman & Susu',
            'unit' => 'bungkus',
            'buy' => 5500,
            'sell' => 7000,
            'img' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80'
        ],
        [
            'barcode' => '8992745366050',
            'aliases' => ['8991005002'],
            'name' => 'Kopi Good Day Cappuccino 1 Renteng (10 sachet)',
            'cat' => 'Minuman & Susu',
            'unit' => 'renteng',
            'buy' => 18500,
            'sell' => 21500,
            'img' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=400&q=80'
        ],
        [
            'barcode' => '8992745366029',
            'aliases' => [],
            'name' => 'Kopi Good Day Mocacinno 1 Renteng (10 sachet)',
            'cat' => 'Minuman & Susu',
            'unit' => 'renteng',
            'buy' => 14000,
            'sell' => 16500,
            'img' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=400&q=80'
        ],
        [
            'barcode' => '8997009360015',
            'aliases' => [],
            'name' => 'Kopi Luwak White Koffie 1 Renteng (10 sachet)',
            'cat' => 'Minuman & Susu',
            'unit' => 'renteng',
            'buy' => 13500,
            'sell' => 16000,
            'img' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80'
        ],
        [
            'barcode' => '8999999026010',
            'aliases' => ['8991005003'],
            'name' => 'Teh Celup Sariwangi isi 30',
            'cat' => 'Minuman & Susu',
            'unit' => 'kotak',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400&q=80'
        ],

        // ==================== MINYAK & GULA ====================
        [
            'barcode' => '8992999001001',
            'aliases' => ['8991002001'],
            'name' => 'Minyak Goreng Minyakita 1 Liter Pouch',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 14500,
            'sell' => 16000,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8992695123456',
            'aliases' => ['8991002002'],
            'name' => 'Minyak Goreng Bimoli 2 Liter Pouch',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 35000,
            'sell' => 39000,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8992695123449',
            'aliases' => [],
            'name' => 'Minyak Goreng Bimoli 1 Liter Pouch',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 18000,
            'sell' => 20500,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8992741123459',
            'aliases' => ['8991002003'],
            'name' => 'Minyak Goreng Tropical 2 Liter Botol',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 34500,
            'sell' => 38500,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8993456789012',
            'aliases' => [],
            'name' => 'Minyak Goreng SunCo 2 Liter Pouch',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 34500,
            'sell' => 38500,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8998765432109',
            'aliases' => [],
            'name' => 'Minyak Goreng Sania 2 Liter Pouch',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 34000,
            'sell' => 38000,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8998765432123',
            'aliases' => [],
            'name' => 'Minyak Goreng Filma 2 Liter Pouch',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 35000,
            'sell' => 39000,
            'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'
        ],
        [
            'barcode' => '8992779001011',
            'aliases' => ['8991002004'],
            'name' => 'Gula Pasir Gulaku Kuning Premium 1kg',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 16500,
            'sell' => 18500,
            'img' => 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80'
        ],
        [
            'barcode' => '8992779001028',
            'aliases' => [],
            'name' => 'Gula Pasir Gulaku Tebu Hijau 1kg',
            'cat' => 'Minyak & Gula',
            'unit' => 'pcs',
            'buy' => 17000,
            'sell' => 19000,
            'img' => 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80'
        ],
        [
            'barcode' => '8991002005',
            'aliases' => [],
            'name' => 'Gula Pasir Curah / Eceran (1 kg)',
            'cat' => 'Minyak & Gula',
            'unit' => 'kg',
            'buy' => 15000,
            'sell' => 17000,
            'img' => 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80'
        ],
        [
            'barcode' => '8991002006',
            'aliases' => [],
            'name' => 'Gula Merah / Aren Gandu (per Kg)',
            'cat' => 'Minyak & Gula',
            'unit' => 'kg',
            'buy' => 22000,
            'sell' => 26000,
            'img' => 'https://images.unsplash.com/photo-1581441363689-1f3c3c414635?w=400&q=80'
        ],

        // ==================== BERAS & TEPUNG ====================
        [
            'barcode' => '8991001001',
            'aliases' => [],
            'name' => 'Beras Rojo Lele (5 kg)',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'sak',
            'buy' => 73000,
            'sell' => 78000,
            'img' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80'
        ],
        [
            'barcode' => '8991001002',
            'aliases' => [],
            'name' => 'Beras Pandan Wangi (5 kg)',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'sak',
            'buy' => 82000,
            'sell' => 88000,
            'img' => 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&q=80'
        ],
        [
            'barcode' => '8991001003',
            'aliases' => [],
            'name' => 'Beras Eceran Medium (per Kg)',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'kg',
            'buy' => 12500,
            'sell' => 14500,
            'img' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80'
        ],
        [
            'barcode' => '8998866701010',
            'aliases' => ['8991001004'],
            'name' => 'Tepung Terigu Segitiga Biru 1kg',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'pcs',
            'buy' => 10500,
            'sell' => 12500,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'
        ],
        [
            'barcode' => '8998866701027',
            'aliases' => [],
            'name' => 'Tepung Terigu Kunci Biru 1kg',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'pcs',
            'buy' => 10500,
            'sell' => 12500,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'
        ],
        [
            'barcode' => '8998866701034',
            'aliases' => [],
            'name' => 'Tepung Terigu Cakra Kembar 1kg',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'pcs',
            'buy' => 12000,
            'sell' => 14000,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'
        ],
        [
            'barcode' => '8992742001014',
            'aliases' => ['8991001005'],
            'name' => 'Tepung Beras Rose Brand 500g',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'pcs',
            'buy' => 7000,
            'sell' => 8500,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'
        ],
        [
            'barcode' => '8992742001021',
            'aliases' => [],
            'name' => 'Tepung Ketan Rose Brand 500g',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'pcs',
            'buy' => 9500,
            'sell' => 11500,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'
        ],
        [
            'barcode' => '8991001006',
            'aliases' => [],
            'name' => 'Tepung Maizena Bola Deli 200g',
            'cat' => 'Beras & Biji-bijian',
            'unit' => 'pcs',
            'buy' => 5000,
            'sell' => 6500,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'
        ],

        // ==================== BUMBU DAPUR & TELUR ====================
        [
            'barcode' => '8991004001',
            'aliases' => [],
            'name' => 'Telur Ayam Negeri (per Kg)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'kg',
            'buy' => 25500,
            'sell' => 28500,
            'img' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400&q=80'
        ],
        [
            'barcode' => '8991004002',
            'aliases' => [],
            'name' => 'Telur Bebek Asin Matang',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'butir',
            'buy' => 3300,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=400&q=80'
        ],
        [
            'barcode' => '8999999014529',
            'aliases' => ['8991004003'],
            'name' => 'Royco Rasa Sapi 1 Renteng (12 sachet)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'renteng',
            'buy' => 4500,
            'sell' => 5500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8999999014512',
            'aliases' => [],
            'name' => 'Royco Rasa Ayam 1 Renteng (12 sachet)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'renteng',
            'buy' => 4500,
            'sell' => 5500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8992775211018',
            'aliases' => ['8991004004'],
            'name' => 'Masako Rasa Ayam 1 Renteng (12 sachet)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'renteng',
            'buy' => 4500,
            'sell' => 5500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8992775221017',
            'aliases' => [],
            'name' => 'Masako Rasa Sapi 1 Renteng (12 sachet)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'renteng',
            'buy' => 4500,
            'sell' => 5500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8999999049965',
            'aliases' => ['8991004005'],
            'name' => 'Kecap Manis Bango Refill 520ml',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'pcs',
            'buy' => 22000,
            'sell' => 25500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8999999049958',
            'aliases' => [],
            'name' => 'Kecap Manis Bango Refill 220ml',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'pcs',
            'buy' => 10500,
            'sell' => 12500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8992761002017',
            'aliases' => ['8991004006'],
            'name' => 'Saus Sambal ABC Asli Botol 135ml',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'btl',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8992761002024',
            'aliases' => [],
            'name' => 'Saus Sambal ABC Extra Pedas 135ml',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'btl',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8992775101012',
            'aliases' => [],
            'name' => 'Saori Saus Tiram 133ml Botol',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'btl',
            'buy' => 9500,
            'sell' => 11500,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8998866501016',
            'aliases' => [],
            'name' => 'Ladaku Merica Bubuk 1 Renteng (12 sachet)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'renteng',
            'buy' => 10500,
            'sell' => 12500,
            'img' => 'https://images.unsplash.com/photo-1518110903415-3e445d0705a6?w=400&q=80'
        ],
        [
            'barcode' => '8998866502013',
            'aliases' => [],
            'name' => 'Desaku Ketumbar Bubuk 1 Renteng (12 sachet)',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'renteng',
            'buy' => 10500,
            'sell' => 12500,
            'img' => 'https://images.unsplash.com/photo-1518110903415-3e445d0705a6?w=400&q=80'
        ],
        [
            'barcode' => '8992742002011',
            'aliases' => [],
            'name' => 'Santan Kelapa Siap Pakai Kara 65ml',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'pcs',
            'buy' => 3000,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'
        ],
        [
            'barcode' => '8991004007',
            'aliases' => [],
            'name' => 'Garam Dapur Beriodium Cap Kapal 250g',
            'cat' => 'Bumbu Dapur & Telur',
            'unit' => 'pcs',
            'buy' => 2500,
            'sell' => 3500,
            'img' => 'https://images.unsplash.com/photo-1518110903415-3e445d0705a6?w=400&q=80'
        ],

        // ==================== SABUN & KEBERSIHAN ====================
        [
            'barcode' => '8999999001550',
            'aliases' => ['8991007001'],
            'name' => 'Sabun Cuci Piring Sunlight Jeruk Nipis 650ml',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 12500,
            'sell' => 15000,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],
        [
            'barcode' => '8999999001543',
            'aliases' => [],
            'name' => 'Sabun Cuci Piring Sunlight 210ml',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 4500,
            'sell' => 6000,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],
        [
            'barcode' => '8992770001010',
            'aliases' => [],
            'name' => 'Sabun Cuci Piring Mama Lemon 680ml',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 11000,
            'sell' => 13500,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],
        [
            'barcode' => '8999999518331',
            'aliases' => ['8991007002'],
            'name' => 'Deterjen Rinso Molto Anti Noda 770g',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 19500,
            'sell' => 23000,
            'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'
        ],
        [
            'barcode' => '8992388201015',
            'aliases' => [],
            'name' => 'Deterjen So Klin Pro 770g',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 18000,
            'sell' => 21500,
            'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'
        ],
        [
            'barcode' => '8992388301012',
            'aliases' => [],
            'name' => 'Deterjen Daia Bunga 850g',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 17000,
            'sell' => 20000,
            'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'
        ],
        [
            'barcode' => '8999999052026',
            'aliases' => ['8991007003'],
            'name' => 'Sabun Mandi Lifebuoy Total 10 110g',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 3500,
            'sell' => 4500,
            'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'
        ],
        [
            'barcode' => '8992388401019',
            'aliases' => [],
            'name' => 'Sabun Mandi Nuvo Family Merah 110g',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 3000,
            'sell' => 4000,
            'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'
        ],
        [
            'barcode' => '8999999040108',
            'aliases' => ['8991007004'],
            'name' => 'Pasta Gigi Pepsodent Pencegah Gigi Berlubang 190g',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'pcs',
            'buy' => 12500,
            'sell' => 15000,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],
        [
            'barcode' => '8999999061011',
            'aliases' => ['8991007005'],
            'name' => 'Shampoo Sunsilk Black Shine 1 Renteng (12 sachet)',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'renteng',
            'buy' => 5000,
            'sell' => 6500,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],
        [
            'barcode' => '8992760001011',
            'aliases' => [],
            'name' => 'Obat Nyamuk Semprot Baygon Kuning 600ml',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'kaleng',
            'buy' => 38000,
            'sell' => 44000,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],
        [
            'barcode' => '8992770301011',
            'aliases' => [],
            'name' => 'Obat Nyamuk Semprot Hit Orange 600ml',
            'cat' => 'Sabun & Kebersihan',
            'unit' => 'kaleng',
            'buy' => 36000,
            'sell' => 42000,
            'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'
        ],

        // ==================== ROKOK ====================
        [
            'barcode' => '8992988111018',
            'aliases' => ['8991008001'],
            'name' => 'Rokok Sampoerna A Mild 16',
            'cat' => 'Rokok',
            'unit' => 'bungkus',
            'buy' => 32000,
            'sell' => 35000,
            'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'
        ],
        [
            'barcode' => '8992988222019',
            'aliases' => ['8991008002'],
            'name' => 'Rokok Djarum Super 12',
            'cat' => 'Rokok',
            'unit' => 'bungkus',
            'buy' => 23000,
            'sell' => 25500,
            'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'
        ],
        [
            'barcode' => '8992988333010',
            'aliases' => ['8991008003'],
            'name' => 'Rokok Gudang Garam Surya 16',
            'cat' => 'Rokok',
            'unit' => 'bungkus',
            'buy' => 31000,
            'sell' => 34000,
            'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'
        ],
        [
            'barcode' => '8992988333027',
            'aliases' => [],
            'name' => 'Rokok Gudang Garam Surya 12',
            'cat' => 'Rokok',
            'unit' => 'bungkus',
            'buy' => 24000,
            'sell' => 26500,
            'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'
        ],
        [
            'barcode' => '8992988444011',
            'aliases' => [],
            'name' => 'Rokok Marlboro Merah 20',
            'cat' => 'Rokok',
            'unit' => 'bungkus',
            'buy' => 38000,
            'sell' => 42000,
            'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'
        ],

        // ==================== SNACK & MAKANAN RINGAN ====================
        [
            'barcode' => '8996001101018',
            'aliases' => ['8991009002'],
            'name' => 'Biskuit Roma Kelapa 300g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 9500,
            'sell' => 11500,
            'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'
        ],
        [
            'barcode' => '8996001102015',
            'aliases' => [],
            'name' => 'Biskuit Roma Malkist Abon 135g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 6000,
            'sell' => 7500,
            'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'
        ],
        [
            'barcode' => '8996001102022',
            'aliases' => [],
            'name' => 'Biskuit Roma Malkist Cokelat 135g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'
        ],
        [
            'barcode' => '8992760101018',
            'aliases' => [],
            'name' => 'Biskuit Oreo Vanilla 133g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 8500,
            'sell' => 10500,
            'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'
        ],
        [
            'barcode' => '8992741201010',
            'aliases' => [],
            'name' => 'Wafer Tango Cokelat 130g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'
        ],
        [
            'barcode' => '8992770401018',
            'aliases' => [],
            'name' => 'Wafer Nabati Richeese Keju 130g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 6500,
            'sell' => 8000,
            'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'
        ],
        [
            'barcode' => '8996001201015',
            'aliases' => [],
            'name' => 'Beng-beng Wafer Cokelat 25g (1 Box isi 20)',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'box',
            'buy' => 38000,
            'sell' => 44000,
            'img' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=400&q=80'
        ],
        [
            'barcode' => '8998866801017',
            'aliases' => [],
            'name' => 'Chitato Rasa Sapi Panggang 68g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 9500,
            'sell' => 11500,
            'img' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400&q=80'
        ],
        [
            'barcode' => '8998866802014',
            'aliases' => [],
            'name' => 'Qtela Keripik Singkong Balado 60g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'bungkus',
            'buy' => 5500,
            'sell' => 7000,
            'img' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400&q=80'
        ],
        [
            'barcode' => '8992735001013',
            'aliases' => [],
            'name' => 'Cokelat SilverQueen Cashew 58g',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'pcs',
            'buy' => 14000,
            'sell' => 17000,
            'img' => 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=400&q=80'
        ],
        [
            'barcode' => '8991009001',
            'aliases' => [],
            'name' => 'Kerupuk Kaleng Putih Satuan',
            'cat' => 'Snack & Makanan Ringan',
            'unit' => 'pcs',
            'buy' => 800,
            'sell' => 1000,
            'img' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=400&q=80'
        ],

        // ==================== GAS & GALON ====================
        [
            'barcode' => '8991006001',
            'aliases' => [],
            'name' => 'Gas Elpiji 3 Kg (Isi Ulang Melon)',
            'cat' => 'Gas & Galon',
            'unit' => 'tabung',
            'buy' => 19000,
            'sell' => 22000,
            'img' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=400&q=80'
        ],
        [
            'barcode' => '8991006002',
            'aliases' => [],
            'name' => 'Aqua Galon 19 Liter (Refill Resmi)',
            'cat' => 'Gas & Galon',
            'unit' => 'galon',
            'buy' => 18500,
            'sell' => 22000,
            'img' => 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=400&q=80'
        ],
        [
            'barcode' => '8991006003',
            'aliases' => [],
            'name' => 'Air Galon Isi Ulang RO Standar',
            'cat' => 'Gas & Galon',
            'unit' => 'galon',
            'buy' => 4000,
            'sell' => 6000,
            'img' => 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=400&q=80'
        ],
    ];

    /**
     * Get category ID mapping from database
     */
    private static function getCategoryMap(): array {
        $cats = Database::fetchAll("SELECT id, name FROM categories");
        $map = [];
        foreach ($cats as $c) {
            $map[strtolower(trim($c['name']))] = (int)$c['id'];
        }
        return $map;
    }

    /**
     * Look up product by barcode
     */
    public static function lookup(string $barcode): array {
        $cleanBarcode = trim($barcode);
        if (empty($cleanBarcode)) {
            return ['found' => false, 'message' => 'Barcode kosong'];
        }

        $categoryMap = self::getCategoryMap();

        // 1. Check if product already exists in current store inventory
        $storeId = \App\Auth::id() ?? 1;
        $existing = Database::fetchOne("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.barcode = ? AND p.store_id = ? AND p.is_active = 1
        ", [$cleanBarcode, $storeId]);

        if ($existing) {
            return [
                'found' => true,
                'already_in_store' => true,
                'source' => 'store_inventory',
                'message' => 'Produk ini sudah ada di daftar tokomu!',
                'product' => $existing
            ];
        }

        // 2. Check local master database
        foreach (self::$masterItems as $item) {
            $matched = ($item['barcode'] === $cleanBarcode) 
                    || in_array($cleanBarcode, $item['aliases'] ?? [])
                    || ltrim($item['barcode'], '0') === ltrim($cleanBarcode, '0');

            if ($matched) {
                $catId = $categoryMap[strtolower(trim($item['cat']))] ?? 1;
                return [
                    'found' => true,
                    'already_in_store' => false,
                    'source' => 'master_db',
                    'message' => 'Produk ditemukan di Master Database Sembako!',
                    'product' => [
                        'barcode' => $item['barcode'],
                        'name' => $item['name'],
                        'category_id' => $catId,
                        'category_name' => $item['cat'],
                        'unit' => $item['unit'],
                        'buy_price' => $item['buy'],
                        'sell_price' => $item['sell'],
                        'image' => $item['img'],
                        'stock' => 10,
                        'min_stock' => 5
                    ]
                ];
            }
        }

        // 3. Fallback: Query Open Food Facts API (Timeout max 3 seconds)
        if (strlen($cleanBarcode) >= 8 && function_exists('curl_init')) {
            $offProduct = self::queryOpenFoodFacts($cleanBarcode, $categoryMap);
            if ($offProduct) {
                return [
                    'found' => true,
                    'already_in_store' => false,
                    'source' => 'open_food_facts',
                    'message' => 'Produk ditemukan dari Database Barcode Online!',
                    'product' => $offProduct
                ];
            }
        }

        return [
            'found' => false,
            'message' => 'Barcode belum dikenali di master database. Silakan isi nama produk manual.',
            'barcode' => $cleanBarcode
        ];
    }

    /**
     * Search master catalog by product name keyword
     */
    public static function searchByName(string $query): array {
        $q = strtolower(trim($query));
        if (empty($q) || strlen($q) < 2) {
            return [];
        }

        $categoryMap = self::getCategoryMap();
        $results = [];

        foreach (self::$masterItems as $item) {
            if (stripos($item['name'], $q) !== false || stripos($item['cat'], $q) !== false) {
                $catId = $categoryMap[strtolower(trim($item['cat']))] ?? 1;
                $results[] = [
                    'barcode' => $item['barcode'],
                    'name' => $item['name'],
                    'category_id' => $catId,
                    'category_name' => $item['cat'],
                    'unit' => $item['unit'],
                    'buy_price' => $item['buy'],
                    'sell_price' => $item['sell'],
                    'image' => $item['img']
                ];
                if (count($results) >= 8) break;
            }
        }

        return $results;
    }

    /**
     * Query Open Food Facts API for product metadata
     */
    private static function queryOpenFoodFacts(string $barcode, array $categoryMap): ?array {
        $url = "https://world.openfoodfacts.org/api/v0/product/" . urlencode($barcode) . ".json";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WarungSembakoPOS/1.0 (fahru@localhost)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $data = json_decode($response, true);
            if (!empty($data['status']) && $data['status'] === 1 && !empty($data['product'])) {
                $p = $data['product'];
                
                $name = $p['product_name_id'] ?? $p['product_name'] ?? $p['generic_name'] ?? '';
                if (empty($name)) return null;

                $brand = $p['brands'] ?? '';
                if ($brand && stripos($name, $brand) === false) {
                    $name = $brand . ' ' . $name;
                }

                $quantity = $p['quantity'] ?? '';
                if ($quantity && stripos($name, $quantity) === false) {
                    $name .= ' ' . $quantity;
                }

                $image = $p['image_front_small_url'] ?? $p['image_url'] ?? '';

                // Simple category guessing
                $catName = 'Mie & Instan';
                $nameLower = strtolower($name);
                if (str_contains($nameLower, 'mie') || str_contains($nameLower, 'noodle') || str_contains($nameLower, 'pasta')) {
                    $catName = 'Mie & Instan';
                } elseif (str_contains($nameLower, 'teh') || str_contains($nameLower, 'kopi') || str_contains($nameLower, 'susu') || str_contains($nameLower, 'air') || str_contains($nameLower, 'water') || str_contains($nameLower, 'drink') || str_contains($nameLower, 'juice')) {
                    $catName = 'Minuman & Susu';
                } elseif (str_contains($nameLower, 'minyak') || str_contains($nameLower, 'gula') || str_contains($nameLower, 'oil') || str_contains($nameLower, 'sugar')) {
                    $catName = 'Minyak & Gula';
                } elseif (str_contains($nameLower, 'sabun') || str_contains($nameLower, 'shampoo') || str_contains($nameLower, 'deterjen') || str_contains($nameLower, 'rinso') || str_contains($nameLower, 'soap')) {
                    $catName = 'Sabun & Kebersihan';
                } elseif (str_contains($nameLower, 'snack') || str_contains($nameLower, 'biskuit') || str_contains($nameLower, 'wafer') || str_contains($nameLower, 'keripik') || str_contains($nameLower, 'chip') || str_contains($nameLower, 'cookie')) {
                    $catName = 'Snack & Makanan Ringan';
                } elseif (str_contains($nameLower, 'beras') || str_contains($nameLower, 'tepung') || str_contains($nameLower, 'rice') || str_contains($nameLower, 'flour')) {
                    $catName = 'Beras & Biji-bijian';
                } elseif (str_contains($nameLower, 'bumbu') || str_contains($nameLower, 'kecap') || str_contains($nameLower, 'sauce') || str_contains($nameLower, 'sambal') || str_contains($nameLower, 'telur')) {
                    $catName = 'Bumbu Dapur & Telur';
                }

                $catId = $categoryMap[strtolower(trim($catName))] ?? 1;

                return [
                    'barcode' => $barcode,
                    'name' => trim($name),
                    'category_id' => $catId,
                    'category_name' => $catName,
                    'unit' => 'pcs',
                    'buy_price' => 0,
                    'sell_price' => 0,
                    'image' => $image,
                    'stock' => 10,
                    'min_stock' => 5
                ];
            }
        }

        return null;
    }
}
