<?php

class PaymentsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() 
	{
		return View::make('payments.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function add() 
	{
		$payers = Payer::where('user_id', '=', Auth::user()->id)->get();
		return View::make('payments.add')->with('payers', $payers);
	}

	public function addReimbursement() 
	{
		$payers = Payer::where('user_id', '=', Auth::user()->id)->lists('name', 'id');
		return View::make('payments.reimburse')->with('payers', $payers);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() 
	{
		$validator = Validator::make(Input::all(), Payment::$rules);

		if ($validator->passes()) 
		{
			$payers = Payer::where('user_id', '=', Auth::user()->id)->get();
			$has_payer = false;
		    //Prepare the join table data
			foreach ($payers as $payer)
			{
				$sync_data[$payer->id] = [
				'amount'=> Input::has($payer->id . '-amount') ? Input::get($payer->id . '-amount') : 0,
				'pays'  => Input::has($payer->id . '-pays'),
				];
				if ( Input::has($payer->id . '-pays') ) $has_payer = true;
			}
			//Ensure at least one person is a payer
			if (!$has_payer) 
			{
				return Redirect::route('payments.add')
				->with('message', 'At least one person needs to be a payer')
				->with('alert-class', 'alert-danger')
				->withInput();
			}
			
      // validation has passed, save payment in DB
			$payment = new Payment;
			$payment->payment_date = Input::get('payment_date');
			$payment->company = Input::get('company');
			$payment->item = Input::get('item');
			$payment->comment = Input::get('comment');
			$payment->save();

			//Save the amount each payer paid
			$payment->payers()->sync($sync_data);

			Payment::clearPaymentCaches();
			
			return Redirect::route('payments.statement')
			->with('message', 'Payment Added!')
			->with('alert-class', 'alert-success');
		}
      // validation has failed, display error messages 
		return Redirect::route('payments.add')
		->withErrors($validator)->withInput();
	}

	public function storeReimbursement()
	{
		$validator = Validator::make(Input::all(), Payment::$rules);

		if ($validator->passes())
		{

			if ( Input::get('from') === Input::get('to') )
			{
				return Redirect::route('payments.reimburse')
				->with('message', 'A payer cannot reimburse themself')
				->with('alert-class', 'alert-danger');
			}
			if( !Input::get('amount') )
			{
				return Redirect::route('payments.reimburse')
				->with('message', 'Please enter an amount')
				->with('alert-class', 'alert-danger');
			}

			$amount = Input::get('amount');
			$sync_data[Input::get('from')] = [
			'amount' => $amount,
			'pays'	=> 1,
			];
			$sync_data[Input::get('to')] = [
			'amount' => -$amount,
			'pays'	=> 1,
			];

			$from = Payer::find(Input::get('from'));
			$to = Payer::find(Input::get('to'));
			$item = sprintf('%s from %s to %s', $amount, $from->name, $to->name);
			
      // validation has passed, save payment in DB
			$payment = new Payment;
			$payment->payment_date = Input::get('payment_date');
			$payment->company = Input::get('company');
			$payment->item = $item;
			$payment->save();

			//Save the amount each payer paid
			$payment->payers()->sync($sync_data);
			
			Payment::clearPaymentCaches();

			return Redirect::route('payments.statement')
			->with('message', 'Reimbursement Added!')
			->with('alert-class', 'alert-success');
		}
      // validation has failed, display error messages 
		return Redirect::route('payments.reimburse')
		->withErrors($validator)->withInput();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return View::make('payments.show');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete(Payment $payment) 
	{
		PayerPayment::where('payment_id', '=', $payment->id)->delete();
		$payment->delete();
		Payment::clearPaymentCaches();

		return Redirect::route('payments.statement')
		->with('message', 'Payment Deleted')
		->with('alert-class', 'message-success');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Payment $payment) 
	{
		$payers = Payer::where('user_id', '=', Auth::user()->id)->get();
		$payer_payments = PayerPayment::where('payment_id', '=', $payment->id)->get();
		
		//Ensure the currently logged in user is the owner of this payment
		$payment_user = DB::table('payers')
		->select('user_id')
		->join('payer_payment', 'payers.id', '=',  'payer_payment.payer_id')
		->where('payer_payment.payment_id', '=', $payment->id)
		->groupBy('user_id')
		->get();
		if(isset($payment_user[0]))
		{
			// ensure the currently logged in user is alowed to edit this. 
			// There must be a better way to achieve this
			if ($payment_user[0]->user_id !== Auth::user()->id)
			{
				return Redirect::route('payments.statement')
				->with('message', 'You can\'t edit this payment as you\'re not the correct user.')
				->with('alert-class', 'alert-danger');
			}
		}

		$pps = array();
		foreach ($payer_payments as $payer_payment)
		{
			$pps[$payer_payment->payer_id] = array(
				'amount' => $payer_payment->amount,
				'pays'	=> $payer_payment->pays,
				);
		}
		
		return View::make('payments.edit')
		->with('id', $payment->id)
		->with(compact('payers'))
		->with(compact('payment'))
		->with(compact('pps'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($payment) 
	{
		$validator = Validator::make(Input::all(), Payment::$rules);

		if ($validator->passes()) 
		{
		    //Prepare the join table data
			$payers = Payer::where('user_id', '=', Auth::user()->id)->get();
			$has_payer = false;
			foreach ($payers as $payer)
			{
				$sync_data[$payer->id] = array(
					'amount'=> Input::has($payer->id . '-amount') ? Input::get($payer->id . '-amount') : 0,
					'pays'  => Input::has($payer->id . '-pays')
					);
				if ( Input::has($payer->id . '-pays') ) $has_payer = true;
			}
			//Ensure at least one person is a payer
			if (!$has_payer) 
			{
				return Redirect::route('payments.edit', array($payment->id))
				->with('message', 'At least one person needs to be a payer')
				->with('alert-class', 'alert-danger')
				->withInput();
			}

			//Save the base payment data
			$payment->payment_date = Input::get('payment_date');
			$payment->company = Input::get('company');
			$payment->item = Input::get('item');
			$payment->comment = Input::get('comment');
			$payment->save();

			//Save the amount each payer paid
			$payment->payers()->sync($sync_data);
			
			Payment::clearPaymentCaches();

			return Redirect::route('payments.statement')
			->with('message', 'Payment Edited!')
			->with('alert-class', 'alert-success');
		} else {
		    //Validator failed
			return Redirect::route('payments.edit', array($payment->id))
			->with('message', 'Validation Errors Occurred')
			->with('alert-class', 'alert-danger')
			->withErrors($validator)->withInput();
		}

	}

	public function statement() 
	{
		$start_date = Input::get('from', null);
		$end_date = Input::get('to', null);
		$payments = Payment::getPayments($start_date, $end_date);

		$totals = array();
		$settles = array();
		$payers = Payer::where('user_id', '=', Auth::user()->id)->lists('name', 'id');

		if ( $payments ) 
		{
			$payment_data = Payment::process_payment($payments->toArray());
			$totals = Payment::payer_summary(Auth::user()->id);
			$settles = Helper::settleUp($totals);
		} else {
			$payment_data = array();
			//Display a message if there are no payers or payments. Payers need to be added first so we'll show that before.
			if ( !$payers )
			{
				Session::flash('message', 'There are no payers. ' . HTML::linkRoute('payers.add', 'Add one') );
			} else {
				Session::flash('message', 'There are no payments. ' . HTML::linkRoute('payments.add', 'Add one') );
			}
			Session::flash('alert-class', 'alert-info');
		}

		return View::make('payments.statement')
		->with(compact('payment_data'))
		->with(compact('totals'))
		->with(compact('payers'))
		->with(compact('settles'));
	}

	/**
	 * Show a list of payments summarising each
	 */
	public function summary()
	{
		$payments = Payment::getPayments(Input::get('from', null), Input::get('to', null));
		if ( $payments ) 
		{
			$payment_data = Payment::process_payment($payments->toArray());

		} else {
			//Display a message if there are no payers or payments. Payers need to be added first so we'll show that before.
			Session::flash('message', 'There are no payments. ' . HTML::linkRoute('payments.add', 'Add one') );
			Session::flash('alert-class', 'alert-info');
		}
		return View::make('payments.summary')
		->with(compact(['payments']));
	}

}
