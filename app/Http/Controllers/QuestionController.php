<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionStoreRequest;
use App\Http\Requests\QuestionUpdateRequest;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $questions = Question::with('category:id,name');

        if ($request->search) {
            $questions->where('question', 'LIKE', "%{$request->search}%");
        }

        if ($request->difficulty) {
            $questions->where('difficulty', $request->difficulty);
        }

        if ($request->categories) {
            $questions->whereHas('category', function ($query) {
                return $query->whereIn('id', request('categories'));
            });
        }

        return $questions
            ->orderBy($request->sortField ?: 'difficulty', $request->sortOrder ?: 'ASC')
            ->paginate($request->limit ?: 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(QuestionStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->authorize('create', Question::class);
            Question::create($request->validated());
            return response()->json(['message' => __('Question saved successfully')]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(QuestionUpdateRequest $request, Question $question): \Illuminate\Http\JsonResponse
    {
        try {
            $this->authorize('update', $question);
            $question->update($request->validated());
            return response()->json(['message' => __('Question updated successfully')]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Question $question): \Illuminate\Http\JsonResponse
    {
        try {
            $this->authorize('delete', $question);
            $question->delete();
            return response()->json(['message' => __('Question deleted successfully')]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
