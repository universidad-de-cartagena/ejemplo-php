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
     * This tells PHPUnit not to care about this function
     * @doesNotPerformAssertions
     **/
    private function get_datetime_from_string(string $date): DateTime
    {
        // Example $date = "2019-08-24UTC09:51:04.000Z"
        $utc_time_zone = new DateTimeZone('UTC');
        $result = new DateTime('now', $utc_time_zone);
        $result->setDate(
            (int) substr($date, 0, 4),
            (int) substr($date, 5, 2),
            (int) substr($date, 8, 2)
        );
        $result->setTime(
            (int) substr($date, 13, 2),
            (int) substr($date, 16, 2),
            (int) substr($date, 19, 2),
            (int) substr($date, 22, 3)
        );
        return $result;
    }

    public function x_empty_array_of_notes()
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
        $this->assertEquals($expected_result['title'], $inserted_note['title']);
        $this->assertEquals($expected_result['author'], $inserted_note['author']);
        $this->assertEquals($expected_result['body'], $inserted_note['body']);
        $this->assertGreaterThanOrEqual(
            $this->get_datetime_from_string($inserted_note['created_at']),
            $expected_result['created_at']
        );
        $this->assertEquals(
            Uuid::fromString($inserted_note['uuid'])->getVersion(),
            Uuid::UUID_TYPE_RANDOM
        );
    }

    /**
     * @doesNotPerformAssertions
     **/
    public function x_array_of_2_notes()
    {
        $utc_time_zone = new DateTimeZone('UTC');
        $expected_result = [
            [
                'title' => 'Mi genial nota',
                'author' => 'Amaury Pruebas',
                'body' => 'Unit testing'
            ],
            [
                'title' => 'Mi genial nota 2',
                'author' => 'Amaury Pruebas 2',
                'body' => 'Unit testing x2'
            ]
        ];
        foreach ($expected_result as $note) {
            $inserted_note = BusinessLogic::createNote(
                $title = $note['title'],
                $author = $note['author'],
                $body = $note['body']
            );
            $this->assertEquals($note['title'], $inserted_note['title']);
            $this->assertEquals($note['author'], $inserted_note['author']);
            $this->assertEquals($note['body'], $inserted_note['body']);
            // Check UUID https://github.com/ramsey/uuid/issues/178#issuecomment-323606470
            // UUID_TYPE_RANDOM = 4
            $this->assertEquals(
                Uuid::fromString($inserted_note['uuid'])->getVersion(),
                Uuid::UUID_TYPE_RANDOM
            );
            $current_time = new DateTime("now", $utc_time_zone);
            $created_at_datetime = $this->get_datetime_from_string($inserted_note['created_at']);
            $this->assertLessThanOrEqual($current_time, $created_at_datetime);
        }
        // var_dump(BusinessLogic::listNotes());
        // $this->assertEquals();
    }
}
