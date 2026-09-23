<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Landlord;
use App\Models\Permission;
use App\Models\Property;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Seeds one user per role plus demo property structure so the RBAC
     * behavior can be exercised immediately.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@propertyke.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'is_super_admin' => true],
        );

        $landlordA = Landlord::firstOrCreate(['email' => 'alice@landlord.test'], [
            'name' => 'Alice Owner', 'business_name' => 'Alice Properties', 'phone' => '+254700000001', 'status' => 'active',
        ]);
        $landlordB = Landlord::firstOrCreate(['email' => 'bob@landlord.test'], [
            'name' => 'Bob Owner', 'business_name' => 'Bob Holdings', 'phone' => '+254700000002', 'status' => 'active',
        ]);

        $alice = User::firstOrCreate(['email' => 'landlord@propmanage.test'], [
            'name' => 'Alice Landlord', 'password' => Hash::make('password'), 'landlord_id' => $landlordA->id,
        ]);
        $manager = User::firstOrCreate(['email' => 'manager@propmanage.test'], [
            'name' => 'Mary Manager', 'password' => Hash::make('password'), 'landlord_id' => $landlordA->id,
        ]);
        $accountant = User::firstOrCreate(['email' => 'accountant@propmanage.test'], [
            'name' => 'Adam Accountant', 'password' => Hash::make('password'), 'landlord_id' => $landlordA->id,
        ]);
        $staff = User::firstOrCreate(['email' => 'staff@propmanage.test'], [
            'name' => 'Sam Staff', 'password' => Hash::make('password'), 'landlord_id' => $landlordA->id,
        ]);
        $bob = User::firstOrCreate(['email' => 'otherlandlord@propmanage.test'], [
            'name' => 'Bob Landlord', 'password' => Hash::make('password'), 'landlord_id' => $landlordB->id,
        ]);

        $roles = Role::pluck('id', 'slug');
        $alice->assignedRoles()->syncWithoutDetaching([$roles[Role::LANDLORD] => ['landlord_id' => $landlordA->id]]);
        $manager->assignedRoles()->syncWithoutDetaching([$roles[Role::PROPERTY_MANAGER] => ['landlord_id' => $landlordA->id]]);
        $accountant->assignedRoles()->syncWithoutDetaching([$roles[Role::ACCOUNTANT] => ['landlord_id' => $landlordA->id]]);
        $staff->assignedRoles()->syncWithoutDetaching([$roles[Role::PROPERTY_STAFF] => ['landlord_id' => $landlordA->id]]);
        $bob->assignedRoles()->syncWithoutDetaching([$roles[Role::LANDLORD] => ['landlord_id' => $landlordB->id]]);

        // Staff access ONLY through explicit grants: view units + view tenants.
        $staff->directPermissions()->sync(
            Permission::whereIn('slug', ['units.view', 'tenants.view'])->pluck('id')
        );

        // Demo hierarchy for landlord A: Property > Block A > Units with individual rents.
        $property = Property::firstOrCreate(
            ['landlord_id' => $landlordA->id, 'property_code' => 'RIVERSIDE'],
            ['name' => 'Riverside Apartments', 'address' => 'Nairobi, Kilimani', 'status' => 'active'],
        );

        $blockA = Block::firstOrCreate(
            ['property_id' => $property->id, 'block_code' => 'A'],
            ['name' => 'Block A', 'status' => 'active'],
        );

        foreach ([['A01', 10000], ['A02', 12000], ['A03', 10000]] as [$number, $rent]) {
            $unit = Unit::withTrashed()->firstOrCreate(
                ['block_id' => $blockA->id, 'unit_number' => $number],
                ['floor' => '1', 'unit_type' => '1BR', 'status' => 'vacant',
                 'current_rent' => $rent, 'deposit_requirement' => $rent],
            );

            if ($unit->rentHistories()->count() === 0) {
                $unit->rentHistories()->create([
                    'rent_amount' => $rent,
                    'effective_from' => now()->startOfYear()->toDateString(),
                    'effective_to' => null,
                    'reason' => 'initial rent',
                ]);
            }
        }

        Tenant::firstOrCreate(
            ['account_number' => 'TNT-0001'],
            [
                'landlord_id' => $landlordA->id,
                'full_name' => 'John Mwangi',
                'phone' => '+254711111111',
                'email' => 'john@example.test',
                'national_id_reference' => '12345678',
                'address' => 'Nairobi',
                'emergency_contact' => '+254722222222',
                'next_of_kin' => 'Jane Mwangi',
                'status' => 'inactive',
                'registered_date' => now()->toDateString(),
            ],
        );
    }
}
