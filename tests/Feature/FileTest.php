<?php

namespace Tests\Feature;

use App\Models\File;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileTest extends TestCase {

    public function test_create() {
        $response = $this->requestCreateFakeFile();
        $lastFile = File::all()->last();
        Storage::disk('public')->assertExists("uploads/{$lastFile->filename}");
        $response
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) use ($lastFile) {
                $json->where('id', $lastFile->id)
                    ->where('filename', $lastFile->filename)
                    ->where('user_id', $lastFile->user_id)
                    ->where('type', $lastFile->type)
                    ->where('size', (int)$lastFile->size)
                    ->etc();
            });
    }

    public function test_group() {
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->create('document.pdf', 200, 'application/pdf');
        $response = $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->getToken()}",
                'Content-Type' => 'multipart/form-data; boundary=<calculated when request is sent>'
            ])
            ->post('/api/files/group', ['files' => [$fakeFile, $fakeFile, $fakeFile]]);
        $firstFile = File::all()->first();
        Storage::disk('public')->assertExists("uploads/{$firstFile->filename}");
        $response
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) use ($firstFile) {
                $json->has(3)
                    ->first(function ($json) use ($firstFile) {
                        $json->where('id', $firstFile->id)
                            ->where('filename', $firstFile->filename)
                            ->where('user_id', $firstFile->user_id)
                            ->where('type', $firstFile->type)
                            ->where('size', (int)$firstFile->size)
                            ->etc();
                    });
            });
    }

    public function test_destroy() {
        $responseFileCreated = $this->requestCreateFakeFile();
        $createdFile = File::find($responseFileCreated->json()['id']);
        $response = $this
            ->withHeaders(['Authorization' => "Bearer {$this->getToken()}"])
            ->delete("/api/files/{$createdFile->id}");
        $response
            ->assertStatus(204);
        $this->assertSoftDeleted($createdFile);
        Storage::disk('public')->assertExists("uploads/{$createdFile->filename}");
    }


    public function test_destroy_force_delete() {
        $responseFileCreated = $this->requestCreateFakeFile();
        $createdFile = File::find($responseFileCreated->json()['id']);
        Storage::disk('public')->assertExists("uploads/{$createdFile->filename}");
        $response = $this
            ->withHeaders(['Authorization' => "Bearer {$this->getToken()}"])
            ->delete("/api/files/{$createdFile->id}", ['forceDelete' => 'true']);
        $response
            ->assertStatus(204);
        $this->assertDeleted($createdFile);
        Storage::disk('public')->assertMissing("uploads/{$createdFile->filename}");
    }

    private function requestCreateFakeFile() {
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->create('document.pdf', 200, 'application/pdf');
        return $this
            ->withHeaders([
                'Authorization' => "Bearer {$this->getToken()}",
                'Content-Type' => 'multipart/form-data; boundary=<calculated when request is sent>'
            ])
            ->post('/api/files', ['file' => $fakeFile]);
    }
}
