<?php

namespace App\Console\Commands;

use App\Models\Mou;
use App\Models\Notification;
use Illuminate\Console\Command;

class UpdateMouStatus extends Command
{
    protected $signature = 'mou:update-status';
    protected $description = 'Update MoU status and generate expire notifications';

    public function handle(): int
    {
        $this->info('Updating MoU statuses...');

        $updated = 0;

        // Update all MoU statuses
        $mous = Mou::whereNull('deleted_at')->get();

        foreach ($mous as $mou) {
            $oldStatus = $mou->status;
            $mou->updateStatus();

            if ($oldStatus !== $mou->status) {
                $updated++;
            }
        }

        $this->info("Updated {$updated} MoU statuses.");

        // Generate notifications for expiring MoUs
        $this->generateNotifications();

        $this->info('Status update completed.');
        return Command::SUCCESS;
    }

    private function generateNotifications(): void
    {
        // H-90
        $h90 = Mou::where('status', 'aktif')
            ->whereDate('end_date', now()->addDays(90)->format('Y-m-d'))
            ->get();

        foreach ($h90 as $mou) {
            $this->createNotification($mou, 'expire_h90', 'MoU Akan Expire (H-90)', "MoU \"{$mou->title}\" dengan {$mou->institution->name} akan berakhir dalam 90 hari.");
        }

        // H-30
        $h30 = Mou::where('status', 'aktif')
            ->whereDate('end_date', now()->addDays(30)->format('Y-m-d'))
            ->get();

        foreach ($h30 as $mou) {
            $this->createNotification($mou, 'expire_h30', 'MoU Akan Expire (H-30)', "MoU \"{$mou->title}\" dengan {$mou->institution->name} akan berakhir dalam 30 hari.");
        }

        // H-7
        $h7 = Mou::where('status', 'aktif')
            ->whereDate('end_date', now()->addDays(7)->format('Y-m-d'))
            ->get();

        foreach ($h7 as $mou) {
            $this->createNotification($mou, 'expire_h7', 'MoU Segera Expire (H-7)', "URGENT: MoU \"{$mou->title}\" dengan {$mou->institution->name} akan berakhir dalam 7 hari!");
        }

        // Expired today
        $expired = Mou::where('status', 'aktif')
            ->whereDate('end_date', now()->format('Y-m-d'))
            ->get();

        foreach ($expired as $mou) {
            $this->createNotification($mou, 'expired', 'MoU Expired', "MoU \"{$mou->title}\" dengan {$mou->institution->name} telah berakhir hari ini.");
        }
    }

    private function createNotification(Mou $mou, string $type, string $title, string $message): void
    {
        // Avoid duplicate notifications for same MoU and type on same day
        $exists = Notification::where('mou_id', $mou->id)
            ->where('type', $type)
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->exists();

        if (!$exists) {
            Notification::create([
                'mou_id' => $mou->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }
}
