<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PolicyResource;
use App\Http\Controllers\Controller;
use App\Models\OnlinePayment;   //Live Database
use App\Models\Payment;         //Test Database
use Illuminate\Http\Request;
use Carbon\Carbon;
// use Validator;
use DB;


class PolicyController extends Controller
{
   /* request for policy status and amount check */
   public function info(Request $request)
   {
    $polInfo = $request->all();

    $polNo = $polInfo['policy_no'];

    $sysdate = date('Y-m-d H:i:s');

    $policies = DB::table('POLICY.APPS_POLICY_ALL')
        ->select(DB::raw('POLICY_NO,MATURITY, INSTPREM AS AMOUNT, STATUS AS POLICY_STATUS, PROPOSER, DOB, MOBILE,nextprem'))
        ->where('status', '=', 1)
        ->where('POLICY_NO', '<>', 'PROPOSAL_N')
        ->where('MATURITY', '>', $sysdate) 
        ->where( 'NEXTPREM', '>', Carbon::now()->subDays(30))   
        ->where('NEXTPREM', '>', $sysdate)
        ->whereNotIn('TABLE_ID', [07,102])
        ->where('POLICY_NO', '=', $polNo)
        ->get();        

       if (!($policies)) {
         return response(['message' => 'Policy Number is invaild or lapse. Please contact Fareast Life HO @ +88 09612 666 999 or 16681'], 400);
        }        

       $policies = json_decode( json_encode($policies), true);

       $policy_status = $policies['0']['policy_status'];

        switch ($policy_status) {
        case "1":
            $pol_status ='Inforce';
            $policies['0']['pol_status'] = $pol_status;
            break;
        case "2":
            $pol_status ='Lapse';
            $policies['0']['pol_status'] = $pol_status;
            break;
        case "3":
            $pol_status ='Dorment';
            $policies['0']['pol_status'] = $pol_status;
            break;
        default:
        $pol_status ='Other';
        $policies['0']['pol_status'] = $pol_status;
        }

       $dob = Carbon::parse($policies['0']['dob']);
       $policies['0']['dob'] = $dob->format('d M Y');

       $maturity = Carbon::parse($policies['0']['maturity']);
       $policies['0']['maturity'] = $maturity->format('d M Y');

       $nextprem = Carbon::parse($policies['0']['nextprem']);
       $policies['0']['nextprem'] = $nextprem->format('d M Y');


       return response([ 'policy_info' => PolicyResource::collection($policies), 'message' => 'Retrieved successfully'], 200);
   }

    /**
     * Store a newly created payment from Nagad Billing.
     */

    public function payment(Request $request)
    {            
        $online_payments = $request->all();
        // validation check
        $rules=[
            'payment_ref_id'=>'required',
            'policy_no'=>'required',
            'amount'=>'required',
            'ref_mobile_no'=>'required',
            'status'=>'required'
            
         ];

         $customMessage=[
            'payment_ref_id.required'=>'payment_ref_id is required',
            'policy_no.required'=>'policy_no is required',
            'amount.required'=>'amount is required',
            'ref_mobile_no.required'=>'ref_mobile_no is required',
            'status.required'=>'status is required'
         ];

         $validator=Validator::make($online_payments,$rules,$customMessage);

         if($validator->fails()){
             return response()->json($validator->errors(),422);
         }

        $nagad = new OnlinePayment;
        
        $nagad->payment_ref_id = $online_payments['payment_ref_id'];
        $nagad->policy_no = $online_payments['policy_no'];
        $nagad->amount = $online_payments['amount'];
        $nagad->ref_mobile_no =$online_payments['ref_mobile_no'];
        $nagad->status =$online_payments['status'];
        // MFS requested IP and info;
        $online_payments['client_ip'] =$request->getClientIp();
        $online_payments['client_id'] ='100';
        $online_payments['client_name'] ='Nagad';

        $nagad->client_ip = $online_payments['client_ip'];
        $nagad->client_id = $online_payments['client_id'];
        $nagad->client_name = $online_payments['client_name'];

        $nagad->save();
        $message='Payment Successful';
         
        return response()->json(['status_code'=>200, 'message'=>$message],200);        
    }

