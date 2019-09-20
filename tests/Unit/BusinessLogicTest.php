<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Note;
use App\Services\BusinessLogic;
use DateTime;
use DateTimeZone;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;

class BusinessLogicTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @doesNotPerformAssertions
     **/
    private function assertNoteContent(array $expected, array $value)
    {
        $this->assertEquals($expected['title'], $value['title']);
        $this->assertEquals($expected['author'], $value['author']);
        $this->assertEquals($expected['body'], $value['body']);
        // Check UUID https://github.com/ramsey/uuid/issues/178#issuecomment-323606470
        // UUID_TYPE_RANDOM = 4
        $this->assertEquals(
            Uuid::fromString($value['uuid'])->getVersion(),
            Uuid::UUID_TYPE_RANDOM
        );
    }

    public function test_empty_array_of_notes()
    {
        /**
         * When starting the program, an empty array should be returned 
         */
        $expected_result = [];
        $result = BusinessLogic::listNotes();
        $this->assertIsArray($result);
        $this->assertEquals($expected_result, $result);
        $this->assertEmpty($result);
    }

    public function test_insert_one_note()
    {
        $expected_result = [
            'title' => 'Super titulo',
            'author' => 'Amaury unit test insert one',
            'body' => 'Unit testing',
            'created_at' => new DateTime('now', new DateTimeZone('UTC'))
        ];
        $inserted_note = BusinessLogic::createNote(
            $expected_result['title'],
            $expected_result['author'],
            $expected_result['body']
        );
        $result = BusinessLogic::listNotes();
        $this->assertIsArray($result);
        $this->assertEquals(sizeof($result), 1);
        $this->assertNoteContent($expected_result, $inserted_note);
    }

    public function test_array_of_2_notes()
    {
        $expected_result = [
            [
                'title' => 'Mi genial nota',
                'author' => 'Amaury Pruebas',
                'body' => 'Unit testing',
                'created_at' => new DateTime('now', new DateTimeZone('utc'))
            ],
            [
                'title' => 'Mi genial nota 2',
                'author' => 'Amaury Pruebas 2',
                'body' => 'Unit testing x2',
                'created_at' => new DateTime('now', new DateTimeZone('utc'))
            ]
        ];
        foreach ($expected_result as $note) {
            $inserted_note = BusinessLogic::createNote(
                $note['title'],
                $note['author'],
                $note['body']
            );
            $this->assertNoteContent($note, $inserted_note);
        }
        $result = BusinessLogic::listNotes();
        $this->assertIsArray($result);
        $this->assertEquals(sizeof($result), 2);
    }

    public function test_delete_note()
    {
        $note_to_insert = [
            'title' => 'Mi genial nota',
            'author' => 'Amaury Pruebas',
            'body' => 'Unit testing'
        ];
        $result = BusinessLogic::createNote(
            $title = $note_to_insert['title'],
            $author = $note_to_insert['author'],
            $body = $note_to_insert['body']
        );
        $this->assertEquals(sizeof(BusinessLogic::listNotes()), 1);

        BusinessLogic::deleteNote($result['uuid']);
        $this->assertIsArray(BusinessLogic::listNotes());
        $this->assertEquals(BusinessLogic::listNotes(), []);

        $this->expectExceptionMessage('deleteNote could not delete the note because ORM could not find a note with uuid: ' . $result['uuid']);
        BusinessLogic::deleteNote($result['uuid']);

        $this->expectExceptionMessage('getNote could not find a note with uuid: ' . $result['uuid']);
        BusinessLogic::getNote($result['uuid']);
    }
}
