<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailMarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Email Marketing setup...');
        
        // Run permission seeder
        $this->call(EmailMarketingPermissionsSeeder::class);
        
        // Run template seeder
        $this->call(EmailTemplateSeeder::class);
        
        $this->command->info('✅ Email Marketing setup completed successfully!');
        $this->command->info('');
        $this->command->info('📧 Next steps:');
        $this->command->info('1. Configure your email settings in .env');
        $this->command->info('2. Run: php artisan queue:table && php artisan migrate');
        $this->command->info('3. Start queue worker: php artisan queue:work');
        $this->command->info('4. Assign email marketing permissions to roles');
        $this->command->info('');
    }
}