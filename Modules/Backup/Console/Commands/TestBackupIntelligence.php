<?php

namespace Modules\Backup\Console\Commands;

use Modules\Backup\Services\BackupIntelligenceService;
use Illuminate\Console\Command;

class TestBackupIntelligence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:test-intelligence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the backup intelligence features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Backup Intelligence Features...');
        $this->newLine();

        $service = new BackupIntelligenceService();

        // Test Health Score
        $this->info('1. Backup Health Score:');
        $healthScore = $service->getBackupHealthScore();
        $this->line("   Score: {$healthScore['score']}/100");
        $this->line("   Status: {$healthScore['status']}");

        if (!empty($healthScore['issues'])) {
            $this->line("   Issues:");
            foreach ($healthScore['issues'] as $issue) {
                $this->line("   - {$issue}");
            }
        }
        $this->newLine();

        // Test Recommendations
        $this->info('2. AI Recommendations:');
        $recommendations = $service->getBackupRecommendations();

        if (empty($recommendations)) {
            $this->line("   No recommendations at this time.");
        } else {
            foreach ($recommendations as $index => $recommendation) {
                $this->line("   " . ($index + 1) . ". {$recommendation['title']}");
                $this->line("      Type: {$recommendation['type']}");
                $this->line("      Message: {$recommendation['message']}");
                if (isset($recommendation['action'])) {
                    $this->line("      Action: {$recommendation['action']}");
                }
                $this->newLine();
            }
        }

        // Test Data Change Insights
        $this->info('3. Data Change Insights:');
        $insights = $service->getDataChangeInsights();

        if ($insights['total_changes'] > 0) {
            $this->line("   Total changes since last backup: {$insights['total_changes']}");
            $this->line("   Days since last backup: {$insights['days_since_backup']}");
            $this->line("   Backup recommended: " . ($insights['backup_recommended'] ? 'Yes' : 'No'));
            $this->line("   Reason: {$insights['recommendation_reason']}");

            $this->line("   Changes breakdown:");
            foreach ($insights['changes'] as $type => $change) {
                if ($change['count'] > 0) {
                    $this->line("   - {$change['label']}: {$change['count']}");
                }
            }
        } else {
            $this->line("   " . ($insights['message'] ?? 'No data changes detected'));
        }

        $this->newLine();
        $this->info('Intelligence test completed!');

        return Command::SUCCESS;
    }
}
