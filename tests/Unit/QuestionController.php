<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class QuestionController extends TestCase
{
    /** @group index */
    public function testIndexForSuccess()
    {
        Question::factory()->count(3)->create();

        $this->get(route('api.get.questions'))
            ->assertOk()
            ->assertJsonFragment(['total' => 3]);
    }

    /** @group index */
    public function testIndexForSuccessWhenSearching()
    {
        Question::factory()->count(3)->create();

        $question = Question::factory()->create(['question' => 'laravel?'])->question;

        $this->get(route('api.get.questions', ['search' => 'lara']))
            ->assertOk()
            ->assertJsonFragment(['total' => 1, 'question' => $question]);
    }

    /** @group index */
    public function testIndexForSuccessWhenFilteringUsingCategory()
    {
        Question::factory()->count(3)->create();

        $category = Category::factory()->create(['name' => 'Php:Laravel']);

        $question = Question::factory()->create(['question' => 'laravel?', 'category_id' => $category->id])->question;

        $this->get(route('api.get.questions', ['categories' => [$category->id]]))
            ->assertOk()
            ->assertJsonFragment(['total' => 1, 'question' => $question, 'category_id' => $category->id]);
    }

    /** @group index */
    public function testIndexForSuccessWhenFilteringUsingDifficulty()
    {
        $question = Question::factory()->create(['difficulty' => 2]);
        Question::factory()->create(['difficulty' => 0]);

        $this->get(route('api.get.questions', ['difficulty' => 2]))
            ->assertOk()
            ->assertJsonFragment(
                ['total' => 1, 'question' => $question->question, 'difficulty' => 2]
            );
    }

    /** @group store */
    public function testStoreForSuccess()
    {
        $this->getLoggedInUser();

        $categoryId = Category::factory()->create()->id;

        $this->assertCount(0, Question::get());

        $this->postJson(route(
            'api.store.question',
            ['question' => 'question__', 'answer' => '__answer', 'category_id' => $categoryId]
        ))
            ->assertOk()
            ->assertJsonFragment(['message' => __('Question saved successfully')]);

        $this->assertCount(1, Question::get());
    }

    /** @group store */
    public function testStoreForFailureWhenTryingToCreateByNonAuthorizedUser()
    {
        $this->getLoggedInUser('user');

        $categoryId = Category::factory()->create()->id;

        $this->postJson(route(
            'api.store.question',
            ['question' => 'question__', 'answer' => '__answer', 'category_id' => $categoryId]
        ))
            ->assertStatus(400);

        $this->assertCount(0, Question::get());
    }

    /** @group update */
    public function testUpdateForSuccess()
    {
        $this->getLoggedInUser();

        $question = Question::factory()->create();

        $updatedQuestion = Str::random();

        $this->putJson(
            "api/question/update/{$question->id}",
            [
                'question' => $updatedQuestion, 'answer' => $question->answer,
                'category_id' => $question->category_id, 'difficulty' => $question->difficulty
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['message' => __('Question updated successfully')]);
    }

    /** @group update */
    public function testUpdateForFailureWhenTryingToUpdateByNonAuthorizedUser()
    {
        $this->getLoggedInUser('user');

        $userId = User::factory()->create()->id;

        $question = Question::factory()->create(['user_id' => $userId]);

        $updatedQuestion = Str::random();

        $this->putJson(
            "api/question/update/{$question->id}",
            [
                'question' => $updatedQuestion, 'answer' => $question->answer,
                'category_id' => $question->category_id, 'difficulty' => $question->difficulty
            ]
        )
            ->assertStatus(400);
    }

    /** @group destroy */
    public function testDestroyForFailureWhenTryingToDeleteByNonAuthorizedUser()
    {
        $this->getLoggedInUser('user');

        $userId = User::factory()->create()->id;

        $question = Question::factory()->create(['user_id' => $userId]);

        $this->deleteJson("api/question/destroy/{$question->id}")
            ->assertStatus(400);
    }

    /** @group destroy */
    public function testDestroyForSuccess()
    {
        $this->getLoggedInUser();

        $questionId = Question::factory()->create(['user_id' => $this->user->id])->id;

        $this->deleteJson("api/question/destroy/{$questionId}")
            ->assertOk()
            ->assertJsonFragment(['message' => __('Question deleted successfully')]);
    }

    /** @group destroy */
    public function testDestroyForSuccessByAdmin()
    {
        $this->getLoggedInUser();

        $userId = User::factory()->create()->id;

        $questionId = Question::factory()->create(['user_id' => $userId])->id;

        $this->deleteJson("api/question/destroy/{$questionId}")
            ->assertOk()
            ->assertJsonFragment(['message' => __('Question deleted successfully')]);
    }
}
