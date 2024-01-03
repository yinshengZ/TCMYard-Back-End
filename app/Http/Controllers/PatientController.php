<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\Allergy;
use App\Models\Symptom;
use App\Models\Gender;
use Carbon\Carbon;
use Exception;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = Patient::with('gender')->with('marital_status')->with('allergy')
            ->symptoms()->allergies()->diseases()->get();

        return response()->json([
            'data'=>$patients,
            'code'=>200
        ]);
    }

    public function get_current_month_new_patients()
    {
        $patients = Patient::whereMonth('created_at', Carbon::now()->month)->with('gender')->get();

        return response()->json([
            'data'=>$patients,
            'code'=>200
        ]);
    }








    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request);

        $this->validate($request, array(
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'gender_id' => 'required|integer',
            'date_of_birth' => 'required|date',
            'postcode' => 'regex:/^[a-zA-Z0-9\s]+$/',
            'telephone' => 'numeric',
            'email' => 'email|unique:patients',
            'marital_status_id' => 'required|integer',
            'occupation' => 'string|nullable',
            'hiv' => 'boolean',
            'past_history' => 'string|nullable',
            'current_issue' => 'string|nullable',
            'allergies' => 'array|nullable',
            'current_medication' => 'array|nullable',
            'symptoms' => 'array|nullable',
            'diseases' => 'array|nullable',
        ));

        $patient = new Patient;
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->gender_id = $request->gender_id;
        $patient->date_of_birth = $request->date_of_birth;
        $patient->date_joined = $request->date_joined;
        $patient->postcode = $request->postcode;
        $patient->telephone = $request->telephone;
        $patient->email = $request->email;
        $patient->marital_status_id = $request->marital_status_id;
        $patient->occupation = $request->occupation;
        $patient->hiv_status = $request->hiv;
        $patient->past_history = $request->past_history;
        $patient->current_issue = $request->current_issue;

        $patient->save();

        $patient->allergies()->sync($request->allergies);
        $patient->symptoms()->sync($request->symptoms);
        $patient->medications()->sync($request->current_medication);
        $patient->diseases()->sync($request->diseases);

        return response()->json([
            'data' => 'Patient has been added successfully!',
            'code' => 200
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patients = Patient::with('gender')->with('marital_status')->with('incomes')->find($id);
        $patients['diseases'] = $patients->diseases;
        $patients['allergies'] = $patients->allergies;
        $patients['medications'] = $patients->medications;
        $patients['symptoms'] = $patients->symptoms;
        return response()->json([
            'data'=>$patients,
            'code'=>200

        ]);
    }

    public function get_patient_info($id)
    {
        $patients = Patient::with('gender')->with('marital_status')->find($id);
        return response()->json([
            'data'=> $patients,
            'code'=>200
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($request->id);

        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->gender_id = $request->gender_id;
        $patient->postcode = $request->postcode;
        $patient->telephone = $request->telephone;
        $patient->email = $request->email;
        $patient->marital_status_id = $request->marital_status_id;
        $patient->occupation = $request->occupation;
        if($request->hiv_status==0){
            $patient->hiv_status=0;
        }
        $patient->date_of_birth = $request->date_of_birth;
        $patient->current_issue = $request->current_issue;
        $patient->past_history = $request->past_history;
        $patient->date_joined = $request->date_joined;

        $patient->diseases()->sync($request->disease_ids);
        $patient->allergies()->sync($request->allergy_ids);
        $patient->symptoms()->sync($request->symptom_ids);
        $patient->medications()->sync($request->medication_ids);

        $patient->save();



        return response()->json([
            'data' => 'Patient has been updated!',
            'code'=>200
        ]);
    }

    /**
     * Remove paitient from database by it's id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $patient = Patient::find($id);
        $patient->delete();

        return response()->json([
            'data' => 'Patient has been deleted successfully!',
            'code' => 200
        ]);
    }



    public function search_patient(Request $query)
    {

        $this->validate($query, array(
            'input' => 'required|max:255',
            'option' => 'required'
        ));

        $input = $query->input;
        $option = $query->option;
        $id=0;
        $patients=[];
        if ($option == "name") {
            $id = Patient::where('first_name', "LIKE", "%{$input}%")
                ->orWhere('last_name', "Like", "%{$input}%")
                ->get()->pluck('id');
        } else if ($option == "email") {
            $id = Patient::where('email', 'LIKE', "%{$input}%")->get()->pluck('id');
        } else if ($option == "gender") {
            $gender_id = Gender::select('id')->where('gender', $input)->value('id');
            $id = Patient::where('gender_id', $gender_id)->get()->pluck('id');
        } else if ($option == "symptom") {
            $data = Symptom::where('symptom', 'LIKE', "%{$input}%")->with('patients')->get();
            $id = $data->pluck('patients')->collapse()->pluck('id');
        } else if ($option == "allergy") {
            $data = Allergy::where('allergies', 'LIKE', "%{$input}%")->with('patients')->get();
            $id = $data->pluck('patients')->collapse()->pluck('id');
        } else if ($option == "date") {
            
            $id = Patient::whereDate('created_at', Carbon::parse($input))->get()->pluck('id');
        }

        $patients = Patient::whereIn('id', $id)->with('gender')->with('marital_status')->orderBy('first_name')->get();

        return response()->json([
            'data'=>$patients,
            'code'=>200
        ]);

        //return $patients;
    }


    public function add_patient_disease(Request $request)
    {
        $patient = Patient::find($request->patient_id);
        $patient->diseases()->attach($request->disease_id);

        return response()->json([
            'data' => 'Patient disease has been added!',
            'code'=>200
        ]);
    }

    public function add_patient_allergy(Request $request)
    {
        $patient = Patient::find($request->patient_id);

        $patient->allergies()->attach($request->allergy_id);

        return response()->json([
            'data' => 'Patient allergy has been added!',
            'code'=>200
        ]);
    }

    public function add_patient_symptom(Request $request)
    {
        $patient = Patient::find($request->patient_id);
        $patient->symptoms()->attach($request->symptom_id);

        return response()->json([
            'data' => 'Patient symptom has been added!',
            'code'=>200
        ]);
    }

    public function add_patient_medication(Request $request)
    {
        $patient = Patient::find($request->patient_id);
        $patient->medications()->attach($request->medication_id);

        return response()->json([
            'data' => 'Patient medication has been added!',
            'code'=>200
        ]);
    }


    public function delete_patient_disease(Request $request)
    {
        $patient = Patient::find($request->patient_id);
        $patient->diseases()->detach($request->disease_id);

        return response()->json([
            'data' => 'Patient disease has been deleted!',
            'code'=>200

        ]);
    }

    public function delete_patient_symptom(Request $request)
    {


        $patient = Patient::find($request->patient_id);

        $patient->symptoms()->detach($request->symptom_id);

        return response()->json([
            'data' => 'Patient symptom has been deleted!',
            'code'=>200
        ]);
    }

    public function delete_patient_medication(Request $request)
    {
        $patient = Patient::find($request->patient_id);
        $patient->medications()->detach($request->medication_id);

        return response()->json([
            'data' => 'Patient medication has been deleted!',
            'code'=>200
        ]);
    }

    public function delete_patient_allergy(Request $request)
    {
        $patient = Patient::find($request->patient_id);
        $patient->allergies()->detach($request->allergy_id);

        return response()->json([
            'data' => 'Patient allergy has been deleted!',
            'code'=>200
        ]);
    }
}
