<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Covoiturage;

class CompleteRide extends Command
{
    protected $signature = 'ride:complete {rideId} {--check : Just check the ride status}';

    protected $description = 'Complete a ride and trigger notifications';

    public function handle()
    {
        $rideId = $this->argument('rideId');

        $ride = Covoiturage::find($rideId);

        if (!$ride) {
            $this->error("Ride {$rideId} not found");
            return 1;
        }

        if ($this->option('check')) {
            $this->info("Ride {$rideId} status:");
            $this->info("  - trip_started: " . ($ride->trip_started ? 'true' : 'false'));
            $this->info("  - trip_completed: " . ($ride->trip_completed ? 'true' : 'false'));
            $this->info("  - cancelled: " . ($ride->cancelled ? 'true' : 'false'));
            $this->info("  - departure: {$ride->departure_date} {$ride->departure_time}");
            return 0;
        }

        $ride->trip_completed = true;
        $ride->save();

        $this->info("Ride {$rideId} completed successfully");
        return 0;
    }
}
