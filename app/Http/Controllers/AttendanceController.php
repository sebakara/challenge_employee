<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Response;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;



class AttendanceController extends Controller
{
public function recordAttendance(Request $request){

// var_dump(Auth::user());
try{
$user = $request->user();
$checkempl = Employee::where('user_id',$user->id)->first();
if(!$checkempl){
    return response()->json([
        'status'=>true,
        'message'=>'invalid employee',
    ],200);
}
// now check for the attendance
$attendance = Attendance::where('employee_id', $checkempl->id)
->whereDate('created_at', Carbon::today())
->first();

if ($attendance) {
    if($attendance->start_time && $attendance->end_time){
    return response()->json([
            'status'=>false,
            'message'=>"attendance for today is already recorded"
        ],200);
    }
  if ($attendance->start_time) {
       $attendance->end_time = Carbon::now();
       $message = "depart time is recorded successfully";
    }
    $attendance->save();

    // send email
    $data = ["messages"=>$user->first_name." ".$user->last_nam." your signed out ".Carbon::now()];
    $to_name = $user->first_name." ".$user->last_name;
    $to_email = $user->email;
    Mail::send("attendance", $data, function($message) use ($to_name, $to_email) {
    $message->to($to_email, $to_name)
    ->subject("Attendance");
    $message->from(env("MAIL_FROM_ADDRESS"));
    });
}
else
{
$attendance = new Attendance();
$attendance->employee_id = $checkempl->id;
$attendance->start_time = Carbon::now();
$attendance->save();
// send email
$data = ["messages"=>$user->first_name." ".$user->last_nam." your signed in ".Carbon::now()];
$to_name = $user->first_name." ".$user->last_name;
$to_email = $user->email;
Mail::send("attendance", $data, function($message) use ($to_name, $to_email) {
$message->to($to_email, $to_name)
->subject("Attendance");
$message->from(env("MAIL_FROM_ADDRESS"));
});
$message = "start time is recorded successfully";
}
// start_time,end_time'
return response()->json([
    'status'=>false,
    'message'=>$message
],200);

}
catch(\Throwable $th){
return response()->json([
    'status'=>false,
    'message'=>$th->getMessage()
],500);
}


}

// attendance report on a pdf file
public function attendanceReport(Request $request){
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'file_format' => 'required|in:pdf,excel'
    ]);

    $startDate = Carbon::parse($request->input('start_date'));
    $endDate = Carbon::parse($request->input('end_date'));
    if($request->input('file_format') == 'pdf'){
    $attendances = Attendance::join('employees','employees.id','=','attendances.employee_id')
    ->join('users','users.id','=','employees.user_id')
    ->whereBetween('attendances.created_at', [$startDate, $endDate])
    ->select('employees.emp_id', 'users.first_name', 'users.last_name','attendances.start_time','attendances.end_time')
    ->orderBy('attendances.created_at', 'desc')
    ->get();
    // generate the pdf
    $pdffile = PDF::loadView('reports.attendance_report',['attendances'=>$attendances])->output();
    return Response::make($pdffile, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="attendance_report.pdf"'
    ]);
    }
    else if($request->input('file_format') == 'excel'){
        $export = new ReportExport($startDate, $endDate);
        // return Excel::download(new ReportExport($startDate, $endDate), 'attendance.xlsx');
        return Excel::download($export, 'attendance.xlsx');
        // $export->download('attendance.xlsx');
    }
}
}
