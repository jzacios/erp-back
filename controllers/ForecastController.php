<?php

namespace App\Http\Controllers;

use App\Models\Forecast;
use App\Models\ProjectRates;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\LogsController;


class ForecastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $rates = ProjectRates::where('project', $id)->get();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $forecasts = [];
        foreach($rates as $rate)
        {
            $forecast = Forecast::where('rate_id', $rate->id)->where('month', $month)->where('year', $year)->orderBy('created_at', 'desc')->first();
            if($forecast)
            {
                array_push($forecasts, $forecast);
            }
        }
        $forecasts = collect($forecasts);

        LogsController::addLog(['event' => 'show', 'model' => 'Forecast']);

        return $forecasts;
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rate = ProjectRates::find($request->rate);
        if($forecast = Forecast::where('month', Carbon::now()->month)->where('year', Carbon::now()->year)->where('rate_id', $request->rate)->first()){
            $forecast->value = $request->value;
            $forecast->forecast = $rate->rate*$request->value;
            $forecast->save();
        }else{
            $forecast = new Forecast;
            $forecast->value = $request->value;
            $forecast->project_id = $request->project;
            $forecast->rate_id = $request->rate;
            $forecast->month = Carbon::now()->month;
            $forecast->year = Carbon::now()->year;
            $forecast->forecast = $rate->rate*$request->value;
            $forecast->save();
        }
        

        $rates = ProjectRates::where('project', $rate->project)->get();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $forecasts = [];
        foreach($rates as $rate)
        {
            $forecast = Forecast::where('rate_id', $rate->id)->where('month', $month)->where('year', $year)->orderBy('created_at', 'desc')->first();
            if($forecast)
            {
                $project = Project::find($forecast->project_id);
                $company = Company::find($project->company);
                $coordinator = User::where('id',$project->coordinator)->first();
                $forecast->name = $project->name;
                $forecast->company = $company->name;
                $forecast->number = $project->record;
                $forecast->manager = $coordinator->firstname." ".$coordinator->surname;
                array_push($forecasts, $forecast);
            }
        }
        $forecasts = collect($forecasts);

        LogsController::addLog(['event' => 'add', 'model' => 'Forecast', 'element_id' => [$forecast->id]]);

        return $forecasts;
    }

    public function projectForecast()
    {
        $forecasts = Forecast::get();
        foreach($forecasts as $forecast)
        {
            $project = Project::find($forecast->project_id);
            $company = Company::find($project->company);
            $coordinator = User::find($project->coordinator);
            $forecast->name = $project->name;
            $forecast->company = $company->name;
            $forecast->number = $project->record;
            $forecast->manager = $coordinator->firstname." ".$coordinator->lastname;
        }

        LogsController::addLog(['event' => 'show', 'model' => 'Forecast']);

        return $forecasts;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Forecast  $forecast
     * @return \Illuminate\Http\Response
     */
    public function show(Forecast $forecast)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Forecast  $forecast
     * @return \Illuminate\Http\Response
     */
    public function edit(Forecast $forecast)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Forecast  $forecast
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Forecast $forecast)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Forecast  $forecast
     * @return \Illuminate\Http\Response
     */
    public function destroy(Forecast $forecast)
    {
        //
    }
}
