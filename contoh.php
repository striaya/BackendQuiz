public function checkAnswer(Request $request, $lesson_id, $content_id)
{
    // 🔐 401 - Invalid Token
    $user = $request->user();
    if (!$user) {
        return response()->json([
            "status" => "invalid_token",
            "message" => "Invalid or expired token"
        ], 401);
    }

    // 🔒 403 - Forbidden (misalnya hanya student)
    if ($user->role !== 'student') {
        return response()->json([
            "status" => "insufficient_permissions",
            "message" => "Access forbidden"
        ], 403);
    }

    // 🔎 404 - Lesson tidak ada
    $lesson = Lesson::find($lesson_id);
    if (!$lesson) {
        return response()->json([
            "status" => "not_found",
            "message" => "Resource not found"
        ], 404);
    }

    // 🔎 404 - Content tidak ada
    $content = LessonContent::where('id', $content_id)
                ->where('lesson_id', $lesson_id)
                ->first();

    if (!$content) {
        return response()->json([
            "status" => "not_found",
            "message" => "Resource not found"
        ], 404);
    }

    // ❌ 400 - Hanya untuk quiz
    if ($content->type !== 'quiz') {
        return response()->json([
            "status" => "error",
            "message" => "Only for quiz content"
        ], 400);
    }

    // ✅ Validasi option_id
    $request->validate([
        'option_id' => 'required|exists:options,id'
    ]);

    $option = Option::where('id', $request->option_id)
                    ->where('lesson_content_id', $content_id)
                    ->first();

    if (!$option) {
        return response()->json([
            "status" => "not_found",
            "message" => "Resource not found"
        ], 404);
    }

    return response()->json([
        "status" => "success",
        "message" => "Check answer success",
        "data" => [
            "question" => $content->content,
            "user_answer" => $option->option_text,
            "is_correct" => (bool) $option->is_correct
        ]
    ], 200);
}
