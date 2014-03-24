<?php

class Payment extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
   'payment_date' => 'required',
   'company' => 'required',
   'item' => 'required'
   );

	public function payers(){
		return $this->belongsToMany('Payer', 'payer_payment')
   ->withPivot('amount', 'pays')
   ->withTimestamps();
 }
 
 /**
  * Clean up the query data and return an array that's easy to output in a view, with payment totals, fair share etc.
  * 
  * @return array
  */ 
 public static function process_payment($payments){

		foreach ($payments as $pmt_id => $payment){

			//get the total for the payment and the number of people included
			$pmt_sum = 0; $pyrs_sum = 0;
			foreach ($payment['payers'] as $payer){
				$pmt_sum += $payer['pivot']['amount'];
				$pyrs_sum += $payer['pivot']['pays'];	
			}

			$fair_share = $pyrs_sum > 0 ? $pmt_sum / $pyrs_sum : 0;
			$payments[$pmt_id]['total'] = $pmt_sum;
			$payments[$pmt_id]['fair_share'] = $fair_share;

			foreach ($payment['payers'] as $pyr_id => $payer){
				$pyr_fs = $fair_share * $payer['pivot']['pays'];
				$payments[$pmt_id]['payers'][$pyr_id]['pivot']['fair_share'] = $pyr_fs;
				$payments[$pmt_id]['payers'][$pyr_id]['pivot']['owes'] = $pyr_fs - $payer['pivot']['amount'];
				$payer_id = $payer['pivot']['payer_id'];
				$payments[$pmt_id]['payers_tmp'][$payer_id] = $payments[$pmt_id]['payers'][$pyr_id];
			}
			$payments[$pmt_id]['payers'] = $payments[$pmt_id]['payers_tmp'];
			unset($payments[$pmt_id]['payers_tmp']);
		}
		return $payments;
	}



 public static function payer_summary($user_id){
  $totals = DB::table('payers')
  ->join('payer_payment', 'payers.id', '=', 'payer_payment.payer_id')
  ->join(DB::raw('(SELECT payment_id,
    SUM(amount) / SUM(pays) AS single_share
    FROM   payer_payment
    GROUP  BY payment_id) AS payments_share'), 'payer_payment.payment_id', '=', 'payments_share.payment_id')
  ->select(DB::raw('
    payers.name,
    payer_payment.payer_id,
    SUM(payer_payment.amount)                             AS total_paid,
    SUM(payer_payment.pays * payments_share.single_share) AS fair_share
    '))                 
  ->where('payers.user_id', '=', Auth::user()->id)
  ->groupBy('payer_payment.payer_id')
  ->get();
  
  $totals_tmp = array();
  if ( $totals ){
    foreach ($totals as $total){
      $totals_tmp[$total->payer_id] = $total;
    } 
  }

  return $totals_tmp;

}
}
