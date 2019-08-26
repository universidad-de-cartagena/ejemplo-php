<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;

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
        $this->assertEquals($sent_body['title'], $response_body['title']);
        $this->assertEquals($sent_body['body'], $response_body['body']);
        $this->assertEquals($sent_body['author'], $response_body['author']);
        $this->assertEquals(
            Uuid::fromString($response_body['uuid'])->getVersion(),
            Uuid::UUID_TYPE_RANDOM
        );
    }
}
