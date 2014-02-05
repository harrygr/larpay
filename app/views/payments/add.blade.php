@extends('layouts.master')

@section('content')
<div class="row">
   <div class="col-md-6">
      {{ Form::open(array('url'=>'payments/store', 'class'=>'form-signup')) }}
      <h2 class="form-signup-heading">Add a new payment</h2>

      <ul>
         @foreach($errors->all() as $error)
         <li>{{ $error }}</li>
         @endforeach
      </ul>


      <div class="form-group">
         {{ Form::input('date', 'payment_date', null, array('class'=>'form-control', 'placeholder'=>'Payment Date')) }}
      </div>

      <div class="form-group">
         {{ Form::text('company', null, array('class'=>'form-control', 'placeholder'=>'Company')) }}
      </div>

      <div class="form-group">
         {{ Form::text('item', null, array('class'=>'form-control', 'placeholder'=>'Item')) }}
      </div>

      @foreach ($payers as $payer)
      <div class="form-group">
         {{ Form::input('number', $payer->id . '-amount', null, array('class'=>'form-control', 'placeholder'=> $payer->name)) }}
         {{ Form::checkbox($payer->id . '-pays', null, array('class'=>'form-control')) }}
      </div>
      @endforeach


      {{ Form::submit('Add Payment', array('class'=>'btn btn-primary'))}}
      {{ Form::close() }}
   </div>
</div>
@stop