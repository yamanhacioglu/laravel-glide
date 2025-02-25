<?php

namespace LukasMu\Glide\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GlideClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'glide:clear';

    /**
     * The console command description.
     */
    protected $description = 'Remove the cached Glide images';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        File::deleteDirectory(config('glide.cache'));

        // TODO: Also remove cached srcset widths
        // TODO: Add option to delete individual cached images (not all at once)

        $this->info('Removed the cached Glide images');

        return static::SUCCESS;
    }
}
