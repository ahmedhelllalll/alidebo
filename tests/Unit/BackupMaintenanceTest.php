<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BackupLog;
use App\Http\Controllers\Admin\BackupController;
use App\Imports\BusinessProfilesImport;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class BackupMaintenanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql.database' => 'alidebo_staging']);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Test that no backup tasks remain stuck in in_progress state.
     */
    public function test_no_in_progress_backups_exist()
    {
        $stuckCount = BackupLog::where('status', 'in_progress')->count();
        $this->assertEquals(0, $stuckCount, "There should be 0 in_progress records in backup_logs.");
    }

    /**
     * Test that automated backup schedule is commented out in routes/console.php.
     */
    public function test_automated_backup_schedule_is_disabled()
    {
        $consoleRoutes = file_get_contents(base_path('routes/console.php'));
        $this->assertStringNotContainsString(
            "\nSchedule::command('db:backup')",
            $consoleRoutes,
            "Automated Schedule::command('db:backup') should not be active."
        );
    }

    /**
     * Test that remote image scraping in BusinessProfilesImport is paused by default.
     */
    public function test_image_scraping_is_paused_in_import()
    {
        $import = new BusinessProfilesImport('admin@alidebo.com');
        $reflector = new ReflectionClass($import);
        $method = $reflector->getMethod('downloadAndStoreImage');
        $method->setAccessible(true);

        // Calling with a valid remote URL should immediately return null due to the pause guard
        $result = $method->invoke($import, 'https://example.com/logo.jpg', 'logos', 'Test Company');
        $this->assertNull($result, "Remote image downloads must return null while image scraping is paused.");
    }

    /**
     * Test that BackupController reports isGenerating as false.
     */
    public function test_backup_controller_is_generating_is_false()
    {
        $controller = new BackupController();
        $view = $controller->index();
        $this->assertFalse($view->getData()['isGenerating'], "isGenerating must be false when no active backup is running.");
    }
}
