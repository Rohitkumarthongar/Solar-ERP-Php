<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Product;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Solar Panels',
                'slug' => 'solar-panels',
                'description' => 'High-efficiency monocrystalline and polycrystalline solar panels for residential and commercial use.',
                'icon' => 'fas fa-solar-panel',
                'color' => 'yellow',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Inverters',
                'slug' => 'inverters',
                'description' => 'String inverters, micro-inverters, and hybrid inverters for efficient DC to AC power conversion.',
                'icon' => 'fas fa-bolt',
                'color' => 'blue',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Batteries & Storage',
                'slug' => 'batteries-storage',
                'description' => 'Solar batteries and energy storage systems for backup power and off-grid solutions.',
                'icon' => 'fas fa-battery-full',
                'color' => 'green',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Mounting Structures',
                'slug' => 'mounting-structures',
                'description' => 'Galvanised iron and aluminium mounting structures for rooftop and ground-mounted solar systems.',
                'icon' => 'fas fa-drafting-compass',
                'color' => 'gray',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Cables & Accessories',
                'slug' => 'cables-accessories',
                'description' => 'Solar-grade DC cables, MC4 connectors, junction boxes and installation accessories.',
                'icon' => 'fas fa-plug',
                'color' => 'red',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Monitoring Systems',
                'slug' => 'monitoring-systems',
                'description' => 'Smart monitoring devices and software to track your solar system performance in real-time.',
                'icon' => 'fas fa-chart-line',
                'color' => 'purple',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            ProductCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Assign category_id to existing products by mapping old category enum
        $map = [
            'solar_panel' => ProductCategory::where('slug', 'solar-panels')->first()?->id,
            'inverter'    => ProductCategory::where('slug', 'inverters')->first()?->id,
            'battery'     => ProductCategory::where('slug', 'batteries-storage')->first()?->id,
            'mounting'    => ProductCategory::where('slug', 'mounting-structures')->first()?->id,
            'cable'       => ProductCategory::where('slug', 'cables-accessories')->first()?->id,
            'other'       => null,
        ];

        foreach ($map as $enum => $catId) {
            if ($catId) {
                Product::where('category', $enum)->update(['category_id' => $catId]);
            }
        }
    }
}