    /**
     * Store a newly created payment from Rocket Billing .
     */

    public function payment_rocket(Request $request)
    {    
        $online_payments = $request->all();

        // validation check
        $rules=[
            'payment_ref_id'=>'required',
            'policy_no'=>'required',
            'amount'=>'required',
            'ref_mobile_no'=>'required',
            'status'=>'required'
            
         ];

         $customMessage=[
            'payment_ref_id.required'=>'payment_ref_id is required',
            'policy_no.required'=>'policy_no is required',
            'amount.required'=>'amount is required',
            'ref_mobile_no.required'=>'ref_mobile_no is required',
            'status.required'=>'status is required'

         ];

         $validator=Validator::make($online_payments,$rules,$customMessage);

         if($validator->fails()){
             return response()->json($validator->errors(),422);
         }

        $rocket = new OnlinePayment;
        
        $rocket->payment_ref_id = $online_payments['payment_ref_id'];
        $rocket->policy_no = $online_payments['policy_no'];
        $rocket->amount = $online_payments['amount'];
        $rocket->ref_mobile_no =$online_payments['ref_mobile_no'];
        $rocket->status =$online_payments['status'];
        // MFS requested IP and info;
        $online_payments['client_ip'] =$request->getClientIp();
        $online_payments['client_id'] ='200';
        $online_payments['client_name'] ='Rocket';

        $rocket->client_ip = $online_payments['client_ip'];
        $rocket->client_id = $online_payments['client_id'];
        $rocket->client_name = $online_payments['client_name'];


        $rocket->save();        
        $message='Payment Successful';
         
        return response()->json(['status_code'=>200, 'message'=>$message],200);        
    }

        /**
     * Store a newly created payment from Exim Bank Billing .
     */

     public function payment_eximbank(Request $request)
     {    
         $online_payments = $request->all();
 
         // validation check
         $rules=[
             'payment_ref_id'=>'required',
             'policy_no'=>'required',
             'amount'=>'required',
             'ref_mobile_no'=>'required',
             'status'=>'required'
             
          ];
 
          $customMessage=[
             'payment_ref_id.required'=>'payment_ref_id is required',
             'policy_no.required'=>'policy_no is required',
             'amount.required'=>'amount is required',
             'ref_mobile_no.required'=>'ref_mobile_no is required',
             'status.required'=>'status is required'
 
          ];
 
          $validator=Validator::make($online_payments,$rules,$customMessage);
 
          if($validator->fails()){
              return response()->json($validator->errors(),422);
          }
 
         $exim = new OnlinePayment;
         
         $exim->payment_ref_id = $online_payments['payment_ref_id'];
         $exim->policy_no = $online_payments['policy_no'];
         $exim->amount = $online_payments['amount'];
         $exim->ref_mobile_no =$online_payments['ref_mobile_no'];
         $exim->status =$online_payments['status'];
         // MFS requested IP and info;
         $online_payments['client_ip'] =$request->getClientIp();
         $online_payments['client_id'] ='300';
         $online_payments['client_name'] ='EXIM';
 
         $exim->client_ip = $online_payments['client_ip'];
         $exim->client_id = $online_payments['client_id'];
         $exim->client_name = $online_payments['client_name'];
 
 
         $exim->save();        
         $message='Payment Successful';
          
         return response()->json(['status_code'=>200, 'message'=>$message],200);        
     }

    public function status(Request $request, $ref_id)
    {            
        $payment_status = DB::select('SELECT payment_ref_id, policy_no, amount, ref_mobile_no, client_ip, client_id, client_name, created_at from SHAHIDUL.ONLINE_PAYMENTS WHERE PAYMENT_REF_ID = :payment_ref_id', ['PAYMENT_REF_ID' => $ref_id]);

        if (!($payment_status)) {
            return response(['status_code'=>400, 'message' => 'This payment does not exist'], 400);
         }
         
        $payment_status = json_decode( json_encode($payment_status), true);
        return response([ 'payment_status' => PolicyResource::collection($payment_status), 'message' => 'Retrieved successfully'], 200);
    } 
}
