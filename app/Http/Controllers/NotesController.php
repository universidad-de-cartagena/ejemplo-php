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
        // if (!$request->expectsJson()) {
        //     return MessagesUtil::error_message("We only reply JSON");
        // }
        return response()->json(BusinessLogic::listNotes(), 200, [], NotesController::$default_json_encode_options);
    }

    public function show(Request $request, string $uuid)
    {
        return BusinessLogic::getNote($uuid);
    }

    public function create(Request $request)
    {
        if (!$request->isJson()) {
            return MessagesUtil::error_message("Send a json please");
        }
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
            return MessagesUtil::error_message('No note was found with UUID:' . $uuid, 404);
        }
        return response()->json([
            'message' => 'Note with UUID: ' . $uuid . ' has been deleted'
        ], 200);
    }
}
