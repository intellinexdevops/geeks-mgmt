<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function select(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id'
        ]);

        $attendance = DB::table('attendance')
            ->where('employee_id', $validated['employee_id'])
            ->get();

        if ($attendance->isEmpty()) {
            return response()->json([
                'msg' => "You have not attended yet."
            ], 404);
        }

        return response()->json([
            'msg' => "Successfully retreived data",
            'data' => $attendance
        ]);
    }

    public function checkin(Request $request)
    {


        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'check_in_time' => 'required'
        ]);

        try {

            DB::beginTransaction();

            $response = DB::table('attendance')
                ->insertGetId([
                    'employee_id' => $validated['employee_id'],
                    'status' => 'Present',
                    'date' => now(),
                    'check_in_time' => $validated['check_in_time'],
                ]);

            DB::table('users')->where('id', $validated['employee_id'])
                ->update(['status' => 'Active']);

            if ($response) {

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'msg' => "Successfully checked-in."
                ], 201);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'msg' => "Error: $th"
            ], 500);
        }
    }
    public function checkout(Request $request)
    {


        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'check_out_time' => 'required'
        ]);

        try {

            DB::beginTransaction();

            $attendance = DB::table('attendance')
                ->where('employee_id', $validated['employee_id'])
                ->where('date', '=', now()->format('Y-m-d'))
                ->first();



            $response = DB::table('attendance')
                ->insertGetId([
                    'employee_id' => $validated['employee_id'],
                    'date' => now(),
                    'check_in_time' => $attendance->check_in_time,
                    'check_out_time' => $validated['check_out_time'],
                ]);

            DB::table('users')->where('id', $validated['employee_id'])
                ->update(['status' => 'Inactive']);

            if ($response) {

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'msg' => "Successfully checked-out."
                ], 201);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'msg' => "Error: $th"
            ], 500);
        }
    }
}