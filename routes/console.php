<?php

use Illuminate\Support\Facades\Schedule;

// Run MoU status update daily at 6:00 AM
Schedule::command('mou:update-status')->dailyAt('06:00');
