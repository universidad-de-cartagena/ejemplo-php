<?php

namespace Tests\Feature;

use App\Services\BusinessLogic;
use Ramsey\Uuid\Uuid;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotesIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_empty_array_of_notes()
    {
        $expected_body = [];
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';

        $response = $this->get('/notes');

        // Status code 200
        $response->assertOk();
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);

        // var_dump($response->json());    
    }

    public function test_insert_note()
    {
        $sent_body = [
            'title' => 'super titulo',
            'body' => 'super mega contenido raro',
            'author' => 'Amaury Ortega'
        ];
        $expected_body = $sent_body;
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';

        $response = $this->postJson('/notes', $sent_body);
        $response->assertOk();
        $response->assertHeader($expected_header, $expected_header_value);

        $response_body = $response->json();
        $this->assertEquals($expected_body['title'], $response_body['title']);
        $this->assertEquals($expected_body['body'], $response_body['body']);
        $this->assertEquals($expected_body['author'], $response_body['author']);
        $this->assertEquals(
            Uuid::fromString($response_body['uuid'])->getVersion(),
            Uuid::UUID_TYPE_RANDOM
        );
    }

    public function test_get_inserted_note()
    {
        $note_to_insert = [
            'title' => 'super titulo',
            'author' => 'Amaury Ortega',
            'body' => 'super mega contenido raro'
        ];
        $expected_body = $inserted_note = BusinessLogic::createNote(
            $note_to_insert['title'],
            $note_to_insert['author'],
            $note_to_insert['body']
        );
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';

        $response = $this->getJson('/notes/' . $inserted_note['uuid']);
        $response->assertOk();
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);
    }

    public function test_delete_note()
    {
        $note_to_insert = [
            'title' => 'super titulo',
            'author' => 'Amaury Ortega',
            'body' => 'super mega contenido raro'
        ];
        $expected_body = $inserted_note = BusinessLogic::createNote(
            $note_to_insert['title'],
            $note_to_insert['author'],
            $note_to_insert['body']
        );
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';
        $expected_status_code = 200;
        $expected_body = [
            'message' => 'Note with UUID: ' . $inserted_note['uuid'] . ' has been deleted'
        ];

        $response = $this->deleteJson('/notes/' . $inserted_note['uuid']);
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);
    }

    public function test_delete_non_existent_note()
    {
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';
        $expected_status_code = 404;
        $random_uuid = Uuid::uuid4();
        $expected_body = [
            'message' => 'No note was found with UUID: ' . $random_uuid
        ];

        $response = $this->deleteJson('/notes/' . $random_uuid);
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);
    }

    public function test_delete_endpoint_without_uuid()
    {
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';
        $expected_status_code = 400;
        $expected_body = [
            'message' => 'Provide the UUID of the note that wants to be deleted'
        ];

        $response = $this->deleteJson('/notes');
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);
    }

    public function test_get_endpoint_with_non_existent_uuid()
    {
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';
        $expected_status_code = 404;
        $random_uuid = Uuid::uuid4();
        $expected_body = [
            'message' => 'No note was found with UUID: ' . $random_uuid
        ];

        $response = $this->getJson('/notes/' . $random_uuid);
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);
    }

    public function test_non_supported_http_method()
    {
        $expected_header = 'Content-Type';
        $expected_header_value = 'application/json';
        $expected_status_code = 405;
        $expected_body = [
            'message' => "Use one of the accepted HTTP methods ['GET', 'POST', 'DELETE']"
        ];

        $response = $this->patchJson('/notes');
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);

        $response = $this->putJson('/notes');
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);

        $response = $this->optionJson('/notes');
        $response->assertStatus($expected_status_code);
        $response->assertHeader($expected_header, $expected_header_value);
        $response->assertExactJson($expected_body);
    }
}
