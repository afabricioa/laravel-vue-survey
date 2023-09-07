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
        return new SurveyResource($data);
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
        $survey->update($request->validated());

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
}
