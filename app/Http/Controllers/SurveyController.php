<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user(); //gets user from the request
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate()); //transform result in an array
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request){
        //validates request based on rules from StoreSurveyRequest
        $data = $request->validated();

        $survey = Survey::create($data);
        //create new questions
        foreach($data['questions'] as $question){
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        }

        //returns Survey base on toArray method from SurveyResource
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey, Request $request){
        $user = $request->user();
        if($user->id !== $survey->user_id){
            return abort(403, 'Unauthorized action.');
        }
        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey){
        $data = $request->validated();
        $survey->update($data);

        //get ids of existing questions
        //pluck extracts certain values
        //same as for each and .push in some array a value id
        $existingIds = $survey->questions->pluck('id')->toArray();
        //get id of new questions
        //Arr::pluck retrieve all the values from the array from a given key
        //retrieve all the id values from array questions in data object
        $newIds = Arr::pluck($data['questions'], 'id');
        //find questions to delete
        $toDelete = array_diff($existingIds, $newIds);
        //find questions to add
        $toAdd = array_diff($newIds, $existingIds);

        //delete questions
        SurveyQuestion::destroy($toDelete);

        //create new question
        foreach ($data['questions'] as $question) {
            if(in_array($question['id'], $toAdd)){
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }

        //update existing questions
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach ($survey->questions as $question) {
            //check if $question(from database) exists inside QuestionMap,
            //if yes, then is the questions to update, it means it was not deleted
            if(isset($questionMap[$question->id])){
                $this->updateQuestion($question, $questionMap[$question->id]);
            }
        }

        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Request $request){
        $user = $request->user();

        if($user->id !== $survey->user_id){
            return abort(403, 'Unauthorized action');
        }

        $survey->delete();

        return response('', 204);
    }


    public function createQuestion($data){
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);
            //if data from questions is an array(select, checkbox, radio)
            //then it needs to be saved as a json not as array(not supported)

            $validator = Validator::make($data, [
                'question' => 'required|string',
                'type' => ['required', Rule::in([
                    Survey::TYPE_TEXT,
                    Survey::TYPE_CHECKBOX,
                    Survey::TYPE_SELECT,
                    Survey::TYPE_TEXTAREA,
                    Survey::TYPE_RADIO,
                ])],
                'description' => 'nullable|string',
                'data' => 'present',
                'survey_id' => 'exists:App\Models\Survey,id'
            ]);

            return SurveyQuestion::create($validator->validated());
        }
    }

    private function updateQuestion(SurveyQuestion $question, $data){
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);
            echo $data['data'];

            $validator = Validator::make($data, [
                'id' => 'exists:App\Models\SurveyQuestion,id',
                'question' => 'required|string',
                'type' => ['required', Rule::in([
                    Survey::TYPE_TEXT,
                    Survey::TYPE_CHECKBOX,
                    Survey::TYPE_SELECT,
                    Survey::TYPE_TEXTAREA,
                    Survey::TYPE_RADIO
                ])],
                'description' => 'nullable|string',
                'data' => 'present',
            ]);

            return $question->update($validator->validated());
        }
    }
}
