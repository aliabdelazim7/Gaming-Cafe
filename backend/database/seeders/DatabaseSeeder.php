<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceSession;
use App\Models\InventoryLog;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Fahad Al-Admin',
            'email' => 'admin@gamingcafe.com',
            'password' => Hash::make('password123'),
            'pin_code' => '1234',
            'phone' => '+966500112233',
            'role' => 'admin',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
        ]);

        $manager = User::create([
            'name' => 'Sara Manager',
            'email' => 'manager@gamingcafe.com',
            'password' => Hash::make('password123'),
            'pin_code' => '5678',
            'phone' => '+966551122334',
            'role' => 'manager',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
        ]);

        $staff = User::create([
            'name' => 'Karim Staff',
            'email' => 'staff@gamingcafe.com',
            'password' => Hash::make('password123'),
            'pin_code' => '0000',
            'phone' => '+966542233445',
            'role' => 'staff',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
        ]);

        // 2. Create Active Shift for Karim
        $shift = Shift::create([
            'staff_id' => $staff->id,
            'start_time' => Carbon::now()->subHours(3),
            'status' => 'active',
            'total_before_deductions' => 640.00,
            'total_after_deductions' => 640.00,
            'cash_collected' => 380.00,
            'card_collected' => 260.00,
            'notes' => 'Morning Shift - Weekend Rush',
        ]);

        $staff->update(['shift_id' => $shift->id]);

        // 3. Create Products / Cafe Items
        $productsData = [
            // Hot Drinks
            ['name' => 'Spanish Latte (Hot)', 'name_ar' => 'سبانش لاتيه حار', 'category' => 'hot_drinks', 'price' => 22.00, 'cost_price' => 7.00, 'stock_quantity' => 120, 'reorder_level' => 15],
            ['name' => 'Espresso Double', 'name_ar' => 'دبل إسبريسو', 'category' => 'hot_drinks', 'price' => 15.00, 'cost_price' => 4.00, 'stock_quantity' => 200, 'reorder_level' => 20],
            ['name' => 'Caramel Macchiato', 'name_ar' => 'كراميل ماكياتو', 'category' => 'hot_drinks', 'price' => 24.00, 'cost_price' => 8.00, 'stock_quantity' => 85, 'reorder_level' => 10],
            ['name' => 'Karak Tea Special', 'name_ar' => 'شاي كرك مميز', 'category' => 'hot_drinks', 'price' => 12.00, 'cost_price' => 3.00, 'stock_quantity' => 150, 'reorder_level' => 20],
            ['name' => 'Americano', 'name_ar' => 'أمريكانو', 'category' => 'hot_drinks', 'price' => 16.00, 'cost_price' => 4.50, 'stock_quantity' => 110, 'reorder_level' => 15],

            // Cold Drinks
            ['name' => 'Iced Spanish Latte', 'name_ar' => 'سبانش لاتيه بارد', 'category' => 'cold_drinks', 'price' => 25.00, 'cost_price' => 8.00, 'stock_quantity' => 95, 'reorder_level' => 15],
            ['name' => 'Cold Brew Nitro', 'name_ar' => 'كولد برو نيترو', 'category' => 'cold_drinks', 'price' => 28.00, 'cost_price' => 9.50, 'stock_quantity' => 60, 'reorder_level' => 10],
            ['name' => 'Iced Pistachio Latte', 'name_ar' => 'آيس بستاشيو لاتيه', 'category' => 'cold_drinks', 'price' => 30.00, 'cost_price' => 11.00, 'stock_quantity' => 45, 'reorder_level' => 10],
            ['name' => 'Peach Passion Iced Tea', 'name_ar' => 'آيس تي خوخ وباشن فروت', 'category' => 'cold_drinks', 'price' => 20.00, 'cost_price' => 5.00, 'stock_quantity' => 70, 'reorder_level' => 12],

            // Soft Drinks & Energy
            ['name' => 'Red Bull Energy 250ml', 'name_ar' => 'ريد بول كلاسيك', 'category' => 'soft_drinks', 'price' => 18.00, 'cost_price' => 9.00, 'stock_quantity' => 6, 'reorder_level' => 10], // Low stock warning!
            ['name' => 'Red Bull Watermelon Punch', 'name_ar' => 'ريد بول بطيخ موخيتو', 'category' => 'soft_drinks', 'price' => 26.00, 'cost_price' => 12.00, 'stock_quantity' => 35, 'reorder_level' => 10],
            ['name' => 'Pepsi Diet Can', 'name_ar' => 'بيبسي دايت', 'category' => 'soft_drinks', 'price' => 8.00, 'cost_price' => 2.50, 'stock_quantity' => 80, 'reorder_level' => 15],
            ['name' => 'Sparkling Mineral Water', 'name_ar' => 'مياه غازية فاخرة', 'category' => 'soft_drinks', 'price' => 7.00, 'cost_price' => 2.00, 'stock_quantity' => 100, 'reorder_level' => 20],

            // Snacks & Food
            ['name' => 'Cheesy Gaming Nachos', 'name_ar' => 'ناتشوز بالجبن الساخن والهلابينو', 'category' => 'snacks', 'price' => 32.00, 'cost_price' => 12.00, 'stock_quantity' => 40, 'reorder_level' => 8],
            ['name' => 'Warm Belgian Waffle', 'name_ar' => 'وافل بلجيكي بالنوتيلا', 'category' => 'food', 'price' => 28.00, 'cost_price' => 9.00, 'stock_quantity' => 25, 'reorder_level' => 5],
            ['name' => 'Smoked Turkey Croissant', 'name_ar' => 'كرواسون الديك الرومي المدخن', 'category' => 'food', 'price' => 24.00, 'cost_price' => 8.50, 'stock_quantity' => 18, 'reorder_level' => 5],
            ['name' => 'Double Chocolate Cookie', 'name_ar' => 'كوكيز شوكولاتة مضاعفة', 'category' => 'snacks', 'price' => 14.00, 'cost_price' => 4.00, 'stock_quantity' => 50, 'reorder_level' => 10],
        ];

        $products = [];
        foreach ($productsData as $item) {
            $p = Product::create($item);
            $products[$item['name']] = $p;
            InventoryLog::create([
                'product_id' => $p->id,
                'quantity_change' => $p->stock_quantity,
                'reason' => 'restock',
                'staff_id' => $staff->id,
            ]);
        }

        // 4. Create Gaming Devices / Rooms
        $devices = [
            ['room_name' => 'PlayStation Arena', 'device_name' => 'PS5 Station 01', 'device_type' => 'ps5', 'hourly_rate' => 40.00, 'status' => 'active', 'specs' => 'Sony PS5 + 65" 4K 120Hz + 2 DualSense Controllers'],
            ['room_name' => 'PlayStation Arena', 'device_name' => 'PS5 Station 02', 'device_name_ar' => 'بلايستيشن 2', 'device_type' => 'ps5', 'hourly_rate' => 40.00, 'status' => 'active', 'specs' => 'Sony PS5 + 65" 4K 120Hz + 4 DualSense Controllers'],
            ['room_name' => 'PlayStation Arena', 'device_name' => 'PS5 Station 03', 'device_type' => 'ps5', 'hourly_rate' => 40.00, 'status' => 'available', 'specs' => 'Sony PS5 + 55" 4K HDR + 2 Controllers'],
            ['room_name' => 'PlayStation Arena', 'device_name' => 'PS5 Station 04', 'device_type' => 'ps5', 'hourly_rate' => 40.00, 'status' => 'available', 'specs' => 'Sony PS5 + 55" 4K HDR + 2 Controllers'],
            
            ['room_name' => 'Esports PC Arena', 'device_name' => 'PC Master 01', 'device_type' => 'pc', 'hourly_rate' => 50.00, 'status' => 'active', 'specs' => 'i9-14900K, RTX 4080 Super, 360Hz BenQ ZOWIE, HyperX Peripherals'],
            ['room_name' => 'Esports PC Arena', 'device_name' => 'PC Master 02', 'device_type' => 'pc', 'hourly_rate' => 50.00, 'status' => 'available', 'specs' => 'i9-14900K, RTX 4080 Super, 360Hz BenQ ZOWIE, Razer Peripherals'],
            ['room_name' => 'Esports PC Arena', 'device_name' => 'PC Master 03', 'device_type' => 'pc', 'hourly_rate' => 50.00, 'status' => 'maintenance', 'specs' => 'GPU Driver diagnostic testing'],
            ['room_name' => 'Esports PC Arena', 'device_name' => 'PC Master 04', 'device_type' => 'pc', 'hourly_rate' => 50.00, 'status' => 'available', 'specs' => 'i7-14700K, RTX 4070Ti, 240Hz ASUS ROG'],

            ['room_name' => 'VIP Cyber Suite', 'device_name' => 'VIP PlayStation Lounge', 'device_type' => 'ps5', 'hourly_rate' => 90.00, 'status' => 'active', 'specs' => 'Private Soundproof Room + 85" LG G3 OLED + 5.1 Surround + Reclining Couches'],
            ['room_name' => 'Sim Racing Bay', 'device_name' => 'F1 Motion Simulator Pro', 'device_type' => 'sim', 'hourly_rate' => 120.00, 'status' => 'available', 'specs' => 'Fanatec DD2 Wheelbase, Hydraulic Pedals, Triple Curved 32" 165Hz Monitors, VR Ready'],
        ];

        $devModels = [];
        foreach ($devices as $d) {
            $devModels[$d['device_name']] = Device::create($d);
        }

        // 5. Create Active Sessions with Timers
        // Session 1: PS5 Station 01 (Started 45 mins ago, 60 mins duration -> 15 mins remaining)
        $s1Start = Carbon::now()->subMinutes(45);
        $s1End = (clone $s1Start)->addMinutes(60);
        $session1 = DeviceSession::create([
            'device_id' => $devModels['PS5 Station 01']->id,
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'customer_name' => 'Ziyad & Friends',
            'customer_phone' => '+966551234567',
            'start_time' => $s1Start,
            'end_time' => $s1End,
            'duration_minutes' => 60,
            'status' => 'active',
            'hourly_rate' => 40.00,
            'session_cost' => 40.00,
            'beverage_cost' => 47.00,
            'total_amount' => 87.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        // Session 2: PS5 Station 02 (Ending in 7 minutes! -> Yellow Warning)
        $s2Start = Carbon::now()->subMinutes(53);
        $s2End = (clone $s2Start)->addMinutes(60);
        $session2 = DeviceSession::create([
            'device_id' => $devModels['PS5 Station 02']->id,
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'customer_name' => 'Ahmed Tariq',
            'customer_phone' => '+966567890123',
            'start_time' => $s2Start,
            'end_time' => $s2End,
            'duration_minutes' => 60,
            'status' => 'active',
            'hourly_rate' => 40.00,
            'session_cost' => 40.00,
            'beverage_cost' => 18.00,
            'total_amount' => 58.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        // Session 3: PC Master 01 (Started 20 mins ago, 120 mins duration)
        $s3Start = Carbon::now()->subMinutes(20);
        $s3End = (clone $s3Start)->addMinutes(120);
        $session3 = DeviceSession::create([
            'device_id' => $devModels['PC Master 01']->id,
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'customer_name' => 'Faisal (Valorant)',
            'customer_phone' => '+966540987654',
            'start_time' => $s3Start,
            'end_time' => $s3End,
            'duration_minutes' => 120,
            'status' => 'active',
            'hourly_rate' => 50.00,
            'session_cost' => 100.00,
            'beverage_cost' => 54.00,
            'total_amount' => 154.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
        ]);

        // Session 4: VIP Lounge (Private booking, 180 mins)
        $s4Start = Carbon::now()->subMinutes(80);
        $s4End = (clone $s4Start)->addMinutes(180);
        $session4 = DeviceSession::create([
            'device_id' => $devModels['VIP PlayStation Lounge']->id,
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'customer_name' => 'Saud Party',
            'customer_phone' => '+966533344455',
            'start_time' => $s4Start,
            'end_time' => $s4End,
            'duration_minutes' => 180,
            'status' => 'active',
            'hourly_rate' => 90.00,
            'session_cost' => 270.00,
            'beverage_cost' => 110.00,
            'total_amount' => 380.00,
            'paid_amount' => 100.00,
            'payment_status' => 'partially_paid',
        ]);

        // 6. Create Tables
        $tablesData = [
            ['table_number' => 'T-01', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'T-02', 'capacity' => 4, 'status' => 'occupied'],
            ['table_number' => 'T-03', 'capacity' => 4, 'status' => 'occupied'],
            ['table_number' => 'T-04', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => 'T-05', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => 'T-06', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => 'T-07', 'capacity' => 6, 'status' => 'occupied'],
            ['table_number' => 'T-08', 'capacity' => 8, 'status' => 'available'],
        ];

        $tables = [];
        foreach ($tablesData as $t) {
            $tables[$t['table_number']] = Table::create($t);
        }

        // Table 2 Order
        $orderT2 = Order::create([
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'status' => 'completed',
            'order_type' => 'dine_in',
            'table_id' => $tables['T-02']->id,
            'subtotal' => 72.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total_amount' => 72.00,
            'payment_status' => 'unpaid',
            'notes' => 'Extra ice for Spanish Latte',
        ]);
        OrderItem::create(['order_id' => $orderT2->id, 'product_id' => $products['Iced Spanish Latte']->id, 'quantity' => 2, 'unit_price' => 25.00, 'subtotal' => 50.00]);
        OrderItem::create(['order_id' => $orderT2->id, 'product_id' => $products['Spanish Latte (Hot)']->id, 'quantity' => 1, 'unit_price' => 22.00, 'subtotal' => 22.00]);
        $tables['T-02']->update(['current_order_id' => $orderT2->id, 'total_spent' => 72.00]);

        // Table 3 Order
        $orderT3 = Order::create([
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'status' => 'completed',
            'order_type' => 'dine_in',
            'table_id' => $tables['T-03']->id,
            'subtotal' => 60.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total_amount' => 60.00,
            'payment_status' => 'unpaid',
            'notes' => 'Table by the window',
        ]);
        OrderItem::create(['order_id' => $orderT3->id, 'product_id' => $products['Cheesy Gaming Nachos']->id, 'quantity' => 1, 'unit_price' => 32.00, 'subtotal' => 32.00]);
        OrderItem::create(['order_id' => $orderT3->id, 'product_id' => $products['Warm Belgian Waffle']->id, 'quantity' => 1, 'unit_price' => 28.00, 'subtotal' => 28.00]);
        $tables['T-03']->update(['current_order_id' => $orderT3->id, 'total_spent' => 60.00]);

        // Table 7 Order
        $orderT7 = Order::create([
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'status' => 'completed',
            'order_type' => 'dine_in',
            'table_id' => $tables['T-07']->id,
            'subtotal' => 108.00,
            'discount' => 8.00,
            'tax' => 0.00,
            'total_amount' => 100.00,
            'payment_status' => 'unpaid',
        ]);
        OrderItem::create(['order_id' => $orderT7->id, 'product_id' => $products['Red Bull Watermelon Punch']->id, 'quantity' => 3, 'unit_price' => 26.00, 'subtotal' => 78.00]);
        OrderItem::create(['order_id' => $orderT7->id, 'product_id' => $products['Double Chocolate Cookie']->id, 'quantity' => 2, 'unit_price' => 14.00, 'subtotal' => 28.00]);
        $tables['T-07']->update(['current_order_id' => $orderT7->id, 'total_spent' => 100.00]);

        // Orders linked to gaming sessions
        $orderS1 = Order::create([
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
            'shift_id' => $shift->id,
            'staff_id' => $staff->id,
            'status' => 'completed',
            'order_type' => 'gaming_room',
            'device_session_id' => $session1->id,
            'subtotal' => 47.00,
            'total_amount' => 47.00,
            'payment_status' => 'unpaid',
        ]);
        OrderItem::create(['order_id' => $orderS1->id, 'product_id' => $products['Iced Spanish Latte']->id, 'quantity' => 1, 'unit_price' => 25.00, 'subtotal' => 25.00]);
        OrderItem::create(['order_id' => $orderS1->id, 'product_id' => $products['Spanish Latte (Hot)']->id, 'quantity' => 1, 'unit_price' => 22.00, 'subtotal' => 22.00]);

        // Notifications
        Notification::create([
            'user_id' => $staff->id,
            'type' => 'session_ending',
            'title' => 'PS5 Station 02 Ending in 7 Minutes',
            'message' => 'Customer Ahmed Tariq session is nearing its 60 min end time. Check for extension.',
            'related_to' => (string)$session2->id,
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $staff->id,
            'type' => 'low_stock',
            'title' => 'Low Stock Alert: Red Bull Energy',
            'message' => 'Only 6 cans remaining in inventory (Reorder level is 10).',
            'related_to' => (string)$products['Red Bull Energy 250ml']->id,
            'is_read' => false,
        ]);
    }
}
