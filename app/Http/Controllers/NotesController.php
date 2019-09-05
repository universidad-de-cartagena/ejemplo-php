<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BusinessLogic;
use App\Services\MessagesUtil;
use Throwable;

class NotesController extends Controller
{
    private static $default_json_encode_options = JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK;

    public function index(Request $request)
    {
        return response()->json(BusinessLogic::listNotes(), 200, [], NotesController::$default_json_encode_options);
    }

    public function show(Request $request, string $uuid)
    {
        try {
            return BusinessLogic::getNote($uuid);
        } catch (\Throwable $th) {
            return MessagesUtil::error_message('No note was found with UUID: ' . $uuid, 404);
        }
    }

    public function create(Request $request)
    {
        return BusinessLogic::createNote(
            $request->input('title'),
            $request->input('author'),
            $request->input('body')
        );
    }

    public function delete(Request $request, string $uuid)
    {
        try {
            BusinessLogic::deleteNote($uuid);
        } catch (Throwable $th) {
            return MessagesUtil::error_message('No note was found with UUID: ' . $uuid, 404);
        }
        return response()->json([
            'message' => 'Note with UUID: ' . $uuid . ' has been deleted'
        ], 200);
    }

    public function fallback(Request $request) {
        return MessagesUtil::error_message(
            "Provide the UUID of the note that wants to be deleted",
            400
        );
    }
}
