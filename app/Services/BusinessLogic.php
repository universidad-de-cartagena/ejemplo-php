<?php

namespace App\Services;

use App\Note;
use DateTime;
use DateTimeZone;
use Exception;

class BusinessLogic
{
    private static function formatNoteForUi(Note $note): array
    {
        return [
            'uuid' => $note->uuid,
            'title' => $note->title,
            'author' => $note->author,
            'body' => $note->body,
            'created_at' => $note->created_at->format('Y-m-dTH:i:s.v\Z')
        ];
    }
    public static function listNotes(): array
    {
        $notes = Note::all();
        $result = [];
        foreach ($notes as $note) {
            array_push($result, BusinessLogic::formatNoteForUi($note));
        }
        return $result;
    }
    public static function getNote(string $uuid)
    {
        $db_result = Note::where('uuid', $uuid)->first();
        if ($db_result == null) {
            throw new Exception('getNote could not find a note with uuid: ' . $uuid);
        }
        return BusinessLogic::formatNoteForUi($db_result);
    }
    public static function createNote(string $title, string $author, string $body)
    {
        $note = Note::create([
            'title' => $title,
            'author' => $author,
            'body' => $body,
            'created_at' => new DateTime('now', new DateTimeZone('UTC'))
        ]);
        return BusinessLogic::formatNoteForUi($note);
    }
    public static function deleteNote(string $uuid)
    {
        $searched_note = Note::where('uuid', $uuid)->first();
        if (is_null($searched_note)) {
            throw new Exception('deleteNote could not delete the note because ORM could not find a note with uuid: ' . $uuid);
        }
        $searched_note->delete();
    }
}
