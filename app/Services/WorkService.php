<?php

namespace App\Services;

use App\Models\Work;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class WorkService
{
    public function list(): Collection
    {
        return Work::with('images')->latest()->get();
    }

    public function find(int $id): Work
    {
        return Work::with('images')->findOrFail($id);
    }

    public function create(array $data, array $images = []): Work
    {
        $work = Work::create($data);

        $this->attachImages($work, $images);

        return $work->load('images');
    }

    public function update(int $id, array $data, array $images = []): Work
    {
        $work = Work::findOrFail($id);
        $work->update($data);

        if (!empty($images)) {
            $this->attachImages($work, $images);
        }

        return $work->load('images');
    }

    public function delete(int $id): void
    {
        $work = Work::with('images')->findOrFail($id);

        foreach ($work->images as $image) {
            Storage::disk('s3')->delete($image->path); 
        }

        $work->delete();
    }

    private function attachImages(Work $work, array $images): void
    {
        $currentMax = $work->images()->max('order') ?? -1;

        foreach ($images as $index => $file) {
            $path = $file->store('works', 's3');

            $work->images()->create([
                'path'  => $path,
                'order' => $currentMax + $index + 1,
            ]);
        }
    }
}
