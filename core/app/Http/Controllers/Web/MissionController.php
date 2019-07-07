<?php

namespace App\Http\Controllers\Web;

use DataTables;
use App\Models\Mission;
use App\Models\MissionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MissionController extends Controller
{
   	public function showAllEnabledMissionTypes()
    {
        $missionTypes = MissionType::paginate(6);
        return view('admin.other_layouts.missions.all_mission_types_enabled', compact('missionTypes'));   
    }

    public function showAllDisabledMissionTypes()
    {
        $missionTypes = MissionType::onlyTrashed()->paginate(6);
        return view('admin.other_layouts.missions.all_mission_types_disabled', compact('missionTypes'));
    }

    public function submitCreateMissionTypeForm(Request $request)
    {
        $request->validate([
            'mission_type_name'=>'required|unique:mission_types,mission_type_name'
        ]);

        $newMissionType = new MissionType();
        $newMissionType->mission_type_name = ucfirst($request->mission_type_name);
        $newMissionType->save();

        return redirect()->back()->with('success', 'New Mission Type has been Created');
    }

    public function submitMissionTypeEditForm(Request $request, $missionTypeId)
    {
        $missionTypeToUpdate =  MissionType::find($missionTypeId);

        $request->validate([
            'mission_type_name'=>'required|unique:mission_types,mission_type_name,'.$missionTypeToUpdate->id
        ]);

        $missionTypeToUpdate->mission_type_name = ucfirst($request->mission_type_name);
        $missionTypeToUpdate->save();

        return redirect()->back()->with('success', 'Mission Type has been Updated');
    }


    public function missionTypeDeleteMethod($missionTypeId)
    {
        $missionTypeToDelete = MissionType::find($missionTypeId);
        // $missionTypeToDelete->relatedMissions()->delete(); 
        $missionTypeToDelete->delete();

        return redirect()->back()->with('success', 'Mission Type is Deleted');
    }

    public function missionTypeUndoMethod($missionTypeId)
    {
        $missionTypeToUndo = MissionType::withTrashed()->find($missionTypeId);
        // $missionTypeToUndo->relatedMissions()->restore();
        $missionTypeToUndo->restore();

        return redirect()->back()->with('success', 'Mission Type is Restored'); 
    }

    public function showEnabledMissions(Request $request)
    {
        if ($request->ajax()) {
            
            $modal = Mission::with('missionType')->select('missions.*');

            return  DataTables::eloquent($modal)
                    
                    ->addColumn('action', function(){
                        $button = "<i class='fa fa-fw fa-edit tooltip-test' style='transform: scale(1.5);' title='View'></i>";

                        $button .="&nbsp;&nbsp;&nbsp;";

                        $button .= "<i class='fa fa-fw fa-trash tooltip-test' style='transform: scale(1.5);' title='Delete'></i>";

                        return $button;
                    })

                    ->setRowId(function (Mission $mission) {
                        return $mission->id;
                    })

                    ->setRowClass(function (Mission $mission) {
                        return $mission->id % 2 == 0 ? 'alert-success' : 'alert-warning';
                    })

                    ->setRowAttr([
                        'align' => 'center',
                    ])

                    ->make(true);
        }

        return view('admin.other_layouts.missions.all_missions_enabled');

        /*
        $missions = Mission::with('missionType')->paginate(6);
        return view('admin.other_layouts.missions.all_missions_enabled', compact('missions'));
        */
    }

    public function showDisabledMissions()
    {
        $missions = Mission::onlyTrashed()->paginate(6);
        return view('admin.other_layouts.missions.all_missions_disabled', compact('missions'));
    }

    public function submitCreateMissionForm(Request $request)
    {
        $request->validate([
            'mission_type_id'=>'required'
        ]);

        $newMission = Mission::create([
        	'name' => $request->name,
        	'description' => $request->description,
        	'play_number' => $request->play_number,
        	'play_time' => $request->play_time,
        	'damage_opponent' => $request->damage_opponent,
        	'kill_opponent' => $request->kill_opponent,
        	'kill_monster' => $request->kill_monster,
        	'travel_distance' => $request->travel_distance,
        	'win_top_time' => $request->win_top_time,
        	'among_two_time' => $request->among_two_time,
        	'among_three_time' => $request->among_three_time,
        	'among_five_time' => $request->among_five_time,
        	'mission_type_id' => $request->mission_type_id
        ]);

        return redirect()->back()->with('success', 'New Mission has been Created');
    }

    public function submitMissionEditForm(Request $request, $missionId)
    {
        $request->validate([
            'mission_type_id'=>'required'
        ]);

        $missionToUpdate = Mission::findOrFail($missionId);

        $$missionToUpdate = $missionToUpdate->update([
        	'name' => $request->name,
        	'description' => $request->description,
        	'play_number' => $request->play_number,
        	'play_time' => $request->play_time,
        	'damage_opponent' => $request->damage_opponent,
        	'kill_opponent' => $request->kill_opponent,
        	'kill_monster' => $request->kill_monster,
        	'travel_distance' => $request->travel_distance,
        	'win_top_time' => $request->win_top_time,
        	'among_two_time' => $request->among_two_time,
        	'among_three_time' => $request->among_three_time,
        	'among_five_time' => $request->among_five_time,
        	'mission_type_id' => $request->mission_type_id
        ]);

        return redirect()->back()->with('success', 'Mission has been Updated');
    }

    public function missionDeleteMethod($missionId)
    {
        $missionToDelete = Mission::find($missionId);
        $missionToDelete->delete();

        return redirect()->back()->with('success', 'Mission is Deleted');
    }

    public function missionUndoMethod($missionId)
    {      
        $missionToUndo = Mission::withTrashed()->find($missionId);
        $missionToUndo->restore();

        return redirect()->back()->with('success', 'Mission is Restored');
    }
}
