<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\LovePhoto;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprime y redimensiona las imagenes existentes en la base de datos para ahorrar espacio.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $photos = LovePhoto::all();
        $manager = new ImageManager(new Driver());
        $count = 0;

        $this->info("Iniciando optimización de " . $photos->count() . " fotos...");

        foreach ($photos as $photo) {
            if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
                try {
                    $fileContent = Storage::disk('public')->get($photo->image_path);
                    $image = $manager->read($fileContent);
                    
                    // Solo redimensionar si es mayor a 1920
                    if ($image->width() > 1920 || $image->height() > 1920) {
                        $image->scaleDown(width: 1920, height: 1920);
                        
                        // Guardar con 90% calidad para preservar detalle
                        Storage::disk('public')->put($photo->image_path, $image->toJpeg(90)->toString());
                        $count++;
                        $this->info("Optimizada: " . $photo->image_path);
                    }
                } catch (\Exception $e) {
                    $this->error("Error con la foto {$photo->image_path}: " . $e->getMessage());
                }
            }
        }

        $this->info("¡Completado! Se han optimizado {$count} fotos.");
    }
}
